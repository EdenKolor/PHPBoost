<?php
/*##################################################
 *                           PagesComments.class.php
 *                            -------------------
 *   begin                : April 25, 2012
 *   copyright            : (C) 2012 Kevin MASSY
 *   email                : soldier.weasel@gmail.com
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

class PagesComments extends AbstractCommentsExtensionPoint
{
	public function get_authorizations($module_id, $id_in_module)
	{
		require_once(PATH_TO_ROOT .'/'. $module_id . '/pages_defines.php');
		
		$authorizations = PagesConfig::load()->get_authorizations();
		$page_authorizations = $this->get_page_authorizations($module_id, $id_in_module);
		
		$authorizations = new CommentsAuthorizations();
		if (!empty($page_authorizations))
		{
			$authorizations->set_authorized_access_module(AppContext::get_current_user()->check_auth($page_authorizations, READ_PAGE));
		
		}
		else
		{
			$authorizations->set_authorized_access_module(AppContext::get_current_user()->check_auth($authorizations, READ_PAGE));
		
		}
		return $authorizations;
	}
	
	public function is_display($module_id, $id_in_module)
	{
		return true;
	}
	
	private function get_page_authorizations($module_id, $id_in_module)
	{
		$columns = 'auth';
		$condition = 'WHERE id = :id_in_module';
		$parameters = array('id_in_module' => $id_in_module);
		return PersistenceContext::get_querier()->get_column_value(PREFIX . 'pages', $columns, $condition, $parameters);
	}
}
?>