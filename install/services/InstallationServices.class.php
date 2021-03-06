<?php
/*##################################################
 *                          InstallationServices.class.php
 *                            -------------------
 *   begin                : February 3, 2010
 *   copyright            : (C) 2010 Loic Rouchon
 *   email                : loic.rouchon@phpboost.com
 *
 *
 ###################################################
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 ###################################################*/

class InstallationServices
{
	const CONNECTION_SUCCESSFUL = 0;
	const CONNECTION_ERROR = 1;
	const UNABLE_TO_CREATE_DATABASE = 2;
	const UNKNOWN_ERROR = 3;

	private static $token_file_content = '1';

	/**
	 * @var File
	 */
	private $token;

	/**
	 * @var string[string]
	 */
	private $messages;

	/**
	 * @var mixed[string] Distribution configuration
	 */
	private $distribution_config;

	public function __construct($locale = '')
	{
		$this->token = new File(PATH_TO_ROOT . '/cache/.install_token');
		if (!empty($locale))
		{
			LangLoader::set_locale($locale);
		}
		$this->messages = LangLoader::get('install', 'install');
		$this->load_distribution_configuration();
	}

	public static function get_available_langs()
	{
		$langs_folder = new Folder(PATH_TO_ROOT . '/install/lang');
		$langs_list = $langs_folder->get_folders();
		
		$available_langs = array();
		foreach ($langs_list as $lang)
		{
			$available_langs[] = $lang->get_name();
		}
		
		return $available_langs;
	}
	
	public function is_already_installed()
	{
		$tables_list = PersistenceContext::get_dbms_utils()->list_tables();
		return in_array(PREFIX . 'member', $tables_list) || in_array(PREFIX . 'configs', $tables_list);
	}

	public function check_db_connection($host, $port, $login, $password, &$database, $tables_prefix)
	{
		try
		{
			$this->try_db_connection($host, $port, $login, $password, $database, $tables_prefix);
		}
		catch (UnexistingDatabaseException $ex)
		{
			if (!$this->create_database($database))
			{
				DBFactory::reset_db_connection();
				return self::UNABLE_TO_CREATE_DATABASE;
			}
			else
			{
				return $this->check_db_connection($host, $port, $login, $password, $database, $tables_prefix);
			}
		}
		catch (DBConnectionException $ex)
		{
			DBFactory::reset_db_connection();
			return self::CONNECTION_ERROR;
		}
		catch (Exception $ex)
		{
			DBFactory::reset_db_connection();
			return self::UNKNOWN_ERROR;
		}
		return self::CONNECTION_SUCCESSFUL;
	}

	private function try_db_connection($host, $port, $login, $password, $database, $tables_prefix)
	{
		defined('PREFIX') or define('PREFIX', $tables_prefix);
		$db_connection_data = array(
			'dbms' => DBFactory::MYSQL,
			'dsn' => 'mysql:host=' . $host . ';port=' . $port . ';dbname=' . $database,
			'driver_options' => array(),
			'host' => $host,
			'login' => $login,
			'password' => $password,
			'database' => $database,
			'port' => $port
		);
		$db_connection = new MySQLDBConnection();
		DBFactory::init_factory($db_connection_data['dbms']);
		DBFactory::set_db_connection($db_connection);
		$db_connection->connect($db_connection_data);
	}

	private function create_database($database)
	{
		try {
			$database = str_replace(array('/', '\\', '.', ' ', '"', '\''), '_', $database);
			$database = PersistenceContext::get_dbms_utils()->create_database($database);
			$databases_list = PersistenceContext::get_dbms_utils()->list_databases();
			PersistenceContext::close_db_connection();
			return in_array($database, $databases_list);
		} catch (SQLQuerierException $e) {
			return false;
		}
	}

