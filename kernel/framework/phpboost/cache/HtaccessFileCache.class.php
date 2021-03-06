<?php
/*##################################################
 *                           HtaccessFileCache.class.php
 *                            -------------------
 *   begin                : October 22, 2009
 *   copyright            : (C) 2009 Benoit Sautel
 *   email                : ben.popeye@phpboost.com
 *
 *
 ###################################################
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 ###################################################*/

/**
 * This class contains the cache data of the .htaccess file which is located at the root of the site
 * and is used to change the Apache configuration only in the PHPBoost folder.
 * @author Benoit Sautel <ben.popeye@phpboost.com>
 *
 */
class HtaccessFileCache implements CacheData
{
	private $htaccess_file_content = '';
	private $general_config;
	private $server_environment_config;

	/**
	 * {@inheritdoc}
	 */
	public function synchronize()
	{
		$this->htaccess_file_content = '';
		$this->general_config = GeneralConfig::load();
		$this->server_environment_config = ServerEnvironmentConfig::load();
		
		$this->set_default_charset();
		
		$this->add_free_php56();
		
		$this->add_hide_directory_listings();
		
		$this->add_http_headers();
		
		if ($this->server_environment_config->is_url_rewriting_enabled())
		{
			$this->enable_rewrite_rules();
			
			$this->force_redirection_if_available();
			
			$this->add_core_rules();
			$this->add_modules_rules();
			
			$this->add_php_and_http_protections();
			
			$this->add_file_and_sql_injections_protections();
			
			$this->add_bandwidth_protection();
		}
		
		$this->add_error_redirection();
		
		$this->add_gzip_compression();
		
		$this->add_expires_headers();
		
		$this->disable_file_etags();
		
		$this->add_manual_content();
		
		$this->clean_file_content();
	}

	private function add_line($line)
	{
		$this->htaccess_file_content .= "\n" . $line;
	}

	private function add_empty_line()
	{
		$this->add_line('');
	}

	private function add_section($name)
	{
		$this->add_empty_line();
		$this->add_line('# ' . $name . ' #');
	}
	
	private function set_default_charset()
	{
		$this->add_section('Charset');
		$this->add_line('AddDefaultCharset UTF-8');
	}
	
	private function add_free_php56()
	{
		if(AppContext::get_request()->get_domain_name() == 'free.fr')
		{
			$this->add_section('Enable PHP5.6 on ' . $domain . ' hosting');
			$this->add_line('php56 1');
		}
	}
	
	private function add_hide_directory_listings()
	{
		$this->add_section('Hide directory listings');
		$this->add_line('Options -Indexes');
		$this->add_section('Prevent viewing of .htaccess file');
		if (AppContext::get_request()->get_domain_name() == 'free.fr')
		{
			$this->add_line('<Files .htaccess>');
			$this->add_line('    Order Allow,Deny');
			$this->add_line('    Deny from all');
			$this->add_line('</Files>');
		}
		else
		{
			$this->add_line('<Files .htaccess>');
			$this->add_line('    # Apache <= 2.3');
			$this->add_line('    <IfModule mod_authz_core.c>');
			$this->add_line('        Require all denied');
			$this->add_line('    </IfModule>');
			$this->add_line('    # Apache 2.2');
			$this->add_line('    <IfModule !mod_authz_core.c>');
			$this->add_line('        Order Allow,Deny');
			$this->add_line('        Deny from all');
			$this->add_line('    </IfModule>');
			$this->add_line('</Files>');
		}
	}
	
