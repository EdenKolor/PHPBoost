<?php
/*##################################################
 *                               DownloadFeedProvider.class.php
 *                            -------------------
 *   begin                : August 24, 2014
 *   copyright            : (C) 2014 Julien BRISWALTER
 *   email                : j1.seth@phpboost.com
 *
 *
 ###################################################
 *
 * This program is a free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 ###################################################*/

 /**
 * @author Julien BRISWALTER <j1.seth@phpboost.com>
 */

class DownloadFeedProvider implements FeedProvider
{
	public function get_feeds_list()
	{
		return DownloadService::get_categories_manager()->get_feeds_categories_module()->get_feed_list();
	}
	
	public function get_feed_data_struct($idcat = 0, $name = '')
	{
		if (DownloadService::get_categories_manager()->get_categories_cache()->category_exists($idcat))
		{
			$querier = PersistenceContext::get_querier();
			$category = DownloadService::get_categories_manager()->get_categories_cache()->get_category($idcat);
			
			$site_name = GeneralConfig::load()->get_site_name();
			$site_name = $idcat != Category::ROOT_CATEGORY ? $site_name . ' : ' . $category->get_name() : $site_name;
			
			$feed_module_name = LangLoader::get_message('module_title', 'common', 'download');
			$data = new FeedData();
			$data->set_title($feed_module_name . ' - ' . $site_name);
			$data->set_date(new Date());
			$data->set_link(SyndicationUrlBuilder::rss('download', $idcat));
			$data->set_host(HOST);
			$data->set_desc($feed_module_name . ' - ' . $site_name);
			$data->set_lang(LangLoader::get_message('xml_lang', 'main'));
			$data->set_auth_bit(Category::READ_AUTHORIZATIONS);
			
			$categories = DownloadService::get_categories_manager()->get_children($idcat, new SearchCategoryChildrensOptions(), true);
			$ids_categories = array_keys($categories);
			
			$now = new Date();
			$results = $querier->select('SELECT download.id, download.id_category, download.name, download.rewrited_name, download.contents, download.short_contents, download.creation_date, download.picture_url, cat.rewrited_name AS rewrited_name_cat
				FROM ' . DownloadSetup::$download_table . ' download
				LEFT JOIN '. DownloadSetup::$download_cats_table .' cat ON cat.id = download.id_category
				WHERE download.id_category IN :ids_categories
				AND (approbation_type = 1 OR (approbation_type = 2 AND start_date < :timestamp_now AND (end_date > :timestamp_now OR end_date = 0)))
				ORDER BY download.creation_date DESC', array(
					'ids_categories' => $ids_categories,
					'timestamp_now' => $now->get_timestamp()
			));
	
			foreach ($results as $row)
			{
				$row['rewrited_name_cat'] = !empty($row['id_category']) ? $row['rewrited_name_cat'] : 'root';
				$link = DownloadUrlBuilder::display($row['id_category'], $row['rewrited_name_cat'], $row['id'], $row['rewrited_name']);
				
				$item = new FeedItem();
				$item->set_title($row['name']);
				$item->set_link($link);
				$item->set_guid($link);
				$item->set_desc(FormatingHelper::second_parse($row['contents']));
				$item->set_date(new Date($row['creation_date'], Timezone::SERVER_TIMEZONE));
				$item->set_image_url($row['picture_url']);
				$item->set_auth(DownloadService::get_categories_manager()->get_heritated_authorizations($row['id_category'], Category::READ_AUTHORIZATIONS, Authorizations::AUTH_PARENT_PRIORITY));
				$data->add_item($item);
			}
			$results->dispose();
			
			return $data;
		}
	}
}
?>