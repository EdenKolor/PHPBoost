<?php
/*##################################################
 *		                         GuestbookFormController.class.php
 *                            -------------------
 *   begin                : June 27, 2013
 *   copyright            : (C) 2013 j1.seth
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
class GuestbookFormController extends ModuleController
{
	/**
	 * @var HTMLForm
	 */
	private $form;
	/**
	 * @var FormButtonSubmit
	 */
	private $submit_button;
	
	private $lang;
	
	private $view;
	
	private $message;
	private $is_new_message;
	
	public function execute(HTTPRequestCustom $request)
	{
		$this->init();
		
		$this->check_authorizations();
		
		$this->build_form($request);
		
		if ($this->submit_button->has_been_submited() && $this->form->validate())
		{
			$id = $this->save();
			AppContext::get_response()->redirect(GuestbookUrlBuilder::home($this->is_new_message ? 1 : $this->form->get_value('page'), $id));
		}
		
		$this->view->put('FORM', $this->form->display());
		
		return $this->generate_response($this->view);
	}
	
	public static function get_view()
	{
		$object = new self();
		$object->init();
		$object->check_authorizations();
		$object->build_form(AppContext::get_request());
		if ($object->submit_button->has_been_submited() && $object->form->validate())
		{
			$id = $object->save();
			AppContext::get_response()->redirect(GuestbookUrlBuilder::home($object->is_new_message ? 1 : $object->form->get_value('page'), $id));
		}
		$object->view->put('FORM', GuestbookAuthorizationsService::check_authorizations()->write() && !AppContext::get_current_user()->is_readonly() ? $object->form->display() : '');
		return $object->view;
	}
	
	private function init()
	{
		$this->lang = LangLoader::get('common', 'guestbook');
		$this->view = new StringTemplate('# INCLUDE FORM #');
		$this->view->add_lang($this->lang);
	}
	
	private function build_form(HTTPRequestCustom $request)
	{
		$config = GuestbookConfig::load();
		
		$formatter = AppContext::get_content_formatting_service()->get_default_factory();
		$formatter->set_forbidden_tags($config->get_forbidden_tags());
		
		$form = new HTMLForm(__CLASS__);
		
		$fieldset = new FormFieldsetHTML('message', $this->is_new_message ? $this->lang['guestbook.add'] : $this->lang['guestbook.edit']);
		$form->add_fieldset($fieldset);
		
		if (!AppContext::get_current_user()->check_level(User::MEMBER_LEVEL))
		{
			$fieldset->add_field(new FormFieldTextEditor('pseudo', LangLoader::get_message('form.name', 'common'), $this->get_message()->get_login(), array(
				'required' => true, 'maxlength' => 25)
			));
		}
		
		$fieldset->add_field(new FormFieldRichTextEditor('contents',  LangLoader::get_message('message', 'main'), $this->get_message()->get_contents(), 
			array('formatter' => $formatter, 'rows' => 10, 'cols' => 47, 'required' => true), 
			array(
				new FormFieldConstraintMaxLinks($config->get_maximum_links_message(), true),
				new FormFieldConstraintAntiFlood(GuestbookService::get_last_message_timestamp_from_user($this->get_message()->get_author_user()->get_id())
			))
		));
		
		$fieldset->add_field(new FormFieldHidden('page', $request->get_getint('page', 1)));
		
		$this->submit_button = new FormButtonDefaultSubmit();
		$form->add_button($this->submit_button);
		$form->add_button(new FormButtonReset());
		
		$this->form = $form;
	}
	
	private function get_message()
	{
		if ($this->message === null)
		{
			$id = AppContext::get_request()->get_getint('id', 0);
			if (!empty($id))
			{
				try {
					$this->message = GuestbookService::get_message('WHERE id=:id', array('id' => $id));
				} catch (RowNotFoundException $e) {
					$error_controller = PHPBoostErrors::unexisting_page();
   					DispatchManager::redirect($error_controller);
				}
			}
			else
			{
				$this->is_new_message = true;
				$this->message = new GuestbookMessage();
				$this->message->init_default_properties();
			}
		}
		return $this->message;
	}
	
	private function check_authorizations()
	{
		$message = $this->get_message();
		
		if ($message->get_id() === null)
		{
			if (!GuestbookAuthorizationsService::check_authorizations()->write())
			{
				$error_controller = PHPBoostErrors::user_not_authorized();
				DispatchManager::redirect($error_controller);
			}
		}
		else
		{
			if (!$message->is_authorized_edit())
			{
				$error_controller = PHPBoostErrors::user_not_authorized();
				DispatchManager::redirect($error_controller);
			}
		}
		if (AppContext::get_current_user()->is_readonly())
		{
			$controller = PHPBoostErrors::user_in_read_only();
			DispatchManager::redirect($controller);
		}
	}
	
	private function save()
	{
		$message = $this->get_message();
		
		if ($this->form->has_field('pseudo'))
			$message->set_login($this->form->get_value('pseudo'));
		$message->set_contents($this->form->get_value('contents'));
		
		if ($message->get_id() === null)
		{
			$id_message = GuestbookService::add($message);
		}
		else
		{
			$id_message = $message->get_id();
			GuestbookService::update($message);
		}
		
		GuestbookMessagesCache::invalidate();
		
		return $id_message;
	}
	
	private function generate_response(View $tpl)
	{
		$message = $this->get_message();
		$page = AppContext::get_request()->get_getint('page', 1);
		
		$response = new SiteDisplayResponse($tpl);
		$graphical_environment = $response->get_graphical_environment();
		
		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['module_title'], GuestbookUrlBuilder::home($page));
		
		if ($message->get_id() === null)
		{
			$graphical_environment->set_page_title($this->lang['guestbook.add'], $this->lang['module_title']);
			$breadcrumb->add($this->lang['guestbook.add'], GuestbookUrlBuilder::add());
			$graphical_environment->get_seo_meta_data()->set_canonical_url(GuestbookUrlBuilder::add());
		}
		else
		{
			$graphical_environment->set_page_title($this->lang['guestbook.edit'], $this->lang['module_title']);
			$breadcrumb->add($this->lang['guestbook.edit'], GuestbookUrlBuilder::edit($message->get_id(), $page));
			$graphical_environment->get_seo_meta_data()->set_canonical_url(GuestbookUrlBuilder::edit($message->get_id(), $page));
		}
		
		return $response;
	}
}
?>