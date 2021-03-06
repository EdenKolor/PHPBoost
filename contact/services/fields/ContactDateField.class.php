<?php
/*##################################################
 *                               ContactDateField.class.php
 *                            -------------------
 *   begin                : July 31, 2013
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
 
class ContactDateField extends AbstractContactField
{
	public function __construct()
	{
		parent::__construct();
		$this->set_disable_fields_configuration(array('regex', 'possible_values', 'default_value_small', 'default_value_medium'));
		$this->set_name(LangLoader::get_message('type.date', 'admin-user-common'));
	}
	
	public function display_field(ContactField $field)
	{
		$fieldset = $field->get_fieldset();
		
		$fieldset->add_field(new FormFieldDate($field->get_field_name(), $field->get_name(), null, 
			array('description' => $field->get_description(), 'required' =>(bool)$field->is_required())
		));
	}
	
	public function get_value(HTMLForm $form, ContactField $field)
	{
		$field_name = $field->get_field_name();
		if ($form->has_field($field_name))
			return $form->get_value($field_name) ? $form->get_value($field_name)->format(Date::FORMAT_DAY_MONTH_YEAR) : '';
		
		return '';
	}
}
?>
