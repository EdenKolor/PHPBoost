<?php
/*##################################################
 *                       AdminModulesManagementController.class.php
 *                            -------------------
 *   begin                : September 20, 2011
 *   copyright            : (C) 2011 Patrick DUBEAU
 *   email                : daaxwizeman@gmail.com
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

class AdminModulesManagementController extends AdminController
{
	private $lang;
	private $view;
	
	public function execute(HTTPRequest $request)
	{
		$this->init();		
		$this->build_view();
		$this->save($request);
		
		return new AdminModulesDisplayResponse($this->view, $this->lang['modules.module_management']);
	}
	
	private function init()
	{	
		$this->load_lang();
		$this->view = new FileTemplate('admin/modules/AdminModulesManagementController.tpl');
		$this->view->add_lang($this->lang);
	}
	
	private function load_lang()
	{
		$this->lang = LangLoader::get('admin-modules-common');
	}
	
	private function build_view()
	{
		$modules_activated = ModulesManager::get_activated_modules_map_sorted_by_localized_name();
		$modules_installed = ModulesManager::get_installed_modules_map_sorted_by_localized_name();

		foreach ($modules_installed as $module)
		{
			$configuration = $module->get_configuration();
			$array_auth = $module->get_authorizations();
			$author = $configuration->get_author();
			$author_email = $configuration->get_author_email();
			$author_website = $configuration->get_author_website();
			
			if (!in_array($module, $modules_activated))
			{
				$this->view->assign_block_vars('modules_not_activated', array(
					'ID' => $module->get_id(),
					'NAME' => ucfirst($configuration->get_name()),
					'ICON' => $module->get_id(),
					'VERSION' => $configuration->get_version(),
					'AUTHOR' => !empty($author) ? '<a href="mailto:' . $author_email. '">' . $author . '</a>' : $author,
					'AUTHOR_WEBSITE' => !empty($author_website) ? '<a href="' . $author_website . '"><img src="' . PATH_TO_ROOT . '/templates/' . get_utheme() . '/images/' . get_ulang() . '/user_web.png" alt="" /></a>' : '',
					'DESCRIPTION' => $configuration->get_description(),
					'COMPATIBILITY' => $configuration->get_compatibility(),
					'PHP_VERSION' => $configuration->get_php_version(),
					'C_MODULE_ACTIVE' => $module->is_activated(),
					'AUTHORIZATIONS' => Authorizations::generate_select(ACCESS_MODULE, $array_auth, array(2 => true), $module->get_id()),
					'U_DELETE_LINK' => AdminModulesUrlBuilder::delete_module($module->get_id())->absolute()
				));	
			}
			else 
			{
				$this->view->assign_block_vars('modules_activated', array(
					'ID' => $module->get_id(),
					'NAME' => ucfirst($configuration->get_name()),
					'ICON' => $module->get_id(),
					'VERSION' => $configuration->get_version(),
					'AUTHOR' => !empty($author) ? '<a href="mailto:' . $author_email. '">' . $author . '</a>' : $author,
					'AUTHOR_WEBSITE' => !empty($author_website) ? '<a href="' . $author_website . '"><img src="' . PATH_TO_ROOT . '/templates/' . get_utheme() . '/images/' . get_ulang() . '/user_web.png" alt="" /></a>' : '',
					'DESCRIPTION' => $configuration->get_description(),
					'COMPATIBILITY' => $configuration->get_compatibility(),
					'PHP_VERSION' => $configuration->get_php_version(),
					'C_MODULE_ACTIVE' => $module->is_activated(),
					'AUTHORIZATIONS' => Authorizations::generate_select(ACCESS_MODULE, $array_auth, array(2 => true), $module->get_id()),
					'U_DELETE_LINK' => AdminModulesUrlBuilder::delete_module($module->get_id())->absolute()
				));
			}
		}
		
		$this->view->put_all(array(
			'C_MODULES_ACTIVATED' => count($modules_activated) > 0 ? true : false,
			'C_MODULES_NOT_ACTIVATED' => (count($modules_installed) - count($modules_activated)) > 0 ? true : false
		));
	}
	
	private function save(HTTPRequest $request)
	{
		if ($request->get_bool('update', false))
		{
			foreach (ModulesManager::get_installed_modules_map() as $module)
			{
				$request = AppContext::get_request();
				$module_id = $module->get_id();
				$activated = $request->get_bool('activated-' . $module_id, false);
				$authorizations = Authorizations::auth_array_simple(ACCESS_MODULE, $module_id);
				ModulesManager::update_module_authorizations($module_id, $activated, $authorizations);
			}
			MenuService::generate_cache();
			AppContext::get_response()->redirect(AdminModulesUrlBuilder::list_installed_modules());
		}	
	}
}
?>