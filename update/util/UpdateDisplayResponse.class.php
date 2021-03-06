<?php
/*##################################################
 *                           UpdateDisplayResponse.class.php
 *                            -------------------
 *   begin                : February 29, 2012
 *   copyright            : (C) 2012 Kevin MASSY
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

class UpdateDisplayResponse extends AbstractResponse
{
	const UPDATE_DEFAULT_LANGUAGE = 'french';

	private $lang;

	private $distribution_lang;

	private $current_step = 0;

	private $nb_steps;

	/**
	 * @var Template
	 */
	private $full_view;

	public function __construct($step_number, $step_title, Template $view)
	{
		$this->load_language_resources();
		$this->init_response($view);
		$env = new UpdateDisplayGraphicalEnvironment();
		$this->add_language_bar();
		$this->init_steps($step_number);
		$this->update_progress_bar();

		$this->full_view->put_all(array(
			'RESTART' => UpdateUrlBuilder::introduction()->rel(),
			'STEP_TITLE' => $step_title,
			'C_HAS_PREVIOUS_STEP' => false,
			'C_HAS_NEXT_STEP' => false,
			'L_XML_LANGUAGE' => LangLoader::get_message('xml_lang', 'main'),
			'PROGRESSION' => floor(100 * $this->current_step / $this->nb_steps)
		));
		
		parent::__construct($env, $this->full_view);
	}

	public function init_response(Template $view)
	{
		$this->full_view = new FileTemplate('update/main.tpl');
		$this->full_view->put('UpdateStep', $view);
		$this->full_view->add_lang($this->lang);
		$view->add_lang($this->lang);
	}

	public function load_language_resources()
	{
		$this->lang = LangLoader::get('update', 'update');
	}

	private function add_language_bar()
	{
		$lang = AppContext::get_request()->get_string('lang', self::UPDATE_DEFAULT_LANGUAGE);
		$lang_dir = new Folder(PATH_TO_ROOT . '/lang');
		$langs = array();
		foreach ($lang_dir->get_folders('`^[a-z_-]+$`i') as $folder)
		{
			$info_lang = load_ini_file(PATH_TO_ROOT . '/lang/', $folder->get_name());
			if (!empty($info_lang['name']))
			{
				$langs[] = array(
					'LANG' => $folder->get_name(),
					'LANG_NAME' => $info_lang['name'],
					'SELECTED' => $folder->get_name() == $lang ? 'selected="selected"' : ''
				);
				if ($folder->get_name() == $lang)
				{
					$this->full_view->put_all(array(
						'LANG_IDENTIFIER' => $info_lang['identifier'],
						'LANG_NAME' => $info_lang['name'])
					);
				}
			}
		}
		$this->full_view->put('lang', $langs);
	}

	private function init_steps($step_number)
	{
		$this->current_step = $step_number;
		
		$steps = array(
			array('name' => $this->lang['step.list.introduction'], 'img' => 'home'),
			array('name' => $this->lang['step.list.server'], 'img' => 'cog')
		);
		
		if (!UpdateServices::database_config_file_checked())
		{
			$steps[] = array('name' => $this->lang['step.list.database'], 'img' => 'server');
			$hide_database_page = false;
		}
		else
		{
			if ($this->current_step > 2)
				$this->current_step--;
			
			$hide_database_page = true;
		}
		
		$steps[] = array('name' => $this->lang['step.list.execute'], 'img' => 'refresh');
		$steps[] = array('name' => $this->lang['step.list.end'], 'img' => 'check');
		
		$this->nb_steps = count($steps) - 1;

		$i = 0;
		foreach ($steps as $step)
		{
			if ($i < $this->current_step)
			{
				$row_class = 'row-success';
			}
			elseif ($i == $this->current_step && $i == $this->nb_steps)
			{
				$row_class = 'row-current row-final';
			}
			elseif ($i == $this->current_step)
			{
				$row_class = 'row-current';
			}
			elseif ($i == $this->nb_steps)
			{
				$row_class = 'row-next row-final';
			}
			else
			{
				$row_class = 'row-next';
			}

			$this->full_view->assign_block_vars('step', array(
				'C_NO_DATABASE_STEP_CLASS' => $hide_database_page,
				'CSS_CLASS' => $row_class,
				'IMG' => $step['img'],
				'NAME' => $step['name']
			));
			
			$i++;
		}
	}

	private function update_progress_bar()
	{
		for ($i = 1; $i <= floor(($this->current_step / $this->nb_steps) * 24); $i++)
		{
			$this->full_view->assign_block_vars('progress_bar', array());
		}
	}
}
?>