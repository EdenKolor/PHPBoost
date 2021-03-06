<?php
/*##################################################
 *                              NotationService.class.php
 *                            -------------------
 *   begin                : February 14, 2010
 *   copyright            : (C) 2010 Kevin MASSY
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
 
 /**
 * @author Kevin MASSY <kevin.massy@phpboost.com>
 * @package {@package}
 */
class NotationService
{
	private static $js_already_included = false;
	
	private static $user;
	private static $db_querier;
	private static $lang;
	
	public static function __static()
	{
		self::$user = AppContext::get_current_user();
		self::$db_querier = PersistenceContext::get_querier();
		self::$lang = LangLoader::get('main');
	}
        
	/**
	 * @desc This function required object Notation containing the module_name, id in module and notation_scale.
	 * @param object $notation Notation
	 * @param boolean $average_notes
	 */
	public static function display_static_image(Notation $notation)
	{
		$notation_scale = $notation->get_notation_scale();
		if (!empty($notation_scale))
		{
			$template = new FileTemplate('framework/content/notation/notation.tpl');

			$average_notes = $notation->get_average_notes();
			$int = intval($average_notes);
			$decimal = floatval('0.' . TextHelper::substr($average_notes, TextHelper::strpos($average_notes, '.') + 1));

			for ($i = 1; $i <= $notation->get_notation_scale(); $i++)
			{				
				$star_full = false;
				$star_half = false;
				$star_empty = false;
				$width = 0;
				
				if ($int >= $i || ($int+1 == $i && $decimal == 0)) {
					$star_full = true;
					$star_width = 'star-width-100';
				}
				else if ($int+1 == $i && $decimal >= 0.90) {
					$star_full = true;
					$star_width = 'star-width-90';
				}
				else if ($int+1 == $i && $decimal >= 0.75 && $decimal < 0.9) {
					$star_full = true;
					$star_width = 'star-width-75';
				}
				else if ($int+1 == $i && $decimal >= 0.5 && $decimal < 0.75) {
					$star_half = true;
					$star_width = 'star-width-50';
				}
				else if ($int+1 == $i && $decimal >= 0.25 && $decimal < 0.5) {
					$star_half = true;
					$star_width = 'star-width-25';
				}
				else if ($int+1 == $i && $decimal >= 0.1 && $decimal < 0.25) {
					$star_empty = true;
					$star_width = 'star-width-10';
				}
				else {
					$star_empty = true;
					$star_width = 'star-width-0';
				}
				
				$template->assign_block_vars('star', array(
					'I' => $i,
					'STAR_EMPTY' => $star_empty,
					'STAR_HALF'  => $star_half,
					'STAR_FULL'  => $star_full,
					'STAR_WIDTH' => $star_width
				));
			}

			$count_notes = $notation->get_number_notes();
			$template->put_all(array(
				'C_STATIC_DISPLAY' => true,
				'C_NOTES' => $count_notes > 0 ? true : false,
				'NUMBER_NOTES' => $notation->get_number_notes(),
				'AVERAGE_NOTES' => $average_notes,
				'NOTATION_SCALE' => $notation->get_notation_scale(),
				'L_NO_NOTE' => LangLoader::get_message('no_note', 'common'),
			));

			return $template->render();
		}
		else
		{
			throw new Exception('The notation scale is empty');
		}
	}
	