	private function add_http_headers()
	{
		if(AppContext::get_request()->get_domain_name() != 'free.fr')
		{
			$this->add_section('HTTP Headers');
			$this->add_line('<IfModule mod_headers.c>');
			
			if ($this->server_environment_config->is_redirection_https_enabled() && $this->server_environment_config->is_hsts_security_enabled())
			{
				$this->add_line('	# Tell the browser to attempt the HTTPS version first');
				$this->add_line('	Header always set Strict-Transport-Security "max-age=' . $this->server_environment_config->get_hsts_security_duration() . '; ' . ($this->server_environment_config->is_hsts_security_subdomain_enabled() ? 'includeSubDomains;' : '') . '"');
			}
			
			$this->add_line('	# Don\'t allow any pages to be framed externally - Defends against CSRF');
			$this->add_line('	Header set X-Frame-Options SAMEORIGIN');
			$this->add_line('	# Control Cross-Domain Policies');
			$this->add_line('	Header set X-Permitted-Cross-Domain-Policies "master-only"');
			$this->add_line('	# Turn on IE8-IE9 XSS prevention tools');
			$this->add_line('	Header set X-XSS-Protection "1; mode=block"');
			$this->add_line('	# Prevent mime based attacks');
			$this->add_line('	Header always set X-Content-Type-Options "nosniff"');
			$this->add_line('	# Use this to force IE to hide that annoying browser compatibility button in the address bar.');
			$this->add_line('	# IE=edge means IE should use the latest (edge) version of its rendering engine.');
			$this->add_line('	# chrome=1 means IE should use the Chrome rendering engine if installed.');
			$this->add_line('	BrowserMatch MSIE ie');
			$this->add_line('	Header set X-UA-Compatible "IE=Edge"');
			$this->add_line('</IfModule>');
		}
		else
		{
			$this->add_section('HTTP Headers disabled on ' . $domain . ' hosting');
		}
	}

	private function enable_rewrite_rules()
	{
		$this->add_section('Rewrite rules');
		$this->add_line('RewriteEngine on');
		$this->add_line('RewriteBase /');
	}

	private function add_core_rules()
	{
		$this->add_section('Core');

		$this->add_rewrite_rule('^user/pm-?([0-9]+)-?([0-9]{0,})-?([0-9]{0,})-?([0-9]{0,})-?([a-z_]{0,})$', 'user/pm.php?pm=$1&id=$2&p=$3&quote=$4');

		$eps = AppContext::get_extension_provider_service();
		$mappings = $eps->get_extension_point(UrlMappingsExtensionPoint::EXTENSION_POINT);
		$authorized_extension_point = array('kernel', 'user');
		foreach ($mappings as $id => $mapping_list)
		{
			if (in_array($id, $authorized_extension_point))
			{
				$this->add_url_mapping($mapping_list);
			}
		}
	}

	private function add_modules_rules()
	{
		$this->add_section('Modules rules');

		$modules = ModulesManager::get_activated_modules_map();
		$eps = AppContext::get_extension_provider_service();
		foreach ($modules as $module)
		{
			$id = $module->get_id();
			$configuration = $module->get_configuration();
			$rules = $configuration->get_url_rewrite_rules();
			if (!empty($rules))
			{
				$this->add_section($id);
			}
			foreach ($rules as $rule)
			{
				$this->add_line(str_replace('DIR', $this->general_config->get_site_path(), $rule));
			}
			if ($eps->provider_exists($id, UrlMappingsExtensionPoint::EXTENSION_POINT))
			{
				$this->add_section($id);
				$provider = $eps->get_provider($id);
				$url_mappings = $provider->get_extension_point(UrlMappingsExtensionPoint::EXTENSION_POINT);
				$this->add_url_mapping($url_mappings);
			}
		}
	}

	private function add_rewrite_rule($match, $path, $options = 'L,QSA')
	{
		$this->add_line('RewriteRule ' . $match . ' ' . $this->general_config->get_site_path() . '/' . ltrim($path, '/') . ' [' . $options . ']');
	}

	private function add_url_mapping(UrlMappingsExtensionPoint $mapping_list)
	{
		foreach ($mapping_list->list_mappings() as $mapping)
		{
			$this->add_rewrite_rule($mapping->from(), $mapping->to(), $mapping->options());
		}
	}
	
	private function add_php_and_http_protections()
	{
		$this->add_section('PHP and HTTP protections');
		$this->add_line('# Block out use of illegal or unsafe characters in the HTTP Request');
		$this->add_line('RewriteCond %{THE_REQUEST} ^.*(\\r|\\n|%0A|%0D).* [NC,OR]');
		$this->add_empty_line();
		$this->add_line('# Block out any script that includes a <script> tag in URL');
		$this->add_line('RewriteCond %{QUERY_STRING} (<|%3C)([^s]*s)+cript.*(>|%3E) [NC,OR]');
		$this->add_line('# Block out any script trying to set a PHP GLOBALS variable via URL');
		$this->add_line('RewriteCond %{QUERY_STRING} GLOBALS(=|[|\%[0-9A-Z]{0,2}) [OR]');
		$this->add_line('# Block out any script trying to modify a _REQUEST variable via URL');
		$this->add_line('RewriteCond %{QUERY_STRING} _REQUEST(=|[|\%[0-9A-Z]{0,2})');
		$this->add_line('RewriteRule .* - [F,L]');
	}
	
