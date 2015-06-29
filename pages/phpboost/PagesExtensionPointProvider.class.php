<?php
/*##################################################
 *                         pagesExtensionPointProvider.class.php
 *                            -------------------
 *   begin                : Februar 24, 2008
 *   copyright            : (C) 2008 Loic Rouchon
 *   email                : loic.rouchon@phpboost.com
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
 
define('PAGES_MAX_SEARCH_RESULTS', 100);

class PagesExtensionPointProvider extends ExtensionPointProvider
{
	public function __construct() //Constructeur de la classe
	{
		parent::__construct('pages');
	}
	
	//Récupération du cache.
	public function get_cache()
	{
		$pages_config = PagesConfig::load();
		
		//Catégories des pages
		$code = 'global $_PAGES_CATS;' . "\n";
		$code .= '$_PAGES_CATS = array();' . "\n\n";

		$result = PersistenceContext::get_querier()->select("SELECT c.id, c.id_parent, c.id_page, p.title, p.auth
		FROM " . PREFIX . "pages_cats c
		LEFT JOIN " . PREFIX . "pages p ON p.id = c.id_page
		ORDER BY p.title");
		while ($row = $result->fetch())
		{
			$code .= '$_PAGES_CATS[' . $row['id'] . '] = ' .
			var_export(array(
			'id' => $row['id'],
			'id_parent' => !empty($row['id_parent']) ? $row['id_parent'] : '0',
			'name' => $row['title'],
			'auth' => unserialize($row['auth'])
			), true)
			. ';' . "\n";
		}
		$result->dispose();
		
		return $code;
	}

	public static function _build_pages_cat_children($cats_tree, $cats, $id_parent = 0)
	{
		$i = 1;
		$nb_cats = count($cats);
		$children = array();
		while ($i <= $nb_cats)
		{
			if ($cats[$i]['id_parent'] == $id_parent)
			{
				$id = $cats[$i]['id'];
				$feeds_cat = new FeedsCat('pages', $id, $cats[$i]['name']);

				// Decrease the complexity
				unset($cats[$i]);
				$cats = array_merge($cats); // re-index the array
				$nb_cats = count($cats);

				self::_build_pages_cat_children($feeds_cat, $cats, $id);
				$cats_tree->add_child($feeds_cat);
			}
			else
			{
				$i++;
			}
		}
	}
	
	public function comments()
	{
		return new CommentsTopics(array(new PagesCommentsTopic()));
	}
	
	public function css_files()
	{
		$module_css_files = new ModuleCssFiles();
		$module_css_files->adding_running_module_displayed_file('pages.css');
		return $module_css_files;
	}
	
	public function feeds()
	{
		return new PagesFeedProvider();
	}
    
	public function home_page()
	{
		return new PagesHomePageExtensionPoint();
	}
	
	public function search()
	{
		return new PagesSearchable();
	}
	
	public function sitemap()
	{
		return new PagesSitemapExtensionPoint();
	}
	
	public function tree_links()
	{
		return new PagesTreeLinks();
	}
}
?>