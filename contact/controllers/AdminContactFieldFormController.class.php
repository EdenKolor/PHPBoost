<?php
/*##################################################
 *                       AdminContactFieldFormController.class.php
 *                            -------------------
 *   begin                : August 4, 2013
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

class AdminContactFieldFormController extends AdminModuleController
{
	private $tpl;
	
	private $lang;
	private $admin_user_common_lang;
	/**
	 * @var HTMLForm
	 */
	private $form;
	/**
	 * @var FormButtonDefaultSubmit
	 */
	private $submit_button;
	
	private $config;
	
	private $field;
	private $id;
	private $is_new_field;
	
	public function execute(HTTPRequestCustom $request)
	{
		$this->init($request);
		
		$this->build_form($request);
		
		$this->tpl = new FileTemplate('contact/AdminContactFieldFormController.tpl');
		$this->tpl->add_lang($this->lang);
		
		if ($this->submit_button->has_been_submited() && $this->form->validate())
		{
			$this->save();
			if ($this->is_new_field)
				AppContext::get_response()->redirect(ContactUrlBuilder::manage_fields(), StringVars::replace_vars($this->lang['message.success.add'], array('name' => $this->get_field()->get_name())));
			else
				AppContext::get_response()->redirect(($this->form->get_value('referrer') ? $this->form->get_value('referrer') : ContactUrlBuilder::manage_fields()), StringVars::replace_vars($this->lang['message.success.edit'], array('name' => $this->get_field()->get_name())));
		}
		
		$this->tpl->put('FORM', $this->form->display());
		
		if (!$this->get_field()->is_readonly())
			$this->tpl->put('JS_EVENT_SELECT_TYPE', $this->get_events_select_type());
		
		return new AdminContactDisplayResponse($this->tpl, !empty($this->id) ? $this->lang['admin.fields.title.edit_field.page_title'] : $this->lang['admin.fields.title.add_field.page_title']);
	}
	
	private function init(HTTPRequestCustom $request)
	{
		$this->lang = LangLoader::get('common', 'contact');
		$this->admin_user_common_lang = LangLoader::get('admin-user-common');
		$this->config = ContactConfig::load();
		$this->id = $request->get_getint('id', 0);
	}
	
	private function build_form(HTTPRequestCustom $request)
	{
		$field = $this->get_field();
		
		$regex_type = !$this->is_new_field ? (is_numeric($field->get_regex()) ? $field->get_regex() : 6) : 0;
		$regex = is_string($field->get_regex()) ? $field->get_regex() : '';
		
		$form = new HTMLForm(__CLASS__);
		
		$fieldset = new FormFieldsetHTML('field', !empty($this->id) ? $this->lang['admin.fields.title.edit_field'] : $this->lang['admin.fields.title.add_field']);
		$form->add_fieldset($fieldset);
		
		$fieldset->add_field(new FormFieldTextEditor('name', $this->admin_user_common_lang['field.name'], $field->get_name(), array(
			'required' => true),
			array(new ContactConstraintFieldExist($this->id))
		));
		
		$fieldset->add_field(new FormFieldShortMultiLineTextEditor('description', $this->admin_user_common_lang['field.description'], $field->get_description(), array(
			'rows' => 4, 'cols' => 47)
		));
		
		$fieldset->add_field(new FormFieldSimpleSelectChoice('field_type', $this->admin_user_common_lang['field.type'], $field->get_field_type(),
			$this->get_array_select_type($field->get_field_name()),
			array('disabled' => $field->is_readonly(), 'events' => array('change' => $this->get_events_select_type()))
		));
		
		$fieldset->add_field(new FormFieldSimpleSelectChoice('regex_type', $this->admin_user_common_lang['field.regex'], $regex_type,
			$this->get_array_select_regex(),
			array('disabled' => $field->is_readonly(), 'description' => $this->admin_user_common_lang['field.regex-explain'], 'events' => array('change' => '
				if (HTMLForms.getField("regex_type").getValue() == 6) { 
					HTMLForms.getField("regex").enable();
					jQuery("#' . __CLASS__ . '_regex").focus();
				} else { 
					HTMLForms.getField("regex").disable(); 
				}'))
		));
		
		$fieldset->add_field(new FormFieldTextEditor('regex', $this->admin_user_common_lang['regex.personnal-regex'], $regex, array(
			'hidden' => $field->is_readonly())
		));
		
		$fieldset->add_field(new FormFieldCheckbox('field_required', $this->admin_user_common_lang['field.required'], (int)$field->is_required(), array('disabled' => $field->is_readonly())));
		
		if ($field->get_field_name() == 'f_recipients')
		{
			$fieldset->add_field(new ContactFormFieldRecipientsPossibleValues('possible_values', $this->admin_user_common_lang['field.possible-values'], $field->get_possible_values(), array(
				'description' => $this->lang['field.possible_values.email.explain'])
			));
		}
		else if ($field->get_field_name() == 'f_subject')
		{
			$fieldset->add_field(new ContactFormFieldObjectPossibleValues('possible_values', $this->admin_user_common_lang['field.possible-values'], $field->get_possible_values(), array(
				'description' => $this->lang['field.possible_values.recipient.explain'])
			));
		}
		else
		{
			$fieldset->add_field(new FormFieldPossibleValues('possible_values', $this->admin_user_common_lang['field.possible-values'], $field->get_possible_values(), array(
				'hidden' => $field->is_readonly())
			));
		}
		
		$fieldset->add_field(new FormFieldTextEditor('default_value_small', $this->admin_user_common_lang['field.default-value'], $field->get_default_value(), array('disabled' => $field->is_readonly())));
		
		$fieldset->add_field(new FormFieldShortMultiLineTextEditor('default_value_medium', $this->admin_user_common_lang['field.default-value'], $field->get_default_value(), array(
			'rows' => 4, 'cols' => 47, 'hidden' => $field->is_readonly())
		));
		
		$fieldset->add_field(new FormFieldCheckbox('display', $this->admin_user_common_lang['field.display'], (int)$field->is_displayed(), array('disabled' => $field->is_readonly())));
		
		$auth_settings = new AuthorizationsSettings(array(
			new ActionAuthorization($this->lang['admin.authorizations.display_field'], ContactField::DISPLAY_FIELD_AUTHORIZATION)
		));
		$auth_settings->build_from_auth_array($field->get_authorization());
		$auth_setter = new FormFieldAuthorizationsSetter('authorizations', $auth_settings, array('hidden' => $field->is_readonly()));
		$fieldset->add_field($auth_setter);
		
		$fieldset->add_field(new FormFieldHidden('referrer', $request->get_url_referrer()));
		
		$this->submit_button = new FormButtonDefaultSubmit();
		$form->add_button($this->submit_button);
		$form->add_button(new FormButtonReset());
		
		$this->form = $form;
	}
	
	private function get_field()
	{
		if ($this->field === null)
		{
			$fields = $this->config->get_fields();
			
			$this->field = new ContactField();
			
			if (!empty($this->id) && isset($fields[$this->id]))
			{
				$properties = $fields[$this->id];
				$this->field->set_properties($properties);
			}
			else
				$this->is_new_field = true;
		}
		return $this->field;
	}
	
	private function save()
	{
		$field = $this->get_field();
		$fields = $this->config->get_fields();
		
		if ($field->is_deletable())
			$field->set_field_name(ContactField::rewrite_field_name($this->form->get_value('name')));
		
		$field->set_name($this->form->get_value('name'));
		$field->set_description($this->form->get_value('description'));
		
		if (!$this->form->field_is_disabled('field_type'))
			$field->set_field_type($this->form->get_value('field_type')->get_raw_value());
		
		if (!$field->is_readonly() && !$this->form->field_is_disabled('regex_type'))
		{
			$regex = $regex_type = $this->form->get_value('regex_type')->get_raw_value();
			
			if (!$this->form->field_is_disabled('regex'))
				$regex = $regex_type != 6 ? $regex_type : $this->form->get_value('regex');
			
			$field->set_regex($regex);
		}
		
		if (!$this->form->field_is_disabled('field_required'))
		{
			if ((bool)$this->form->get_value('field_required'))
				$field->required();
			else
				$field->not_required();
		}
		
		if (!$this->form->field_is_disabled('possible_values'))
		{
			$field->set_possible_values($this->form->get_value('possible_values'));
		}
		
		if (!$this->form->field_is_disabled('default_value_small'))
			$field->set_default_value($this->form->get_value('default_value_small'));
		
		if (!$this->form->field_is_disabled('default_value_medium'))
			$field->set_default_value($this->form->get_value('default_value_medium'));
		
		if (!$this->form->field_is_disabled('display'))
		{
			if ((bool)$this->form->get_value('display'))
				$field->displayed();
			else
				$field->not_displayed();
		}
		
		if (!$this->form->field_is_disabled('authorizations'))
			$field->set_authorization($this->form->get_value('authorizations', $field->get_authorization())->build_auth_array());
		
		$fields[!empty($this->id) ? $this->id : count($this->config->get_fields()) + 1] = $field->get_properties();
		
		$this->config->set_fields($fields);
		
		ContactConfig::save();
	}
	
	private function get_array_select_type($field_name)
	{
		$types = array();
		
		foreach ($this->get_fields_class_name($field_name) as $field_type)
		{
			$types[] = new FormFieldSelectChoiceOption($field_type->get_name(), get_class($field_type));
		}
		return $types;
	}
	
	private function get_array_select_regex()
	{
		return array(
			new FormFieldSelectChoiceOption('--', 0),
			new FormFieldSelectChoiceOption($this->admin_user_common_lang['regex.figures'], 1),
			new FormFieldSelectChoiceOption($this->admin_user_common_lang['regex.letters'], 2),
			new FormFieldSelectChoiceOption($this->admin_user_common_lang['regex.figures-letters'], 3),
			new FormFieldSelectChoiceOption($this->admin_user_common_lang['regex.word'], 7),
			new FormFieldSelectChoiceOption($this->admin_user_common_lang['regex.mail'], 4),
			new FormFieldSelectChoiceOption($this->admin_user_common_lang['regex.website'], 5),
			new FormFieldSelectChoiceOption($this->admin_user_common_lang['regex.phone-number'], 8),
			new FormFieldSelectChoiceOption($this->admin_user_common_lang['regex.personnal-regex'], 6),
		);
	}
	
	private function get_events_select_type()
	{
		$event = '';
		$disable_fields = $this->get_disable_fields();
		foreach ($disable_fields as $name_field_disable => $field_type)
		{
			if (!empty($field_type))
			{
				$one_field = array_shift($field_type);
				$event .= 'if (HTMLForms.getField("field_type").getValue() == "'. $one_field .'"';
				foreach ($field_type as $name)
				{
					$event .= ' || HTMLForms.getField("field_type").getValue() == "'. $name .'"';
				}
				$event .= ') { 
					HTMLForms.getField("' .$name_field_disable. '").disable();';
					if ($name_field_disable == 'regex')
					{
						$event .= 'HTMLForms.getField("regex_type").disable();';
					}
					$event .= '} else { HTMLForms.getField("' .$name_field_disable. '").enable();';
					if ($name_field_disable == 'regex')
					{
						$event .= 'if (HTMLForms.getField("regex_type").getValue() != 6)
							HTMLForms.getField("regex").disable();
						HTMLForms.getField("regex_type").enable();';
					}
					$event .= '}';
			}
		}
		return $event;
	}
	
	private function get_disable_fields()
	{
		$disable_field = array(
			'name' => array(), 
			'description' => array(),
			'regex' => array(),
			'possible_values' => array(), 
			'default_value_small' => array(), 
			'default_value_medium' => array(), 
		);
		
		foreach ($this->get_fields_class_name() as $field_type)
		{
			$disable_fields_extended_field = $field_type->get_disable_fields_configuration();
			
			foreach ($disable_fields_extended_field as $name_disable_field)
			{
				if (array_key_exists($name_disable_field, $disable_field))
				{
					$disable_field[$name_disable_field][] = get_class($field_type);
				}
			}
		}
		return $disable_field;
	}
	
	private function get_fields_class_name($field_name = '')
	{
		if ($field_name == 'f_recipients')
		{
			return array(
				new ContactSimpleSelectField(),
				new ContactMultipleSelectField(),
				new ContactSimpleChoiceField(),
				new ContactMultipleChoiceField(),
			);
		}
		else if ($field_name == 'f_subject')
		{
			return array(
				new ContactShortTextField(),
				new ContactSimpleSelectField(),
				new ContactSimpleChoiceField(),
			);
		}
		else
		{
			return array(
				new ContactShortTextField(),
				new ContactHalfLongTextField(),
				new ContactLongTextField(),
				new ContactSimpleSelectField(),
				new ContactMultipleSelectField(),
				new ContactSimpleChoiceField(),
				new ContactMultipleChoiceField(),
				new ContactDateField()
			);
		}
	}
}
?>