	/**
	 * @desc This function required object Notation containing the module_name, id_in_module, user_id, note and notation_scale.
	 * @param object $notation Notation
	 */
	public static function display_active_image(Notation $notation)
	{
		$note_post = AppContext::get_request()->get_int('note', 0);
		$id_post = AppContext::get_request()->get_int('id', 0);
		
		if (!empty($note_post) && !empty($id_post))
		{
			$notation->set_id_in_module($id_post);
			$notation->set_note($note_post);
			self::register_notation($notation);
		}
		else
		{
			$template = new FileTemplate('framework/content/notation/notation.tpl');

			$average_notes = $notation->get_average_notes();
			$int = intval($average_notes);
			$decimal = floatval('0.' . TextHelper::substr($average_notes, TextHelper::strpos($average_notes, '.') + 1));

			for ($i = 1; $i <= $notation->get_notation_scale(); $i++)
			{				
				$star_full = false;
				$star_half = false;
				$star_empty = false;
				$width = 0;
				
				if ($int >= $i || ($int+1 == $i && $decimal == 1)) {
					$star_full = true;
					$star_width = 'star-width-100'; 
				}
				else if ($int+1 == $i && $decimal >= 0.90) {
					$star_full = true;
					$star_width = 'star-width-90'; 
				}
				else if ($int+1 == $i && $decimal >= 0.75 && $decimal < 0.9) {
					$star_full = true;
					$star_width = 'star-width-75'; 
				}
				else if ($int+1 == $i && $decimal >= 0.5 && $decimal < 0.75) {
					$star_half = true;
					$star_width = 'star-width-50'; 
				}
				else if ($int+1 == $i && $decimal >= 0.25 && $decimal < 0.5) {
					$star_half = true;
					$star_width = 'star-width-25'; 
				}
				else if ($int+1 == $i && $decimal >= 0.1 && $decimal < 0.25) {
					$star_empty = true;
					$star_width = 'star-width-10'; 
				}
				else {
					$star_empty = true;
					$star_width = 'star-width-0'; 
				}
				
				$template->assign_block_vars('star', array(
					'I' => $i,
					'STAR_EMPTY' => $star_empty,
					'STAR_HALF'  => $star_half,
					'STAR_FULL'  => $star_full,
					'STAR_WIDTH' => $star_width
				));
			}

			$count_notes = $notation->get_number_notes();
			$template->put_all(array(
				'C_JS_NOT_ALREADY_INCLUDED' => !self::$js_already_included,
				'C_NOTES' => $count_notes > 0 ? true : false,
				'C_MORE_1_NOTES' => $count_notes > 1 ? true : false,
				'CURRENT_URL' => REWRITED_SCRIPT,
				'ID_IN_MODULE' => $notation->get_id_in_module(),
				'NOTATION_SCALE' => $notation->get_notation_scale(),
				'NUMBER_NOTES' => $count_notes,
				'AVERAGE_NOTES' => $average_notes,
				'ALREADY_NOTE' => $notation->user_already_noted(),
				'L_NO_NOTE' => LangLoader::get_message('no_note', 'common'),
				'L_AUTH_ERROR' => LangLoader::get_message('error.auth', 'status-messages-common'),
				'L_ALREADY_NOTE' => self::$lang['already_vote'],
				'L_NOTES' => LangLoader::get_message('notes', 'common'),
				'L_NOTE' => LangLoader::get_message('note', 'common'),
				'L_VALID_NOTE' => LangLoader::get_message('add_note', 'common')
			));
			
			self::$js_already_included = true;

			return $template->render();
		}
	}
	
	/**
	 * @desc This fonction update notation scale by module_name
	 * @param string $module_name 
	 * @param string $old_notation_scale
	 * @param string $new_notation_scale
	 */
	public static function update_notation_scale($module_name, $old_notation_scale, $new_notation_scale)
	{
		if ($old_notation_scale !== $new_notation_scale)
		{
			$coefficient = $new_notation_scale / $old_notation_scale;
			self::$db_querier->inject("UPDATE " . DB_TABLE_AVERAGE_NOTES . " SET average_notes = average_notes * " . $coefficient . " WHERE module_name = '". $module_name . "'");
			self::$db_querier->inject("UPDATE " . DB_TABLE_NOTE . " SET note = note * " . $coefficient . " WHERE module_name = '". $module_name . "'");
		}
	}
	
	/**
	 * @desc This fonction delete all notes by id module and id in module element
	 * @param string $module_name 
	 * @param string $id_in_module
	 */
	public static function delete_notes_id_in_module($module_name, $id_in_module)
	{
		try {
			$condition = 'WHERE module_name=:module_name AND id_in_module=:id_in_module';
			$parameters = array('module_name' => $module_name, 'id_in_module' => $id_in_module);
			
			self::$db_querier->delete(DB_TABLE_AVERAGE_NOTES, $condition, $parameters);
			self::$db_querier->delete(DB_TABLE_NOTE, $condition, $parameters);
		} catch (MySQLQuerierException $e) {
		}
	}
	
	/**
	 * @desc This fonction delete all notes by module
	 * @param string $module_name 
	 */
	public static function delete_notes_module($module_name)
	{
		try {
			$condition = 'WHERE module_name=:module_name';
			$parameters = array('module_name' => $module_name);
				
			self::$db_querier->delete(DB_TABLE_AVERAGE_NOTES, $condition, $parameters);
			self::$db_querier->delete(DB_TABLE_NOTE, $condition, $parameters);
		} catch (MySQLQuerierException $e) {
		}
	}
	
