<?php
/*##################################################
 *                      BugtrackerDetailController.class.php
 *                            -------------------
 *   begin                : November 11, 2012
 *   copyright            : (C) 2012 Julien BRISWALTER
 *   email                : julienseth78@phpboost.com
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

class BugtrackerDetailController extends ModuleController
{
	private $lang;
	private $view;
	private $bug;
	private $current_user;
	
	public function execute(HTTPRequestCustom $request)
	{
		$this->init();
		
		$this->check_authorizations();
		
		$this->build_view($request);
		
		return $this->build_response($this->view);
	}
	
	private function build_view($request)
	{
		//Configuration load
		$config = BugtrackerConfig::load();
		$types = $config->get_types();
		$categories = $config->get_categories();
		$severities = $config->get_severities();
		$priorities = $config->get_priorities();
		$versions = $config->get_versions_detected();
		
		$user_assigned = $this->bug->get_assigned_to_id() && UserService::user_exists("WHERE user_aprob = 1 AND user_id=:user_id", array('user_id' => $this->bug->get_assigned_to_id())) ? UserService::get_user('WHERE user_aprob = 1 AND user_id=:user_id', array('user_id' => $this->bug->get_assigned_to_id())) : '';
		$user_assigned_group_color = $user_assigned ? User::get_group_color($user_assigned->get_groups(), $user_assigned->get_level(), true) : '';
		
		$this->view->put_all($this->bug->get_array_tpl_vars());
		
		$this->view->put_all(array(
			'C_TYPES' 						=> $types,
			'C_CATEGORIES' 					=> $categories,
			'C_SEVERITIES' 					=> $severities,
			'C_PRIORITIES' 					=> $priorities,
			'C_VERSIONS' 					=> $versions,
			'C_EDIT_BUG'					=> BugtrackerAuthorizationsService::check_authorizations()->moderation() || $this->current_user->get_id() == $this->bug->get_assigned_to_id() || ($this->current_user->get_id() == $this->bug->get_author_user()->get_id() && $this->bug->get_author_user()->get_id() != User::VISITOR_LEVEL),
			'C_DELETE_BUG'					=> BugtrackerAuthorizationsService::check_authorizations()->moderation(),
			'C_CHANGE_STATUS'				=> BugtrackerAuthorizationsService::check_authorizations()->moderation() || $this->current_user->get_id() == $this->bug->get_assigned_to_id(),
			'C_USER_ASSIGNED_GROUP_COLOR'	=> !empty($user_assigned_group_color),
			'C_USER_ASSIGNED'				=> $user_assigned,
			'USER_ASSIGNED'					=> $user_assigned ? $user_assigned->get_pseudo() : '',
			'USER_ASSIGNED_LEVEL_CLASS'		=> $user_assigned ? UserService::get_level_class($user_assigned->get_level()) : '',
			'USER_ASSIGNED_GROUP_COLOR'		=> $user_assigned_group_color,
			'U_CHANGE_STATUS'				=> BugtrackerUrlBuilder::change_status($this->bug->get_id(), 'detail')->rel(),
			'U_EDIT'						=> BugtrackerUrlBuilder::edit($this->bug->get_id() . '/detail')->rel(),
			'U_DELETE'						=> BugtrackerUrlBuilder::delete($this->bug->get_id(), 'unsolved')->rel(),
		));
		
		$comments_topic = new BugtrackerCommentsTopic();
		$comments_topic->set_id_in_module($this->bug->get_id());
		$comments_topic->set_url(BugtrackerUrlBuilder::detail($this->bug->get_id()));
		$this->view->put('COMMENTS', $comments_topic->display());
	}
	
	private function init()
	{
		$this->current_user = AppContext::get_current_user();
		$request = AppContext::get_request();
		$id = $request->get_int('id', 0);
		
		$this->lang = LangLoader::get('common', 'bugtracker');
		
		try {
			$this->bug = BugtrackerService::get_bug('WHERE id=:id', array('id' => $id));
		} catch (RowNotFoundException $e) {
			$error_controller = new UserErrorController(LangLoader::get_message('error', 'status-messages-common'), $this->lang['error.e_unexist_bug']);
			DispatchManager::redirect($error_controller);
		}
		
		$this->view = new FileTemplate('bugtracker/BugtrackerDetailController.tpl');
		$this->view->add_lang($this->lang);
	}
	
	private function check_authorizations()
	{
		if (!BugtrackerAuthorizationsService::check_authorizations()->read())
		{
			$error_controller = PHPBoostErrors::user_not_authorized();
			DispatchManager::redirect($error_controller);
		}
	}
	
	private function build_response(View $view)
	{
		$request = AppContext::get_request();
		$success = $request->get_value('success', '');
		
		$body_view = BugtrackerViews::build_body_view($view, 'detail', $this->bug->get_id());
		
		//Success messages
		switch ($success)
		{
			case 'add':
				$errstr = StringVars::replace_vars($this->lang['success.add'], array('id' => $this->bug->get_id()));
				break;
			case 'edit':
				$errstr = StringVars::replace_vars($this->lang['success.edit'], array('id' => $this->bug->get_id()));
				break;
			case 'new':
				$errstr = StringVars::replace_vars($this->lang['success.new'], array('id' => $this->bug->get_id()));
				break;
			case 'fixed':
				$errstr = StringVars::replace_vars($this->lang['success.fixed'], array('id' => $this->bug->get_id()));
				break;
			case 'pending':
				$errstr = StringVars::replace_vars($this->lang['success.pending'], array('id' => $this->bug->get_id()));
				break;
			case 'in_progress':
				$errstr = StringVars::replace_vars($this->lang['success.in_progress'], array('id' => $this->bug->get_id()));
				break;
			case 'delete':
				$errstr = StringVars::replace_vars($this->lang['success.delete'], array('id' => $this->bug->get_id()));
				break;
			case 'rejected':
				$errstr = StringVars::replace_vars($this->lang['success.reject'], array('id' => $this->bug->get_id()));
				break;
			case 'reopen':
				$errstr = StringVars::replace_vars($this->lang['success.reopen'], array('id' => $this->bug->get_id()));
				break;
			case 'assigned':
				$errstr = StringVars::replace_vars($this->lang['success.assigned'], array('id' => $this->bug->get_id()));
				break;
			default:
				$errstr = '';
		}
		if (!empty($errstr))
			$body_view->put('MSG', MessageHelper::display($errstr, E_USER_SUCCESS, 5));
		
		$response = new BugtrackerDisplayResponse();
		$response->add_breadcrumb_link($this->lang['module_title'], BugtrackerUrlBuilder::home());
		$response->add_breadcrumb_link($this->lang['titles.detail'] . ' #' . $this->bug->get_id(), BugtrackerUrlBuilder::detail($this->bug->get_id()));
		$response->set_page_title($this->lang['titles.detail'] . ' #' . $this->bug->get_id());
		
		return $response->display($body_view);
	}
}
?>
