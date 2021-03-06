
		# INCLUDE forum_top #

		# START error_auth_write #
		<div class="message-helper notice">
			{error_auth_write.L_ERROR_AUTH_WRITE}
		</div>
		# END error_auth_write #

		# IF C_FORUM_SUB_CATS #
			<article itemscope="itemscope" itemtype="http://schema.org/Creativework" id="article-forum-subforum" class="forum-contents">
				<header>
					<h2>
						<a href="${relative_url(SyndicationUrlBuilder::rss('forum',IDCAT))}" class="fa fa-syndication" title="${LangLoader::get_message('syndication', 'common')}"></a>
						&nbsp;&nbsp;<strong>{L_SUBFORUMS}</strong>
					</h2>
				</header>
				<div class="content">
					<table id="table" class="forum-table">
						<thead>
							<tr>
								<th class="forum-announce-topic"><i class="fa fa-eye"></i></th>
								<th class="forum-topic">{L_FORUM}</th>
								<th class="forum-subject-nb"><i class="fa fa-com fa-fw" title="{L_TOPIC}"></i></th>
								<th class="forum-message-nb"><i class="fa fa-coms fa-fw" title="{L_MESSAGE}"></i></th>
								<th class="forum-last-topic"><i class="fa fa-clock fa-fw" title="{L_LAST_MESSAGE}"></i></th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<td colspan="6">
								</td>
							</tr>
						</tfoot>
						<tbody>
					# START subcats #
							<tr>
								# IF subcats.U_FORUM_URL #
								<td class="forum-announce-topic">
									<i class="fa fa-globe fa-2x"></i>
								</td>
								<td class="forum-topic" colspan="4">
									<a href="{subcats.U_FORUM_URL}" title="{subcats.NAME}">{subcats.NAME}</a>
									<span class="smaller">{subcats.DESC}</span>
								</td>
								# ELSE #
								<td class="forum-announce-topic">
									<i class="fa # IF subcats.C_BLINK #blink # ENDIF #{subcats.IMG_ANNOUNCE}"></i>
								</td>
								<td class="forum-topic">
									<a href="forum{subcats.U_FORUM_VARS}" title="{subcats.NAME}">{subcats.NAME}</a>
									<span class="smaller">{subcats.DESC}</span>
									<span class="smaller">{subcats.SUBFORUMS}</span>
								</td>
								<td class="forum-subject-nb">
									{subcats.NBR_TOPIC}
								</td>
								<td class="forum-message-nb">
									{subcats.NBR_MSG}
								</td>
								<td class="forum-last-topic">
									# IF subcats.C_LAST_TOPIC_MSG #
										<span class="last-topic-title"><a href="{subcats.U_LAST_TOPIC}" class="last-topic-title">{subcats.LAST_TOPIC_TITLE}</a></span>
										<span class="last-topic-user"><i class="fa fa-hand-o-right"></i><a href="{subcats.U_LAST_MSG}" title="" class="last-topic-date"></a> ${LangLoader::get_message('on', 'main')} {subcats.LAST_MSG_DATE_FULL}</span>
										${LangLoader::get_message('by', 'main')}
										# IF subcats.C_LAST_MSG_GUEST #
										<a href="{subcats.U_LAST_MSG_USER_PROFIL}" class="last-topic-user small {subcats.LAST_MSG_USER_LEVEL}" {subcats.LAST_MSG_USER_GROUP_COLOR}>{subcats.LAST_MSG_USER_LOGIN}</a>
										# ELSE #
										${LangLoader::get_message('guest', 'main')}
										# ENDIF #
									# ELSE #
										<em>{subcats.L_NO_MSG}</em>
									# ENDIF #
								</td>
								# ENDIF #
							</tr>
				# END subcats #
						</tbody>
					</table>
				</div>
			</article>
		# ENDIF #

		<article itemscope="itemscope" itemtype="http://schema.org/Creativework" id="article-forum-forum" class="forum-contents">
			<header>
				<h2>
					<a href="${relative_url(SyndicationUrlBuilder::rss('forum',IDCAT))}" class="fa fa-syndication" title="${LangLoader::get_message('syndication', 'common')}"></a> {U_FORUM_CAT}
					# IF C_POST_NEW_SUBJECT #
						<i class="fa fa-chevron-circle-right"></i> <a href="{U_POST_NEW_SUBJECT}" class="basic-button">{L_POST_NEW_SUBJECT}</a>
					# ENDIF #
					<span class="actions">
						# IF IDCAT #
						<a href="unread.php?cat={IDCAT}" title="{L_DISPLAY_UNREAD_MSG}"><i class="fa fa-notread"></i></a>
						# ENDIF #
						# IF C_PAGINATION # # INCLUDE PAGINATION # # ENDIF #
					</span>
				</h2>
			</header>
			<div class="content">
				<table id="table2" class="forum-table">
					<thead>
						<tr>
							<th class="forum-announce-topic"><i class="fa fa-eye"></i></th>
							<th class="forum-fixed-topic"><i class="fa fa-check"></i></th>
							<th class="forum-topic" title="{L_TOPIC}"><i class="fa fa-file-o hidden-small-screens"></i><span class="hidden-large-screens">{L_TOPIC}</span></th>
							<th class="forum-author"><i class="fa fa-user-o fa-fw hidden-small-screens" title="{L_AUTHOR}"></i><span class="hidden-large-screens">{L_AUTHOR}</span></th>
							<th class="forum-message-nb"><i class="fa fa-comments-o fa-fw hidden-small-screens" title="{L_ANSWERS}"></i><span class="hidden-large-screens">{L_ANSWERS}</span></th>
							<th class="forum-view"><i class="fa fa-eye fa-fw hidden-small-screens" title="{L_VIEW}"></i><span class="hidden-large-screens">{L_VIEW}</span></th>
							<th class="forum-last-topic"><i class="fa fa-clock-o fa-fw hidden-small-screens" title="{L_LAST_MESSAGE}"></i><span class="hidden-large-screens">{L_LAST_MESSAGE}</span></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th colspan="7">
								<div class="float-left">
									<a href="${relative_url(SyndicationUrlBuilder::rss('forum',IDCAT))}" title="${LangLoader::get_message('syndication', 'common')}"><i class="fa fa-syndication"></i></a> {U_FORUM_CAT}
									# IF C_POST_NEW_SUBJECT #
										<i class="fa fa-chevron-circle-right"></i> <a href="{U_POST_NEW_SUBJECT}" class="basic-button" title="{L_POST_NEW_SUBJECT}">{L_POST_NEW_SUBJECT}</a>
									# ENDIF #
								</div>
								# IF C_PAGINATION #<span class="float-right"># INCLUDE PAGINATION #</span># ENDIF #
							</th>
						</tr>
					</tfoot>
					# IF C_NO_MSG_NOT_READ #
					<tr>
						<td colspan="7">
							<strong>{L_MSG_NOT_READ}</strong>
						</td>
					</tr>
					# ENDIF #

					# START topics #
					<tr>
						# IF C_MASS_MODO_CHECK #
						<td class="forum-mass-modo">
							<input type="checkbox" name="ck{topics.ID}">
						</td>
						# ENDIF #
						<td class="forum-announce-topic">
							# IF NOT topics.C_HOT_TOPIC #
							<i class="fa {topics.IMG_ANNOUNCE}"></i>
							# ELSE #
							<i class="fa # IF topics.C_BLINK #blink # ENDIF #{topics.IMG_ANNOUNCE}-hot"></i>
							# ENDIF #
						</td>
						<td class="forum-fixed-topic">
							# IF topics.C_DISPLAY_MSG #<i class="fa fa-msg-display"></i># ENDIF #
							# IF topics.C_IMG_POLL #<i class="fa fa-tasks" title="{L_POLL}"></i># ENDIF #
							# IF topics.C_IMG_TRACK #<i class="fa fa-msg-track"></i># ENDIF #
						</td>
						<td class="forum-topic">
							# IF topics.C_PAGINATION #<span class="pagin-forum"># INCLUDE topics.PAGINATION #</span># ENDIF #
							{topics.ANCRE} # IF topics.TYPE # <strong>{topics.TYPE}</strong> # ENDIF # <a href="topic{topics.U_TOPIC_VARS}" title="{topics.TITLE}">{topics.L_DISPLAY_MSG} {topics.TITLE}</a>
							<span class="smaller">{topics.DESC}</span>
						</td>
						<td class="forum-author">
							{topics.AUTHOR}
						</td>
						<td class="forum-message-nb">
							{topics.MSG}
						</td>
						<td class="forum-view">
							{topics.VUS}
						</td>
						<td class="forum-last-topic">
							<span class="last-topic-title"><i class="fa fa-hand-o-right fa-fw"></i> <a href={topics.LAST_MSG_URL} title="{topics.TITLE}">{topics.LAST_MSG_DATE_FULL}</a></span>
							<span class="last-topic-user"> <i class="fa fa-user-o fa-fw"></i>
							# IF topics.C_LAST_MSG_GUEST #
							<a href="{topics.LAST_MSG_USER_PROFIL}" class="{topics.LAST_MSG_USER_LEVEL}"{topics.LAST_MSG_USER_GROUP_COLOR}>{topics.LAST_MSG_USER_LOGIN}</a>
							# ELSE #
								<em>${LangLoader::get_message('guest', 'main')}</em>
							# ENDIF #
							</span>
						</td>
					</tr>
					# END topics #

					# IF C_NO_TOPICS #
					<tr>
						<td colspan="7">
							<strong>{L_NO_TOPICS}</strong>
						</td>
					</tr>
					# ENDIF #
				</table>
			</div>
		</article>




		# INCLUDE forum_bottom #