	public function create_phpboost_tables($dbms, $host, $port, $database, $login, $password, $tables_prefix)
	{
		$db_connection_data = $this->initialize_db_connection($dbms, $host, $port, $database, $login, $password, $tables_prefix);
		$this->create_tables();
		$this->write_connection_config_file($db_connection_data, $tables_prefix);
		$this->generate_installation_token();
		$this->regenerate_cache();
		return true;
	}

	public function configure_website($server_url, $server_path, $site_name, $site_slogan = '', $site_desc = '', $site_timezone = '')
	{
		$this->get_installation_token();
		$modules_to_install = $this->distribution_config['modules'];
		$this->generate_website_configuration($server_url, $server_path, $site_name, $site_slogan, $site_desc, $site_timezone);
		$this->install_modules($modules_to_install);
		$this->add_menus();
		$this->add_extended_fields();
		return true;
	}

	public function create_admin($display_name, $login, $password, $email, $create_session = true, $auto_connect = true)
	{
		$this->get_installation_token();
		$this->create_first_admin($display_name, $login, $password, $email, $create_session, $auto_connect);
		$this->delete_installation_token();
		return true;
	}

	private function load_distribution_configuration()
	{
		$this->distribution_config = parse_ini_file(PATH_TO_ROOT . '/install/distribution.ini');
	}

	private function generate_website_configuration($server_url, $server_path, $site_name, $site_slogan = '', $site_desc = '', $site_timezone = '')
	{
		$locale = LangLoader::get_locale();
		$user = new AdminUser();
		$user->set_locale($locale);
		AppContext::set_current_user($user);
		$this->save_general_config($server_url, $server_path, $site_name, $site_slogan, $site_desc, $site_timezone);
		$this->save_server_environnement_config();
		$this->init_graphical_config();
		$this->init_debug_mode();
		$this->init_user_accounts_config($locale);
		$this->install_locale($locale);
		$this->configure_theme($this->distribution_config['theme']);
	}

	private function save_general_config($server_url, $server_path, $site_name, $site_slogan, $site_description, $site_timezone)
	{
		$general_config = GeneralConfig::load();
		$general_config->set_site_url($server_url);
		$general_config->set_site_path('/' . ltrim($server_path, '/'));
		$general_config->set_site_name($site_name);
		$general_config->set_site_slogan($site_slogan);
		$general_config->set_site_description($site_description);
		$general_config->set_module_home_page($this->distribution_config['module_home_page']);
		$general_config->set_site_install_date(new Date());
		$general_config->set_site_timezone($site_timezone);
		GeneralConfig::save();
	}

	public function save_server_environnement_config()
	{
		$server_configuration = new ServerConfiguration();
		$server_environment_config = ServerEnvironmentConfig::load();
		
		try
		{
			if ($server_configuration->has_url_rewriting())
			{
				$server_environment_config->set_url_rewriting_enabled(true);
			}
		}
		catch (UnsupportedOperationException $ex) 
		{
			$server_environment_config->set_url_rewriting_enabled(false);
		}
		
		if (function_exists('ob_gzhandler') && @extension_loaded('zlib'))
		{
			$server_environment_config->set_output_gziping_enabled(true);
		}
		
		if (DataStoreFactory::is_apc_available())
		{
			DataStoreFactory::set_apc_enabled(true);
		}
		
		ServerEnvironmentConfig::save();
	}

	private function init_graphical_config()
	{
		$graphical_environment_config = GraphicalEnvironmentConfig::load();
		$graphical_environment_config->set_page_bench_enabled($this->distribution_config['bench']);
		GraphicalEnvironmentConfig::save();
	}

	private function init_debug_mode()
	{
		if ($this->distribution_config['debug'])
		{
			Debug::enabled_debug_mode();
		}
		else
		{
			Debug::disable_debug_mode();
		}
	}

	private function init_user_accounts_config($locale)
	{
		$user_accounts_config = UserAccountsConfig::load();
		$user_accounts_config->set_default_lang($locale);
		$user_accounts_config->set_default_theme($this->distribution_config['theme']);
		UserAccountsConfig::save();
	}

