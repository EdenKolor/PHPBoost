<?php
/*##################################################
 *                                lastread.php
 *                            -------------------
 *   begin                : April 02, 2007
 *   copyright            : (C) 2007 Viarre R�gis
 *   email                : crowkait@phpboost.com
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

require_once('../kernel/begin.php'); 
require_once('../forum/forum_begin.php');
require_once('../forum/forum_tools.php');

$Bread_crumb->add($CONFIG_FORUM['forum_name'], 'index.php');
$Bread_crumb->add($LANG['show_last_read'], '');

define('TITLE', $LANG['title_forum'] . ' - ' . $LANG['show_last_read']);
require_once('../kernel/header.php'); 

//Redirection changement de cat�gorie.
$change_cat = retrieve(POST, 'change_cat', '');
if (!empty($change_cat))
	AppContext::get_response()->redirect('/forum/forum' . url('.php?id=' . $change_cat, '-' . $change_cat . $rewrited_title . '.php', '&'));
if (!AppContext::get_current_user()->check_level(User::MEMBER_LEVEL)) //R�serv� aux membres.
	AppContext::get_response()->redirect(UserUrlBuilder::connect()->rel()); 

if (AppContext::get_current_user()->check_level(User::MEMBER_LEVEL)) //Affichage des message()s non lu(s) du membre.
{
	$tpl = new FileTemplate('forum/forum_forum.tpl');
	
	//Calcul du temps de p�remption, ou de derni�re vue des messages par � rapport � la configuration.
	$max_time = (time() - $CONFIG_FORUM['view_time']);
	$max_time_msg = forum_limit_time_msg();
	
	$nbr_topics = $Sql->query("SELECT COUNT(*)
	FROM " . PREFIX . "forum_view v
	LEFT JOIN " . PREFIX . "forum_topics t ON t.id = v.idtopic
	WHERE t.last_timestamp >= '" . $max_time . "' AND v.user_id = '" . AppContext::get_current_user()->get_id() . "'");
	
	$page = AppContext::get_request()->get_getint('p', 1);
	$pagination = new ModulePagination($page, $nbr_topics, $CONFIG_FORUM['pagination_topic'], Pagination::LIGHT_PAGINATION);
	$pagination->set_url(new Url('/forum/lastread.php?p=%d'));

	if ($pagination->current_page_is_empty() && $page > 1)
	{
		$error_controller = PHPBoostErrors::unexisting_page();
		DispatchManager::redirect($error_controller);
	}
	
	$result = $Sql->query_while("SELECT m1.display_name AS login, m1.level AS user_level, m1.groups AS user_groups, m2.display_name AS last_login, m2.level AS last_user_level, m2.groups AS last_user_groups, t.id, t.title, t.subtitle, t.user_id, t.nbr_msg, t.nbr_views, t.last_user_id, t.last_msg_id, t.last_timestamp, t.type, t.status, t.display_msg, v.last_view_id, p.question, tr.id AS idtrack
	FROM " . PREFIX . "forum_view v
	LEFT JOIN " . PREFIX . "forum_topics t ON t.id = v.idtopic
	LEFT JOIN " . PREFIX . "forum_cats c ON c.id = t.idcat 
	LEFT JOIN " . PREFIX . "forum_poll p ON p.idtopic = t.id
	LEFT JOIN " . PREFIX . "forum_track tr ON tr.idtopic = t.id AND tr.user_id = '" . AppContext::get_current_user()->get_id() . "'
	LEFT JOIN " . DB_TABLE_MEMBER . " m1 ON m1.user_id = t.user_id
	LEFT JOIN " . DB_TABLE_MEMBER . " m2 ON m2.user_id = t.last_user_id
	WHERE t.last_timestamp >= '" . $max_time . "' AND v.user_id = '" . AppContext::get_current_user()->get_id() . "'
	ORDER BY t.last_timestamp DESC
	" . $Sql->limit($pagination->get_display_from(), $CONFIG_FORUM['pagination_topic']));
	while ($row = $Sql->fetch_assoc($result))
	{
		$last_group_color = User::get_group_color($row['last_user_groups'], $row['last_user_level']);
		$last_msg = $LANG['on'] . ' ' . gmdate_format('date_format', $row['last_timestamp']) . '<br /> ' . $LANG['by'] . ' <a class="small '.UserService::get_level_class($row['last_user_level']).'"' . (!empty($last_group_color) ? ' style="color:' . $last_group_color . '"' : '') . ' href="'. UserUrlBuilder::profile($row['last_user_id'])->rel() .'">' . $row['last_login'] . '</a>';
		
		//On d�finit un array pour l'appelation correspondant au type de champ
		$type = array('2' => $LANG['forum_announce'] . ':', '1' => $LANG['forum_postit'] . ':', '0' => '');
			
		//V�rifications des topics Lu/non Lus.
		$img_announce = 'fa-announce';
		$blink = false;
		if ($row['last_view_id'] != $row['last_msg_id'] && $row['last_timestamp'] >= $max_time_msg) //Nouveau message (non lu).
		{
			$img_announce = $img_announce . '-new'; //Image affich� aux visiteurs.
			$blink = true;
		}
		$img_announce .= ($row['type'] == '1') ? '-post' : '';
		$img_announce .= ($row['type'] == '2') ? '-top' : '';
		$img_announce .= ($row['status'] == '0' && $row['type'] == '0') ? '-lock' : '';
		
		//Si le dernier message lu est pr�sent on redirige vers lui, sinon on redirige vers le dernier post�.		
		if (!empty($row['last_view_id'])) //Calcul de la page du last_view_id r�alis� dans topic.php
		{
			$last_msg_id = $row['last_view_id']; 
			$last_page = 'idm=' . $row['last_view_id'] . '&amp;';
			$last_page_rewrite = '-0-' . $row['last_view_id'];
		}
		else
		{
			$last_msg_id = $row['last_msg_id']; 
			$last_page = ceil( $row['nbr_msg'] / $CONFIG_FORUM['pagination_msg'] );
			$last_page_rewrite = ($last_page > 1) ? '-' . $last_page : '';
			$last_page = ($last_page > 1) ? 'pt=' . $last_page . '&amp;' : '';
		}	
		
		//On encode l'url pour un �ventuel rewriting, c'est une op�ration assez gourmande
		$rewrited_title = ServerEnvironmentConfig::load()->is_url_rewriting_enabled() ? '+' . Url::encode_rewrite($row['title']) : '';
		
		//Affichage du dernier message post�.
		$last_group_color = User::get_group_color($row['last_user_groups'], $row['last_user_level']);
		$last_msg = '<a href="topic' . url('.php?' . $last_page . 'id=' . $row['id'], '-' . $row['id'] . $last_page_rewrite .  $rewrited_title . '.php') . '#m' . $last_msg_id . '" title=""><i class="fa fa-hand-o-right"></i></a>' . ' ' . $LANG['on'] . ' ' . gmdate_format('date_format', $row['last_timestamp']) . '<br /> ' . $LANG['by'] . ' ' . (!empty($row['last_login']) ? '<a class="small '.UserService::get_level_class($row['last_user_level']).'"' . (!empty($last_group_color) ? ' style="color:' . $last_group_color . '"' : '') . ' href="'. UserUrlBuilder::profile($row['last_user_id'])->rel() .'">' . TextHelper::wordwrap_html($row['last_login'], 13) . '</a>' : '<em>' . $LANG['guest'] . '</em>');
		
		//Ancre ajout�e aux messages non lus.
		$new_ancre = '<a href="topic' . url('.php?' . $last_page . 'id=' . $row['id'], '-' . $row['id'] . $last_page_rewrite . $rewrited_title . '.php') . '#m' . $last_msg_id . '" title=""><i class="fa fa-hand-o-right"></i></a>';
		
		//On cr�e une pagination (si activ�) si le nombre de topics est trop important.
		$page = AppContext::get_request()->get_getint('pt', 1);
		$topic_pagination = new ModulePagination($page, $row['nbr_msg'], $CONFIG_FORUM['pagination_msg'], Pagination::LIGHT_PAGINATION);
		$topic_pagination->set_url(new Url('/forum/topic.php?id=' . $row['id'] . '&amp;pt=%d'));
		
		$group_color = User::get_group_color($row['groups'], $row['user_level']);
		
		$tpl->assign_block_vars('topics', array(
			'C_PAGINATION' => $topic_pagination->has_several_pages(),
			'C_IMG_POLL' => !empty($row['question']),
			'C_IMG_TRACK' => !empty($row['idtrack']),
			'C_DISPLAY_MSG' => ($CONFIG_FORUM['activ_display_msg'] && $CONFIG_FORUM['icon_activ_display_msg'] && $row['display_msg']),
			'C_HOT_TOPIC' => ($row['type'] == '0' && $row['status'] != '0' && ($row['nbr_msg'] > $CONFIG_FORUM['pagination_msg'])),
			'C_BLINK' => $blink,
			'IMG_ANNOUNCE' => $img_announce,
			'ANCRE' => $new_ancre,
			'TYPE' => $type[$row['type']],
			'TITLE' => ucfirst($row['title']),
			'AUTHOR' => !empty($row['login']) ? '<a href="'. UserUrlBuilder::profile($row['user_id'])->rel() .'" class="small '.UserService::get_level_class($row['user_level']).'"' . (!empty($group_color) ? ' style="color:' . $group_color . '"' : '') . '>' . $row['login'] . '</a>' : '<em>' . $LANG['guest'] . '</em>',
			'DESC' => $row['subtitle'],
			'PAGINATION' => $topic_pagination->display(),
			'MSG' => ($row['nbr_msg'] - 1),
			'VUS' => $row['nbr_views'],
			'U_TOPIC_VARS' => url('.php?id=' . $row['id'], '-' . $row['id'] . $rewrited_title . '.php'),
			'U_LAST_MSG' => $last_msg,
			'L_DISPLAY_MSG' => ($CONFIG_FORUM['activ_display_msg'] && $row['display_msg']) ? $CONFIG_FORUM['display_msg'] : '',
		));	
	}
	$result->dispose();
	
	//Le membre a d�j� lu tous les messages.
	if ($nbr_topics == 0)
	{
		$tpl->put_all(array(
			'C_NO_TOPICS' => true,
			'L_NO_TOPICS' => '0 ' . $LANG['no_last_read']
		));
	}
	
	//Listes les utilisateurs en lignes.
	list($users_list, $total_admin, $total_modo, $total_member, $total_visit, $total_online) = forum_list_user_online("AND s.location_script LIKE '%" ."/forum/lastread.php%'");

	$vars_tpl = array(
		'TOTAL_ONLINE' => $total_online,
		'USERS_ONLINE' => (($total_online - $total_visit) == 0) ? '<em>' . $LANG['no_member_online'] . '</em>' : $users_list,
		'ADMIN' => $total_admin,
		'MODO' => $total_modo,
		'MEMBER' => $total_member,
		'GUEST' => $total_visit,
		'SELECT_CAT' => forum_list_cat(0, 0), //Retourne la liste des cat�gories, avec les v�rifications d'acc�s qui s'imposent.
		'L_USER' => ($total_online > 1) ? $LANG['user_s'] : $LANG['user'],
		'L_ADMIN' => ($total_admin > 1) ? $LANG['admin_s'] : $LANG['admin'],
		'L_MODO' => ($total_modo > 1) ? $LANG['modo_s'] : $LANG['modo'],
		'L_MEMBER' => ($total_member > 1) ? $LANG['member_s'] : $LANG['member'],
		'L_GUEST' => ($total_visit > 1) ? $LANG['guest_s'] : $LANG['guest'],
		'L_AND' => $LANG['and'],
		'L_ONLINE' => strtolower($LANG['online']),
	
		'C_PAGINATION' => $pagination->has_several_pages(),
		'FORUM_NAME' => $CONFIG_FORUM['forum_name'],
		'PAGINATION' => $pagination->display(),
		'U_CHANGE_CAT'=> 'unread.php' . '&amp;token=' . AppContext::get_session()->get_token(),
		'U_ONCHANGE' => url(".php?id=' + this.options[this.selectedIndex].value + '", "-' + this.options[this.selectedIndex].value + '.php"),
		'U_ONCHANGE_CAT' => url("index.php?id=' + this.options[this.selectedIndex].value + '", "cat-' + this.options[this.selectedIndex].value + '.php"),
		'U_FORUM_CAT' => '<a href="../forum/lastread.php' . '">' . $LANG['show_last_read'] . '</a>',
		'U_POST_NEW_SUBJECT' => '',
		'L_FORUM_INDEX' => $LANG['forum_index'],
		'L_FORUM' => $LANG['forum'],
		'L_AUTHOR' => $LANG['author'],
		'L_TOPIC' =>  ($nbr_topics > 1) ? $LANG['topic_s'] : $LANG['topic'],
		'L_MESSAGE' => $LANG['replies'],
		'L_ANSWERS' => $LANG['answers'],
		'L_VIEW' => $LANG['views'],
		'L_LAST_MESSAGE' => $LANG['last_message'],
	);
	
	$tpl->put_all($vars_tpl);
	$tpl_top->put_all($vars_tpl);
	$tpl_bottom->put_all($vars_tpl);
		
	$tpl->put('forum_top', $tpl_top);
	$tpl->put('forum_bottom', $tpl_bottom);
		
	$tpl->display();
}
else
	AppContext::get_response()->redirect('/forum/index.php');

include('../kernel/footer.php');

?>