	/**
	 * This function required object Notation containing the module_name and id_in_module.
	 */
	public static function get_number_notes(Notation $notation)
	{
		try {
			return self::$db_querier->get_column_value(DB_TABLE_AVERAGE_NOTES, 'number_notes', 'WHERE module_name = :module_name AND id_in_module = :id_in_module', 
			array('module_name' => $notation->get_module_name(), 'id_in_module' => $notation->get_id_in_module()));
		} catch (RowNotFoundException $e) {
			return 0;
		}
	}
	
	/**
	 * This function required object Notation containing the module_name and id_in_module.
	 */
	public static function get_average_notes(Notation $notation)
	{
		try {
			return self::$db_querier->get_column_value(DB_TABLE_AVERAGE_NOTES, 'average_notes', 'WHERE module_name = :module_name AND id_in_module = :id_in_module', 
			array('module_name' => $notation->get_module_name(), 'id_in_module' => $notation->get_id_in_module()));
		} catch (RowNotFoundException $e) {
			return 0;
		}
	}
	
	/**
	 * This function required object Notation containing the module_name, id_in_module and user_id.
	 */
	public static function get_informations_note(Notation $notation)
	{
		try {
			return self::$db_querier->select_single_row_query('SELECT average_notes, number_notes, (SELECT COUNT(*) FROM '. DB_TABLE_NOTE .'
			WHERE user_id=:user_id AND module_name=:module_name AND id_in_module=:id_in_module) AS user_already_noted
			FROM ' . DB_TABLE_AVERAGE_NOTES . '
			WHERE module_name = :module_name AND id_in_module = :id_in_module', array(
				'module_name' => $notation->get_module_name(), 
				'id_in_module' => $notation->get_id_in_module(),
				'user_id' => $notation->get_user_id()
			));
		} catch (RowNotFoundException $e) {
			return array(
				'average_notes' => 0,
				'number_notes' => 0,
				'user_already_noted' => 0
			);
		}
	}
	
	private static function register_notation(Notation $notation)
	{
		if (self::$user->check_level(User::MEMBER_LEVEL))
		{
			$note_is_valid = $notation->get_note() >= 0 && $notation->get_note() <= $notation->get_notation_scale() ? true : false;
			$member_already_notation = self::$db_querier->count(DB_TABLE_NOTE, 'WHERE user_id=:user_id AND module_name=:module_name AND id_in_module=:id_in_module', array(
				'module_name' => $notation->get_module_name(), 
				'id_in_module' => $notation->get_id_in_module(),
				'user_id' => $notation->get_user_id()
			));
			
			if (!$member_already_notation && $note_is_valid)
			{
				self::$db_querier->insert(DB_TABLE_NOTE, array(
					'module_name' => $notation->get_module_name(),
					'id_in_module' => $notation->get_id_in_module(),
					'user_id' => $notation->get_user_id(),
					'note' => $notation->get_note()
				));
				
				$condition = 'WHERE module_name=:module_name AND id_in_module=:id_in_module';
				$parameters = array('module_name' => $notation->get_module_name(), 'id_in_module' => $notation->get_id_in_module());
				
				$nbr_notes = self::$db_querier->count(DB_TABLE_AVERAGE_NOTES, $condition, $parameters);
				if ($nbr_notes == 0)
				{
					self::$db_querier->insert(DB_TABLE_AVERAGE_NOTES, array(
						'module_name' => $notation->get_module_name(),
						'id_in_module' => $notation->get_id_in_module(),
						'average_notes' => self::calculates_average_notes($notation),
						'number_notes' => 1
					));
				}
				else
				{
					self::$db_querier->update(DB_TABLE_AVERAGE_NOTES, array(
						'average_notes' => self::calculates_average_notes($notation), 
						'number_notes' => self::get_number_notes($notation) + 1)
					, $condition, $parameters);
				}
			}
		}
		else
		{
			DispatchManager::redirect(PHPBoostErrors::user_not_authorized());
		}
	}
	
	private static function calculates_average_notes(Notation $notation)
	{
		try {
			$result = self::$db_querier->select_rows(DB_TABLE_NOTE, array('note'), 'WHERE module_name=:module_name AND id_in_module=:id_in_module', 
			array('module_name' => $notation->get_module_name(), 'id_in_module' => $notation->get_id_in_module()));
			
			$notes = 0;
			while ($row = $result->fetch())
			{
				$notes += $row['note'];
			}
			$result->dispose();
			
			return (round(($notes / $result->get_rows_count()) / 0.25) * 0.25);
		} catch (RowNotFoundException $e) {
			return 0;
		}
	}
}
?>