	private function add_file_and_sql_injections_protections()
	{
		$this->add_section('File and SQL injections protections');
		$this->add_line('RewriteCond %{REQUEST_METHOD} GET');
		$this->add_line('RewriteCond %{QUERY_STRING} (;|<|>|\'|"|\)|%0A|%0D|%22|%27|%3C|%3E|%00).*(/\*|union|select|insert|cast|set|declare|drop|update|md5|benchmark) [NC,OR]');
		$this->add_line('RewriteCond %{QUERY_STRING} (<|>|\'|%0A|%0D|%27|%3C|%3E|%00) [NC]');
		$this->add_line('RewriteRule .* - [F,L]');
	}
	
	private function force_redirection_if_available()
	{
		$domain = AppContext::get_request()->get_domain_name();
		
		if ($this->server_environment_config->is_redirection_www_enabled())
		{
			$this->add_section('Site redirection to www');
			$this->add_line('RewriteCond %{HTTP_HOST} ^' . $domain . ' [NC]');
			$this->add_line('RewriteRule ^/?(.*) http' . ($this->server_environment_config->is_redirection_https_enabled() ? 's' : '') . '://' . ($this->server_environment_config->is_redirection_www_mode_with_www() ? 'www.' . $domain : AppContext::get_request()->get_site_domain_name()) . '/$1 [L,R=301]');
		}
		
		if ($this->server_environment_config->is_redirection_https_enabled() && !$this->server_environment_config->is_redirection_www_enabled())
		{
			$this->add_section('Force to use HTTPS if available');
			$this->add_line('RewriteCond %{HTTPS} !=on [OR]'); //check if HTTPS not "on"
			$this->add_line('RewriteCond %{SERVER_PORT} 80 [OR]'); // OR if the server port is 80
			$this->add_line('RewriteCond %{HTTP:X-Forwarded-Proto} !https [NC]'); // OR if the website is behind a load balancer
			$this->add_line('RewriteRule ^/?(.*) https://%{SERVER_NAME}/$1 [R=301,L]');
		}
	}
	
	private function add_bandwidth_protection()
	{
		//Bandwidth protection. The /upload directory can be forbidden if the request comes from out of PHPBoost
		if (FileUploadConfig::load()->get_enable_bandwidth_protect())
		{
			$this->add_section('Bandwith protection');
			$this->add_line('RewriteCond %{HTTP_REFERER} !^$');
			$this->add_line('RewriteCond %{HTTP_REFERER} !^' . $this->general_config->get_site_url());
			$this->add_line('RewriteRule .*upload/.*$ - [F]');
			$this->add_section('Stop hotlinking');
			$this->add_line('RewriteCond %{HTTP_REFERER} !^$');
			$this->add_line('RewriteCond %{HTTP_REFERER} !^' . $this->general_config->get_site_url());
			$this->add_line('RewriteRule \.(bmp|gif|jpe?g|png|swf)$ - [F,L,NC]');
		}
	}

	private function add_error_redirection()
	{
		//Error page
		$this->add_section('Error pages');
		$this->add_line('ErrorDocument 403 ' . $this->general_config->get_site_path() . UserUrlBuilder::error_403()->relative());
		$this->add_line('ErrorDocument 404 ' . $this->general_config->get_site_path() . UserUrlBuilder::error_404()->relative());
	}
	
