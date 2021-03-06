<?php
/*##################################################
 *                       WikiModuleUpdateVersion.class.php
 *                            -------------------
 *   begin                : May 22, 2014
 *   copyright            : (C) 2014 Julien BRISWALTER
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

class WikiModuleUpdateVersion extends ModuleUpdateVersion
{
	private $querier;
	private $db_utils;
	
	public function __construct()
	{
		parent::__construct('wiki');
		$this->querier = PersistenceContext::get_querier();
		$this->db_utils = PersistenceContext::get_dbms_utils();
	}
	
	public function execute()
	{
		if (ModulesManager::is_module_installed('wiki'))
		{
			$tables = $this->db_utils->list_tables(true);
			
			if (in_array(PREFIX . 'wiki_contents', $tables))
				$this->update_wiki_contents_table();
		}
		
		$this->delete_old_files();
	}
	
	private function update_wiki_contents_table()
	{
		$columns = $this->db_utils->desc_table(PREFIX . 'wiki_contents');
		
		if (!isset($columns['change_reason']))
			$this->db_utils->add_column(PREFIX . 'wiki_contents', 'change_reason', array('type' => 'text', 'length' => 100, 'notnull' => 0));
	}
	
	private function delete_old_files()
	{
		$file = new File(Url::to_rel('/' . $this->module_id . '/phpboost/WikiNewContent.class.php'));
		$file->delete();
	}
}
?>
