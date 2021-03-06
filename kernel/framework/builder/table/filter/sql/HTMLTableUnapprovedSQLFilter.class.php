<?php
/*##################################################
 *                         HTMLTableUnapprovedSQLFilter.class.php
 *                            -------------------
 *   begin                : April 18, 2017
 *   copyright            : (C) 2017 Julien BRISWALTER
 *   email                : j1.seth@phpboost.com
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
 * @desc
 * @package {@package}
 */
class HTMLTableUnapprovedSQLFilter extends HTMLTableCheckboxFilter implements SQLFragmentBuilder
{
	private static $param_id_index = 0;
	
	private $db_field;

	public function __construct($db_field, $name, $label, $checked = FormFieldCheckbox::UNCHECKED)
	{
		$this->db_field = $db_field;
		parent::__construct($name, $label, $checked);
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_sql()
	{
		$parameter_name = $this->get_sql_value_parameter_prefix() . '_' . $this->db_field;
		$query = $this->db_field . ' = ' . $parameter_name;
		$parameters = array($parameter_name => 0);
		return new SQLFragment($query, $parameters);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function is_value_allowed($value)
    {
        if (empty($value))
        {
            return false;
        }
        return parent::is_value_allowed($value);
    }
    
    protected function get_sql_value_parameter_prefix()
    {
        return __CLASS__ . '_' . self::$param_id_index++;
    }
}

?>