	private function install_locale($locale)
	{
		LangsManager::install($locale);
	}

	private function configure_theme($theme)
	{
		ThemesManager::install($theme);
	}

	private function install_modules(array $modules_to_install)
	{
		foreach ($modules_to_install as $module_name)
		{
			ModulesManager::install_module($module_name, true, false);
		}
		
		if (ServerEnvironmentConfig::load()->is_url_rewriting_enabled())
		{
			HtaccessFileCache::regenerate();
		}
	}

	private function add_menus()
	{
		MenuService::enable_all(true);
		$modules_menu = MenuService::website_modules();
		MenuService::move($modules_menu, Menu::BLOCK_POSITION__LEFT, false);
		MenuService::set_position($modules_menu, -$modules_menu->get_block_position());
	}

	private function add_extended_fields()
	{
		$lang = LangLoader::get('user-common');
		
		//Sex
		$extended_field = new ExtendedField();
		$extended_field->set_name($lang['extended-field.field.sex']);
		$extended_field->set_field_name('user_sex');
		$extended_field->set_description($lang['extended-field.field.sex-explain']);
		$extended_field->set_field_type('MemberUserSexExtendedField');
		$extended_field->set_is_required(false);
		$extended_field->set_display(false);
		$extended_field->set_is_freeze(true);
		ExtendedFieldsService::add($extended_field);
		
		//Mail notofication when receiving PM
		$extended_field = new ExtendedField();
		$extended_field->set_name($lang['extended-field.field.pmtomail']);
		$extended_field->set_field_name('user_pmtomail');
		$extended_field->set_description($lang['extended-field.field.pmtomail-explain']);
		$extended_field->set_field_type('MemberUserPMToMailExtendedField');
		$extended_field->set_is_required(false);
		$extended_field->set_display(false);
		$extended_field->set_is_freeze(true);
		ExtendedFieldsService::add($extended_field);
		
		//Date Birth
		$extended_field = new ExtendedField();
		$extended_field->set_name($lang['extended-field.field.date-birth']);
		$extended_field->set_field_name('user_born');
		$extended_field->set_description($lang['extended-field.field.date-birth-explain']);
		$extended_field->set_field_type('MemberUserBornExtendedField');
		$extended_field->set_is_required(false);
		$extended_field->set_display(false);
		$extended_field->set_is_freeze(true);
		ExtendedFieldsService::add($extended_field);
		
		//Avatar
		$extended_field = new ExtendedField();
		$extended_field->set_name($lang['extended-field.field.avatar']);
		$extended_field->set_field_name('user_avatar');
		$extended_field->set_description($lang['extended-field.field.avatar-explain']);
		$extended_field->set_field_type('MemberUserAvatarExtendedField');
		$extended_field->set_is_required(false);
		$extended_field->set_display(true);
		$extended_field->set_is_freeze(true);
		ExtendedFieldsService::add($extended_field);
	}
	
	public function regenerate_cache()
	{
		AppContext::get_cache_service()->clear_cache();
	}

	private function initialize_db_connection($dbms, $host, $port, $database, $login, $password, $tables_prefix)
	{
		defined('PREFIX') or define('PREFIX', $tables_prefix);
		$db_connection_data = array(
			'dbms' => $dbms,
			'dsn' => 'mysql:host=' . $host . ';port=' . $port . 'dbname=' . $database,
			'driver_options' => array(),
			'host' => $host,
			'port' => $port,
			'login' => $login,
			'password' => $password,
			'database' => $database,
		);
		$this->connect_to_database($dbms, $db_connection_data, $database);
		return $db_connection_data;
	}

