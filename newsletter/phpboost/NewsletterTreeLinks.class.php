<?php
/*##################################################
 *		                         NewsletterTreeLinks.class.php
 *                            -------------------
 *   begin                : November 24, 2013
 *   copyright            : (C) 2013 Julien BRISWALTER
 *   email                : j1.seth@phpboost.com
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

/**
 * @author Julien BRISWALTER <j1.seth@phpboost.com>
 */
class NewsletterTreeLinks implements ModuleTreeLinksExtensionPoint
{
	public function get_actions_tree_links()
	{
		$lang = LangLoader::get('common', 'newsletter');
		$tree = new ModuleTreeLinks();
		
		$manage_newsletter_link = new ModuleLink($lang['newsletter.streams.manage'], NewsletterUrlBuilder::manage_streams(), NewsletterAuthorizationsService::default_authorizations()->manage_streams());
		$manage_newsletter_link->add_sub_link(new ModuleLink($lang['newsletter.streams.manage'], NewsletterUrlBuilder::manage_streams(), NewsletterAuthorizationsService::default_authorizations()->manage_streams()));
		$manage_newsletter_link->add_sub_link(new ModuleLink($lang['stream.add'], NewsletterUrlBuilder::add_stream(), NewsletterAuthorizationsService::default_authorizations()->manage_streams()));
		$tree->add_link($manage_newsletter_link);
		
		$tree->add_link(new AdminModuleLink(LangLoader::get_message('configuration', 'admin'), NewsletterUrlBuilder::configuration()));
		
		$tree->add_link(new ModuleLink($lang['newsletter-add'], NewsletterUrlBuilder::add_newsletter(), NewsletterAuthorizationsService::default_authorizations()->create_newsletters()));
		$tree->add_link(new ModuleLink($lang['newsletter.archives'], NewsletterUrlBuilder::archives(), NewsletterAuthorizationsService::default_authorizations()->read_archives()));
		
		$tree->add_link(new ModuleLink(LangLoader::get_message('module.documentation', 'admin-modules-common'), ModulesManager::get_module('newsletter')->get_configuration()->get_documentation(), NewsletterAuthorizationsService::default_authorizations()->create_newsletters()));
	
		return $tree;
	}
}
?>