<?php
/*##################################################
 *                   AdminArticlesConfigController.class.php
 *                            -------------------
 *   begin                : February 27, 2013
 *   copyright            : (C) 2013 Patrick DUBEAU
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

/**
 * @author Patrick DUBEAU <daaxwizeman@gmail.com>
 */
class AdminArticlesConfigController extends AdminModuleController
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
	private $admin_common_lang;
	
	/**
	 * @var ArticlesConfig
	 */
	private $config;
	private $comments_config;
	private $content_management_config;
	
	public function execute(HTTPRequestCustom $request)
	{
		$this->init();
		
		$this->build_form();
		
		$tpl = new StringTemplate('# INCLUDE MSG # # INCLUDE FORM #');
		$tpl->add_lang($this->lang);
		
		if ($this->submit_button->has_been_submited() && $this->form->validate())
		{
			$this->save();
			$tpl->put('MSG', MessageHelper::display(LangLoader::get_message('message.success.config', 'status-messages-common'), MessageHelper::SUCCESS, 4));
		}
		
		$tpl->put('FORM', $this->form->display());

		return new AdminArticlesDisplayResponse($tpl, $this->lang['module_config_title']);
	}
	
	private function init()
	{
		$this->lang = LangLoader::get('common', 'articles');
		$this->admin_common_lang = LangLoader::get('admin-common');
		$this->config = ArticlesConfig::load();
		$this->comments_config = CommentsConfig::load();
		$this->content_management_config = ContentManagementConfig::load();
	}
	
	private function build_form()
	{
		$form = new HTMLForm(__CLASS__);
		
		$fieldset = new FormFieldsetHTML('articles_configuration', LangLoader::get_message('configuration', 'admin-common'));
		$form->add_fieldset($fieldset);
		
		$fieldset->add_field(new FormFieldNumberEditor('number_articles_per_page', $this->admin_common_lang['config.items_number_per_page'], $this->config->get_number_articles_per_page(),
			array('min' => 1, 'max' => 50, 'required' => true),
			array(new FormFieldConstraintIntegerRange(1, 50))
		));
		
		$fieldset->add_field(new FormFieldNumberEditor('number_categories_per_page', $this->admin_common_lang['config.categories_number_per_page'], $this->config->get_number_categories_per_page(),
			array('min' => 1, 'max' => 50, 'required' => true),
			array(new FormFieldConstraintIntegerRange(1, 50))
		));

		$fieldset->add_field(new FormFieldNumberEditor('number_cols_display_per_line', $this->admin_common_lang['config.columns_number_per_line'], $this->config->get_number_cols_display_per_line(), 
			array('min' => 1, 'max' => 6, 'required' => true, 'description' => $this->admin_common_lang['config.columns_number_per_line.description']),
			array(new FormFieldConstraintIntegerRange(1, 6))
		));
		
		$fieldset->add_field(new FormFieldSimpleSelectChoice('items_default_sort', $this->admin_common_lang['config.items_default_sort'], $this->config->get_items_default_sort_field() . '-' . $this->config->get_items_default_sort_mode(), $this->get_sort_options()));
		
		$fieldset->add_field(new FormFieldCheckbox('display_icon_cats', $this->lang['articles_configuration.display_icon_cats'], $this->config->are_cats_icon_enabled()
		));
		
		$fieldset->add_field(new FormFieldSimpleSelectChoice('display_type', $this->lang['articles_configuration.display_type'], $this->config->get_display_type(),
			array(
				new FormFieldSelectChoiceOption($this->lang['articles_configuration.display_type.mosaic'], ArticlesConfig::DISPLAY_MOSAIC),
				new FormFieldSelectChoiceOption($this->lang['articles_configuration.display_type.list'], ArticlesConfig::DISPLAY_LIST)
			)
		));
		
		$fieldset->add_field(new FormFieldNumberEditor('number_character_to_cut', $this->lang['articles_configuration.number_character_to_cut'], $this->config->get_number_character_to_cut(),  
			array('min' => 20, 'max' => 1000, 'required' => true),
			array(new FormFieldConstraintIntegerRange(20, 1000))
		));
		
		$fieldset->add_field(new FormFieldCheckbox('display_descriptions_to_guests', $this->lang['articles_configuration.display_descriptions_to_guests'], $this->config->are_descriptions_displayed_to_guests()));
		
		$fieldset->add_field(new FormFieldRichTextEditor('root_category_description', $this->admin_common_lang['config.root_category_description'], $this->config->get_root_category_description(), 
			array('rows' => 8, 'cols' => 47)
		));
		
		$common_lang = LangLoader::get('common');
		$fieldset_authorizations = new FormFieldsetHTML('authorizations', $common_lang['authorizations'],
			array('description' => $this->admin_common_lang['config.authorizations.explain'])
		);
		
		$form->add_fieldset($fieldset_authorizations);
		
		$auth_settings = new AuthorizationsSettings(array(
			new ActionAuthorization($common_lang['authorizations.read'], Category::READ_AUTHORIZATIONS),
			new ActionAuthorization($common_lang['authorizations.write'], Category::WRITE_AUTHORIZATIONS),
			new ActionAuthorization($common_lang['authorizations.contribution'], Category::CONTRIBUTION_AUTHORIZATIONS),
			new ActionAuthorization($common_lang['authorizations.moderation'], Category::MODERATION_AUTHORIZATIONS),
			new ActionAuthorization($common_lang['authorizations.categories_management'], Category::CATEGORIES_MANAGEMENT_AUTHORIZATIONS)
		));
		
		$auth_setter = new FormFieldAuthorizationsSetter('authorizations', $auth_settings);
		$auth_settings->build_from_auth_array($this->config->get_authorizations());
		$fieldset_authorizations->add_field($auth_setter);  
		
		$this->submit_button = new FormButtonDefaultSubmit();
		$form->add_button($this->submit_button);
		$form->add_button(new FormButtonReset());
		
		$this->form = $form;
	}
	
	private function get_sort_options()
	{
		$common_lang = LangLoader::get('common');
		
		$sort_options = array(
			new FormFieldSelectChoiceOption($common_lang['form.date.creation'] . ' - ' . $common_lang['sort.asc'], Article::SORT_DATE . '-' . Article::ASC),
			new FormFieldSelectChoiceOption($common_lang['form.date.creation'] . ' - ' . $common_lang['sort.desc'], Article::SORT_DATE . '-' . Article::DESC),
			new FormFieldSelectChoiceOption($common_lang['sort_by.alphabetic'] . ' - ' . $common_lang['sort.asc'], Article::SORT_ALPHABETIC . '-' . Article::ASC),
			new FormFieldSelectChoiceOption($common_lang['sort_by.alphabetic'] . ' - ' . $common_lang['sort.desc'], Article::SORT_ALPHABETIC . '-' . Article::DESC),
			new FormFieldSelectChoiceOption($common_lang['sort_by.number_views'] . ' - ' . $common_lang['sort.asc'], Article::SORT_NUMBER_VIEWS . '-' . Article::ASC),
			new FormFieldSelectChoiceOption($common_lang['sort_by.number_views'] . ' - ' . $common_lang['sort.desc'], Article::SORT_NUMBER_VIEWS . '-' . Article::DESC),
			new FormFieldSelectChoiceOption($common_lang['author'] . ' - ' . $common_lang['sort.asc'], Article::SORT_AUTHOR . '-' . Article::ASC),
			new FormFieldSelectChoiceOption($common_lang['author'] . ' - ' . $common_lang['sort.desc'], Article::SORT_AUTHOR . '-' . Article::DESC)
		);
		
		if ($this->comments_config->module_comments_is_enabled('articles'))
		{
			$sort_options[] = new FormFieldSelectChoiceOption($common_lang['sort_by.number_comments'] . ' - ' . $common_lang['sort.asc'], Article::SORT_NUMBER_COMMENTS . '-' . Article::ASC);
			$sort_options[] = new FormFieldSelectChoiceOption($common_lang['sort_by.number_comments'] . ' - ' . $common_lang['sort.desc'], Article::SORT_NUMBER_COMMENTS . '-' . Article::DESC);
		}
		
		if ($this->content_management_config->module_notation_is_enabled('articles'))
		{
			$sort_options[] = new FormFieldSelectChoiceOption($common_lang['sort_by.best_note'] . ' - ' . $common_lang['sort.asc'], Article::SORT_NOTATION . '-' . Article::ASC);
			$sort_options[] = new FormFieldSelectChoiceOption($common_lang['sort_by.best_note'] . ' - ' . $common_lang['sort.desc'], Article::SORT_NOTATION . '-' . Article::DESC);
		}
		
		return $sort_options;
	}
	
	private function save()
	{
		$this->config->set_number_articles_per_page($this->form->get_value('number_articles_per_page'));
		$this->config->set_number_cols_display_per_line($this->form->get_value('number_cols_display_per_line'));
		
		$items_default_sort = $this->form->get_value('items_default_sort')->get_raw_value();
		$items_default_sort = explode('-', $items_default_sort);
		$this->config->set_items_default_sort_field($items_default_sort[0]);
		$this->config->set_items_default_sort_mode(TextHelper::strtolower($items_default_sort[1]));
		
		if ($this->form->get_value('display_icon_cats'))
		{
			$this->config->enable_cats_icon();
		}
		else
		{
			$this->config->disable_cats_icon();
		}
		
		$this->config->set_number_categories_per_page($this->form->get_value('number_categories_per_page'));
		$this->config->set_number_character_to_cut($this->form->get_value('number_character_to_cut', $this->config->get_number_character_to_cut()));
		
		if ($this->form->get_value('display_descriptions_to_guests'))
		{
			$this->config->display_descriptions_to_guests();
		}
		else
		{
			$this->config->hide_descriptions_to_guests();
		}
		
		$this->config->set_display_type($this->form->get_value('display_type')->get_raw_value());
		$this->config->set_root_category_description($this->form->get_value('root_category_description'));
		$this->config->set_authorizations($this->form->get_value('authorizations')->build_auth_array());
		
		ArticlesConfig::save();
		ArticlesService::get_categories_manager()->regenerate_cache();
	}
}
?>