	private function add_gzip_compression()
	{
		if(AppContext::get_request()->get_domain_name() != 'free.fr')
		{
			$this->add_section('Gzip compression');
			$this->add_line('<IfModule mod_filter.c>');
			$this->add_line('	<IfModule mod_deflate.c>');
			$this->add_line('		# Compress HTML, CSS, JavaScript, Text, XML and fonts');
			$this->add_line('		AddOutputFilterByType DEFLATE application/javascript');
			$this->add_line('		AddOutputFilterByType DEFLATE application/rss+xml');
			$this->add_line('		AddOutputFilterByType DEFLATE application/vnd.ms-fontobject');
			$this->add_line('		AddOutputFilterByType DEFLATE application/x-font');
			$this->add_line('		AddOutputFilterByType DEFLATE application/x-font-opentype');
			$this->add_line('		AddOutputFilterByType DEFLATE application/x-font-otf');
			$this->add_line('		AddOutputFilterByType DEFLATE application/x-font-truetype');
			$this->add_line('		AddOutputFilterByType DEFLATE application/x-font-ttf');
			$this->add_line('		AddOutputFilterByType DEFLATE application/x-javascript');
			$this->add_line('		AddOutputFilterByType DEFLATE application/xhtml+xml');
			$this->add_line('		AddOutputFilterByType DEFLATE application/xml');
			$this->add_line('		AddOutputFilterByType DEFLATE font/opentype');
			$this->add_line('		AddOutputFilterByType DEFLATE font/otf');
			$this->add_line('		AddOutputFilterByType DEFLATE font/ttf');
			$this->add_line('		AddOutputFilterByType DEFLATE image/svg+xml');
			$this->add_line('		AddOutputFilterByType DEFLATE image/x-icon');
			$this->add_line('		AddOutputFilterByType DEFLATE text/css');
			$this->add_line('		AddOutputFilterByType DEFLATE text/html');
			$this->add_line('		AddOutputFilterByType DEFLATE text/javascript');
			$this->add_line('		AddOutputFilterByType DEFLATE text/plain');
			$this->add_line('		AddOutputFilterByType DEFLATE text/xml');
			$this->add_empty_line();
			$this->add_line('		# Remove browser bugs (only needed for really old browsers)');
			$this->add_line('		BrowserMatch ^Mozilla/4 gzip-only-text/html');
			$this->add_line('		BrowserMatch ^Mozilla/4\.0[678] no-gzip');
			$this->add_line('		BrowserMatch \bMSIE !no-gzip !gzip-only-text/html');
			$this->add_line('		<IfModule mod_headers.c>');
			$this->add_line('			Header append Vary User-Agent');
			$this->add_line('		</IfModule>');
			$this->add_line('	</IfModule>');
			$this->add_line('</IfModule>');
		}
		else
		{
			$this->add_section('Gzip compression disabled on ' . $domain . ' hosting');
		}
	}
	
