<?php
/*##################################################
 *                       AdminCustomizeEditorCSSFilesController.class.php
 *                            -------------------
 *   begin                : September 26, 2011
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

class AdminCustomizeEditorCSSFilesController extends AdminModuleController
{
	private $lang;
	/**
	 * @var HTMLForm
	 */
	private $form;
	/**
	 * @var FormButtonDefaultSubmit
	 */
	private $submit_button;
	
	private $tpl;
	
	private $templates_path = '/templates/';
	private $css_files_path = '/theme/';
	private $css_modules_files_path = '/modules/';

	public function execute(HTTPRequestCustom $request)
	{
		$this->init();
		
		$id_theme = $request->get_value('id_theme', '');
		$id_module = '';
		$file_selected = $request->get_value('file_name', '');
		
		if (preg_match('`/`u', $file_selected))
		{
			$split = explode('/', $file_selected);
			$id_module = $split[0];
			$file_name = $split[1];
		}
		else
			$file_name = $file_selected;
		
		$this->build_form($id_theme, $id_module, $file_name, $file_selected);
		
		if (!empty($id_theme) && !empty($file_selected))
		{
			if ($this->submit_button->has_been_submited() && $this->form->validate())
			{
				$this->save($id_theme, $id_module, $file_name);
				$this->tpl->put('MSG', MessageHelper::display(LangLoader::get_message('process.success', 'status-messages-common'), MessageHelper::SUCCESS, 4));
			}
		}

		$this->tpl->put('FORM', $this->form->display());

		return new AdminCustomizationDisplayResponse($this->tpl, $this->lang['customization.editor.css-files']);
	}

	private function init()
	{
		$this->lang = LangLoader::get('common', 'customization');
		$this->tpl = new StringTemplate('# INCLUDE MSG # # INCLUDE FORM #');
		$this->tpl->add_lang($this->lang);
	}
	
	private function build_form($theme_selected, $module_selected, $file_name, $file_selected)
	{
		$form = new HTMLForm(__CLASS__);
		
		$theme_choise_fieldset = new FormFieldsetHTML('theme-choice', $this->lang['customization.interface.theme-choice']);
		$form->add_fieldset($theme_choise_fieldset);
		
		$theme_choise_fieldset->add_field(
			new FormFieldSimpleSelectChoice('select_theme', $this->lang['customization.interface.select-theme'], $theme_selected,
				$this->list_themes(), 
				array('events' => array('change' => 'document.location.href = "' . AdminCustomizeUrlBuilder::editor_css_file()->rel() . '" + HTMLForms.getField(\'select_theme\').getValue()'))
			)
		);
		
		if (!empty($theme_selected))
		{
			$file_editor_fieldset = new FormFieldsetHTML('file-editor', $this->lang['customization.editor.css-files']);
			$form->add_fieldset($file_editor_fieldset);
		
			$file_editor_fieldset->add_field(
				new FormFieldSimpleSelectChoice('select_file', $this->lang['customization.editor.files.select'], $file_selected,
					$this->list_files($theme_selected), 
					array('events' => array('change' => 'document.location.href = "' . AdminCustomizeUrlBuilder::editor_css_file($theme_selected)->rel() . '" + "/" + HTMLForms.getField(\'select_file\').getValue()'))
				)
			);
			
			if (!empty($file_selected))
			{
				if (!empty($module_selected))
				{
					$css_file = new File(PATH_TO_ROOT . $this->templates_path . $theme_selected . $this->css_modules_files_path . $module_selected . '/' . $file_name);
					if (!$css_file->exists())
						$css_file = new File(PATH_TO_ROOT . '/' . $module_selected . '/templates/' . $file_name);
					
					$file_editor_fieldset->add_field(new FormFieldHidden('id_theme', $theme_selected));
					$file_editor_fieldset->add_field(new FormFieldHidden('file_name', $file_selected));
				}
				else
				{
					$css_file = new File(PATH_TO_ROOT . $this->templates_path . $theme_selected . $this->css_files_path . $file_selected);
				}
				
				if ($css_file->exists())
				{
					$file_editor_fieldset->add_field(new FormFieldMultiLineTextEditor('css_file', $this->lang['customization.editor.files.content'], TextHelper::htmlspecialchars($css_file->read()),
						array('rows' => 30, 'class' => "lined")
					));
				}
			}
		}
		
		if (!empty($theme_selected) && !empty($file_selected))
		{
			$this->submit_button = new FormButtonDefaultSubmit();
			$form->add_button($this->submit_button);
			$form->add_button(new FormButtonReset());
		}
		
		$this->form = $form;
	}

	private function save($theme_selected, $module_selected, $file_name)
	{
		$modules_folder = new File(PATH_TO_ROOT . $this->templates_path . $theme_selected . $this->css_modules_files_path);
		
		if (!$modules_folder->exists())
			mkdir(PATH_TO_ROOT . $this->templates_path . $theme_selected . $this->css_modules_files_path);
		
		$module_folder = new File(PATH_TO_ROOT . $this->templates_path . $theme_selected . $this->css_modules_files_path . $module_selected);
		if (!$module_folder->exists())
			mkdir(PATH_TO_ROOT . $this->templates_path . $theme_selected . $this->css_modules_files_path . $module_selected);
		
		if ($module_selected)
			$css_file = new File(PATH_TO_ROOT . $this->templates_path . $theme_selected . $this->css_modules_files_path . $module_selected . '/' . $file_name);
		else
			$css_file = new File(PATH_TO_ROOT . $this->templates_path . $theme_selected . $this->css_files_path . $file_name);
		
		if (!$css_file->exists())
			copy(PATH_TO_ROOT . '/' . $module_selected . '/templates/' . $file_name, PATH_TO_ROOT . $this->templates_path . $theme_selected . $this->css_modules_files_path . $module_selected . '/' . $file_name);
		
		$css_file->write(TextHelper::html_entity_decode($this->form->get_value('css_file')));
		$css_file->close();
	}
	
	private function list_themes()
	{
		$choices_list = array();
		$choices_list[] = new FormFieldSelectChoiceOption('--', '');
		foreach (ThemesManager::get_activated_themes_map() as $id => $value) 
		{
			$choices_list[] = new FormFieldSelectChoiceOption($value->get_configuration()->get_name(), $id);
		}
		return $choices_list;
	}
	
	private function list_files($theme_selected)
	{
		$files = array();
		$files[] = new FormFieldSelectChoiceOption('--', '');
		
		$folder = new Folder(PATH_TO_ROOT . $this->templates_path . $theme_selected . $this->css_files_path);
		if ($folder->exists())
		{
			foreach ($folder->get_files('`\.css$`') as $file)
			{
				$files[] = new FormFieldSelectChoiceOption($file->get_name(), $file->get_name());
			}
		}
		else
			$this->tpl->put('MSG', MessageHelper::display(LangLoader::get_message('error.page.unexist', 'status-messages-common'), MessageHelper::WARNING));
		
		foreach (ModulesManager::get_activated_modules_map_sorted_by_localized_name() as $id => $module) 
		{
			$folder = new Folder(PATH_TO_ROOT . '/' . $module->get_id() . '/templates');
			if ($folder->exists())
			{
				foreach ($folder->get_files('`\.css$`') as $file)
				{
					$files[] = new FormFieldSelectChoiceOption(LangLoader::get_message('module', 'admin-modules-common') . ' ' . ModulesManager::get_module($module->get_id())->get_configuration()->get_name() . ' : ' . $file->get_name(), $module->get_id() . '/' . $file->get_name());
				}
			}
		}
		
		return $files;
	}
}
?>
