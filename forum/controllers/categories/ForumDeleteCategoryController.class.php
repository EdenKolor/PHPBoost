<?php
/*##################################################
 *                              ForumDeleteCategoryController.class.php
 *                            -------------------
 *   begin                : May 15, 2015
 *   copyright            : (C) 2015 Julien BRISWALTER
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

class ForumDeleteCategoryController extends AbstractDeleteCategoryController
{
	protected function get_id_category()
	{
		return AppContext::get_request()->get_getint('id', 0);
	}

	protected function get_categories_manager()
	{
		return ForumService::get_categories_manager();
	}

	protected function get_categories_management_url()
	{
		return ForumUrlBuilder::manage_categories();
	}
	
	protected function get_delete_category_url(Category $category)
	{
		return ForumUrlBuilder::delete_category($category->get_id());
	}
	
	protected function get_module_home_page_url()
	{
		return ForumUrlBuilder::home();
	}
	
	protected function get_module_home_page_title()
	{
		return LangLoader::get_message('module_title', 'common', 'forum');
	}
	
	protected function check_authorizations()
	{
		if (!ForumAuthorizationsService::check_authorizations()->manage_categories())
		{
			$error_controller = PHPBoostErrors::user_not_authorized();
			DispatchManager::redirect($error_controller);
		}
	}
}
?>
