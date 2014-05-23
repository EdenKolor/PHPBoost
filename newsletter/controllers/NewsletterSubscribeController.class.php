<?php
/*##################################################
 *                         NewsletterUnsubscribeController.class.php
 *                            -------------------
 *   begin                : February 8, 2011
 *   copyright            : (C) 2011 Kevin MASSY
 *   email                : kevin.massy@phpboost.com
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

class NewslettersubscribeController extends ModuleController
{
	private $lang;
	private $view;
	private $form;
	private $submit_button;
	
	public function execute(HTTPRequestCustom $request)
	{
		$this->init();
		$this->build_form();
		
		$tpl = new StringTemplate('# INCLUDE MSG ## INCLUDE FORM #');
		$tpl->add_lang($this->lang);

		if ($this->submit_button->has_been_submited() && $this->form->validate())
		{
			$this->save();
			$tpl->put('MSG', MessageHelper::display($this->lang['success-subscribe'], E_USER_SUCCESS, 4));
		}
		
		$tpl->put('FORM', $this->form->display());
		
		return $this->build_response($tpl);
	}
	
	private function init()
	{
		$this->lang = LangLoader::get('common', 'newsletter');
	}
	
	private function build_form()
	{
		$mail_request = AppContext::get_request()->get_string('mail_newsletter', '');
		if (AppContext::get_current_user()->check_level(User::MEMBER_LEVEL) && empty($mail_request))
		{
			$email = AppContext::get_current_user()->get_attribute('user_mail');
		}
		else
		{
			$email = $mail_request;
		}
		
		$form = new HTMLForm('newsletter');
		
		$fieldset = new FormFieldsetHTML('subscribe.newsletter', $this->lang['subscribe.newsletter']);
		$form->add_fieldset($fieldset);
		
		$fieldset->add_field(new FormFieldTextEditor('mail', $this->lang['subscribe.mail'], $email, array(
			'required' => true),
			array(new FormFieldConstraintMailAddress())
		));
		
		$newsletter_subscribe = AppContext::get_current_user()->check_level(User::MEMBER_LEVEL) ? NewsletterService::get_member_id_streams(AppContext::get_current_user()->get_id()) : array();
		$fieldset->add_field(new FormFieldMultipleSelectChoice('newsletter_choice', $this->lang['subscribe.newsletter_choice'], $newsletter_subscribe, $this->get_streams()));
		
		$this->submit_button = new FormButtonDefaultSubmit();
		$form->add_button($this->submit_button);
		$form->add_button(new FormButtonReset());
		
		$this->form = $form;
	}
	
	private function build_response(View $view)
	{
		$body_view = new FileTemplate('newsletter/NewsletterBody.tpl');
		$body_view->add_lang($this->lang);
		$body_view->put('TEMPLATE', $view);
		$response = new SiteDisplayResponse($body_view);
		$breadcrumb = $response->get_graphical_environment()->get_breadcrumb();
		$breadcrumb->add($this->lang['newsletter'], NewsletterUrlBuilder::home()->rel());
		$breadcrumb->add($this->lang['subscribe.newsletter'], NewsletterUrlBuilder::subscribe()->rel());
		$response->get_graphical_environment()->set_page_title($this->lang['subscribe.newsletter']);
		
		return $response;
	}
	
	private function get_streams()
	{
		$streams = array();
		$newsletter_streams = NewsletterStreamsCache::load()->get_streams();
		foreach ($newsletter_streams as $id => $stream)
		{
			if ($id != Category::ROOT_CATEGORY && NewsletterAuthorizationsService::id_stream($id)->subscribe())
				$streams[] = new FormFieldSelectChoiceOption($stream->get_name(), $id);
		}
		return $streams;
	}
	
	private function save()
	{
		$streams = array();
		foreach ($this->form->get_value('newsletter_choice') as $field => $option)
		{
			$streams[] = $option->get_raw_value();
		}
		
		if (AppContext::get_current_user()->check_level(User::MEMBER_LEVEL))
		{
			NewsletterService::update_subscriptions_member_registered($streams, AppContext::get_current_user()->get_id());
		}
		else
		{
			NewsletterService::update_subscriptions_visitor($streams, $this->form->get_value('mail'));
		}
	}
}
?>