	private function connect_to_database($dbms, array $db_connection_data, $database)
	{
		DBFactory::init_factory($dbms);
		$connection = DBFactory::new_db_connection();
		DBFactory::set_db_connection($connection);
		try
		{
			$connection->connect($db_connection_data);
		}
		catch (UnexistingDatabaseException $exception)
		{
			PersistenceContext::get_dbms_utils()->create_database($database);
			PersistenceContext::close_db_connection();
			$connection = DBFactory::new_db_connection();
			$connection->connect($db_connection_data);
			DBFactory::set_db_connection($connection);
		}
	}

	private function create_tables()
	{
		$kernel = new KernelSetup();
		$kernel->install();
	}

	private function write_connection_config_file(array $db_connection_data, $tables_prefix)
	{
		$db_config_content = '<?php' . "\n" .
			'$db_connection_data = ' . var_export($db_connection_data, true) . ";\n\n" .
			'defined(\'PREFIX\') or define(\'PREFIX\' , \'' . $tables_prefix . '\');'. "\n" .
			'defined(\'PHPBOOST_INSTALLED\') or define(\'PHPBOOST_INSTALLED\', true);' . "\n" .
			'require_once PATH_TO_ROOT . \'/kernel/db/tables.php\';' . "\n" .
		'?>';

		$db_config_file = new File(PATH_TO_ROOT . '/kernel/db/config.php');
		$db_config_file->write($db_config_content);
		$db_config_file->close();
	}

	private function create_first_admin($display_name, $login, $password, $email, $create_session, $auto_connect)
	{
		$user_id = $this->create_first_admin_account($display_name, $login, $password, $email, LangLoader::get_locale(), $this->distribution_config['theme'], GeneralConfig::load()->get_site_timezone());
		$this->configure_mail_sender_system($email);
		$this->configure_accounts_policy();
		$this->send_installation_mail($display_name, $password, $email);
		if ($create_session)
		{
			$this->connect_admin($user_id, $auto_connect);
		}
	}

	private function create_first_admin_account($display_name, $login, $password, $email, $locale, $theme, $timezone)
	{
		$user = new User();
		$user->set_display_name($display_name);
		$user->set_level(User::ADMIN_LEVEL);
		$user->set_email($email);
		$user->set_locale($locale);
		$user->set_theme($theme);
		$auth_method = new PHPBoostAuthenticationMethod($login, $password);
		return UserService::create($user, $auth_method);
	}

	private function configure_mail_sender_system($administrator_email)
	{
		$mail_config = MailServiceConfig::load();
		$mail_config->set_administrators_mails(array($administrator_email));
		$mail_config->set_default_mail_sender($administrator_email);
		MailServiceConfig::save();
	}

	private function configure_accounts_policy()
	{
		$user_account_config = UserAccountsConfig::load();
		$user_account_config->set_registration_enabled($this->distribution_config['allow_members_registration']);
		UserAccountsConfig::save();
	}

	private function send_installation_mail($login, $password, $email)
	{
		$general_config = GeneralConfig::load();
		AppContext::get_mail_service()->send_from_properties($email, $this->messages['admin.created.email.object'], sprintf($this->messages['admin.created.email.unlockCode'], stripslashes($login),
		stripslashes($login), UserUrlBuilder::forget_password()->absolute(), $general_config->get_site_url() . $general_config->get_site_path()));
	}

	private function connect_admin($user_id, $auto_connect)
	{
		$session = Session::create($user_id, $auto_connect);
		AppContext::set_session($session);
	}

	private function generate_installation_token()
	{
		$this->token->write(self::$token_file_content);
	}

	private function get_installation_token()
	{
		$is_token_valid = false;
		try
		{
			$is_token_valid = $this->token->exists() && $this->token->read() == self::$token_file_content;
		}
		catch (IOException $ioe)
		{
			$is_token_valid = false;
		}

		if (!$is_token_valid)
		{
			throw new InstallTokenNotFoundException($this->token->get_path_from_root());
		}
	}

	private function delete_installation_token()
	{
		$this->token->delete();
	}
}
?>