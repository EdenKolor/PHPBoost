<?php
/*##################################################
 *                           index.php
 *                            -------------------
 *   begin                : February 21, 2012
 *   copyright            : (C) 2012 Kevin MASSY
 *   email                : soldier.weasel@gmail.com
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

define('PATH_TO_ROOT', '..');

require_once PATH_TO_ROOT . '/kernel/init.php';

$url_controller_mappers = array(
	new UrlControllerMapper('AdminHomePageConfigController', '`^/admin(?:/config)?/?$`'),
	new UrlControllerMapper('AdminHomePageAddPluginController', '`^/admin/plugin/add(?:/([0-9]+))?/?([A-Za-z0-9]+)?/?$`', array('column', 'plugin')),
	new UrlControllerMapper('HomePagePreviewController', '`^/preview/?$`'),
);
DispatchManager::dispatch($url_controller_mappers);

?>