	private function add_expires_headers()
	{
		if(AppContext::get_request()->get_domain_name() != 'free.fr')
		{
			$this->add_section('Expires Headers');
			$this->add_line('<IfModule mod_expires.c>');
			$this->add_line('	ExpiresActive On');
			$this->add_empty_line();
			$this->add_line('	# Default expiration: 1 week after request');
			$this->add_line('	ExpiresDefault "access plus 1 week"');
			$this->add_empty_line();
			$this->add_line('	# CSS and JS expiration: 1 week after request');
			$this->add_line('	ExpiresByType text/css "access plus 1 week"');
			$this->add_line('	ExpiresByType text/javascript "access plus 1 week"');
			$this->add_line('	ExpiresByType text/x-javascript "access plus 1 week"');
			$this->add_line('	ExpiresByType application/javascript "access plus 1 week"');
			$this->add_line('	ExpiresByType application/x-javascript "access plus 1 week"');
			$this->add_empty_line();
			$this->add_line('	# Fonts expiration: 1 week after request');
			$this->add_line('	<IfModule mod_mime.c>');
			$this->add_line('		AddType application/font-woff .woff');
			$this->add_line('		AddType application/font-woff2 .woff2');
			$this->add_line('	</IfModule>');
			$this->add_line('	ExpiresByType   application/font-woff   "access plus 1 month"');
			$this->add_line('	ExpiresByType   application/font-woff2   "access plus 1 month"');
			$this->add_empty_line();
			$this->add_line('	# Image files expiration: 1 month after request');
			$this->add_line('	ExpiresByType image/bmp "access plus 1 month"');
			$this->add_line('	ExpiresByType image/gif "access plus 1 month"');
			$this->add_line('	ExpiresByType image/jpeg "access plus 1 month"');
			$this->add_line('	ExpiresByType image/jp2 "access plus 1 month"');
			$this->add_line('	ExpiresByType image/pipeg "access plus 1 month"');
			$this->add_line('	ExpiresByType image/png "access plus 1 month"');
			$this->add_line('	ExpiresByType image/svg+xml "access plus 1 month"');
			$this->add_line('	ExpiresByType image/tiff "access plus 1 month"');
			$this->add_line('	ExpiresByType image/vnd.microsoft.icon "access plus 1 month"');
			$this->add_line('	ExpiresByType image/x-icon "access plus 1 month"');
			$this->add_line('	ExpiresByType image/ico "access plus 1 month"');
			$this->add_line('	ExpiresByType image/icon "access plus 1 month"');
			$this->add_line('	ExpiresByType text/ico "access plus 1 month"');
			$this->add_line('	ExpiresByType application/ico "access plus 1 month"');
			$this->add_line('	ExpiresByType image/vnd.wap.wbmp "access plus 1 month"');
			$this->add_line('	ExpiresByType application/vnd.wap.wbxml "access plus 1 month"');
			$this->add_line('	ExpiresByType application/smil "access plus 1 month"');
			$this->add_empty_line();
			$this->add_line('	# Audio files expiration: 1 month after request');
			$this->add_line('	ExpiresByType audio/basic "access plus 1 month"');
			$this->add_line('	ExpiresByType audio/mid "access plus 1 month"');
			$this->add_line('	ExpiresByType audio/midi "access plus 1 month"');
			$this->add_line('	ExpiresByType audio/mpeg "access plus 1 month"');
			$this->add_line('	ExpiresByType audio/x-aiff "access plus 1 month"');
			$this->add_line('	ExpiresByType audio/x-mpegurl "access plus 1 month"');
			$this->add_line('	ExpiresByType audio/x-pn-realaudio "access plus 1 month"');
			$this->add_line('	ExpiresByType audio/x-wav "access plus 1 month"');
			$this->add_empty_line();
			$this->add_line('	# Movie files expiration: 1 month after request');
			$this->add_line('	ExpiresByType application/x-shockwave-flash "access plus 1 month"');
			$this->add_line('	ExpiresByType x-world/x-vrml "access plus 1 month"');
			$this->add_line('	ExpiresByType video/x-msvideo "access plus 1 month"');
			$this->add_line('	ExpiresByType video/mpeg "access plus 1 month"');
			$this->add_line('	ExpiresByType video/mp4 "access plus 1 month"');
			$this->add_line('	ExpiresByType video/quicktime "access plus 1 month"');
			$this->add_line('	ExpiresByType video/x-la-asf "access plus 1 month"');
			$this->add_line('	ExpiresByType video/x-ms-asf "access plus 1 month"');
			$this->add_line('</IfModule>');
		}
		else
		{
			$this->add_section('Expires Headers disabled on ' . $domain . ' hosting');
		}
	}
	
	private function disable_file_etags()
	{
		if(AppContext::get_request()->get_domain_name() != 'free.fr')
		{
			$this->add_section('Disable file etags');
			$this->add_line('FileETag none');
		}
		else
		{
			$this->add_section('Disable file etags disabled on ' . $domain . ' hosting');
		}
	}

	private function add_manual_content()
	{
		$manual_content = $this->server_environment_config->get_htaccess_manual_content();
		if (!empty($manual_content))
		{
			$this->add_section('Manual content');
			$this->add_line($manual_content);
		}
	}

	private function clean_file_content()
	{
		$this->htaccess_file_content = trim($this->htaccess_file_content);
	}

	/**
	 * Returns the content of the .htaccess file
	 * @return string its content
	 */
	public function get_htaccess_file_content()
	{
		return $this->htaccess_file_content;
	}

	/**
	 * Loads and returns the groups cached data.
	 * @return HtaccessFileCache The cached data
	 */
	public static function load()
	{
		return CacheManager::load(__CLASS__, 'kernel', 'htaccess-file');
	}

	/**
	 * Invalidates the current groups cached data.
	 */
	public static function invalidate()
	{
		CacheManager::invalidate('kernel', 'htaccess-file');
	}

	/**
	 * Regenerates the .htaccess file
	 */
	public static function regenerate()
	{
		self::invalidate();
		self::update_htaccess_file();
	}

	private static function update_htaccess_file()
	{
		$file = new File(PATH_TO_ROOT . '/.htaccess');

		try
		{
			$file->write(self::get_file_content());
			$file->close();
		}
		catch(IOException $ex)
		{
			ErrorHandler::add_error_in_log('Couldn\'t write the .htaccess file. Please check the site root read authorizations.', '');
		}
	}

	/**
	 *
	 * @return string
	 */
	private static function get_file_content()
	{
		return self::load()->get_htaccess_file_content();
	}
}
?>