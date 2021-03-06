<?php
/*##################################################
 *                              pages_begin.php
 *                            -------------------
 *   begin                : August 09, 2007
 *   copyright            : (C) 2007 Sautel Benoit
 *   email                : ben.popeye@phpboost.com
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

if (defined('PHPBOOST') !== true)	exit;

load_module_lang('pages');

$pages_config = PagesConfig::load();

require_once(PATH_TO_ROOT .'/pages/pages_defines.php');

//Supprime les menus de gauche et/ou droite suivant la configuration du module.
$columns_disabled = ThemesManager::get_theme(AppContext::get_current_user()->get_theme())->get_columns_disabled();
if ($pages_config->is_left_column_disabled()) 
	$columns_disabled->set_disable_left_columns(true);
if ($pages_config->is_right_column_disabled()) 
	$columns_disabled->set_disable_right_columns(true);
?>