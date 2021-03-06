<?php
/*##################################################
 *                           DivHTMLElement.class.php
 *                            -------------------
 *   begin                : May 26, 2015
 *   copyright            : (C) 2015 Julien BRISWALTER
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
 * 
 * @author Julien BRISWALTER <j1.seth@phpboost.com>
 * @package {@package}
 */
class DivHTMLElement extends AbstractHTMLElement
{
	private $content;
	private $attributs = array();

	public function __construct($content, $attributs = array(), $css_class = '')
	{
		$this->content = $content;
		$this->attributs = $attributs;
		$this->css_class = $css_class;
	}

	public function display()
	{
		$tpl = new FileTemplate('framework/builder/element/DivHTMLElement.tpl');

		$tpl->put_all(array(
			'C_HAS_CSS_CLASSES' => $this->has_css_class(),
			'CSS_CLASSES' => $this->get_css_class(),
			'CONTENT' => $this->content
		));

		foreach ($this->attributs as $type => $value)
		{
			$tpl->assign_block_vars('attributs', array(
				'TYPE' => $type, 
				'VALUE' => $value
			));
		}

		return $tpl->render();
	}
}
?>