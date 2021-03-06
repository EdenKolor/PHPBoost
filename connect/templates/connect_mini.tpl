# IF C_USER_NOTCONNECTED #
<script>
<!--
	function check_connect()
	{
		if( document.getElementById('login').value == "" )
		{
			alert("{L_REQUIRE_PSEUDO}");
			return false;
		}
		if( document.getElementById('password').value == "" )
		{
			alert("{L_REQUIRE_PASSWORD}");
			return false;
		}
	}
-->
</script>
# ENDIF #

<div id="module-connect" class="connect-menu# IF C_USER_NOTCONNECTED # not-connected# ELSE # connected# ENDIF## IF C_VERTICAL # module-mini-container vertical# ELSE # horizontal# ENDIF #">
	# IF C_USER_NOTCONNECTED #

		# IF C_VERTICAL #
		<div class="module-mini-top">
			<div class="sub-title">{@connection}</div>
		</div>
		<div class="module-mini-contents connect-contents">
		# ELSE #
		<div class="connect-contents">
			<a href="" class="js-menu-button" onclick="open_submenu('module-connect', 'active-connect');return false;"><i class="fa fa-sign-in"></i><span>{@connection}</span></a>
		# ENDIF #
			<div class="connect-containers">
				<div class="connect-input-container# IF C_VERTICAL # vertical-fieldset# ELSE # horizontal-fieldset# ENDIF #" >
					<form action="{U_CONNECT}" method="post" onsubmit="return check_connect();" class="form-element">
						<label for="login">
							<span>{@login}</span>
							<input type="text" id="login" name="login" title="{@login}" placeholder="{@login}">
						</label>
						<label for="password">
							<span>{@password}</span>
							<input type="password" id="password" name="password" title="{@password}" placeholder="{@password}">
						</label>
						<label for="autoconnect">
							<span>{@autoconnect}</span>
							<input checked="checked" type="checkbox" id="autoconnect" name="autoconnect" title="{@autoconnect}">
						</label>
						<input type="hidden" name="redirect" value="{SITE_REWRITED_SCRIPT}">
						<input type="hidden" name="token" value="{TOKEN}">
						<button type="submit" name="authenticate" value="internal" class="submit">{@connection}</button>
					</form>
				</div>

				# IF C_USER_REGISTER #
				<div class="connect-register-container">

					<form action="${relative_url(UserUrlBuilder::registration())}" method="post">
						<button type="submit" name="register" value="true" class="submit">{@register}</button>
						<input type="hidden" name="token" value="{TOKEN}">
					</form>
					# START external_auth #
						<a class="{external_auth.CSS_CLASS}" href="{external_auth.U_CONNECT}" title="{external_auth.NAME}">{external_auth.IMAGE_HTML}</a>
					# END external_auth #
				</div>
				# ENDIF #
				<div class="forget-pass-container">
					<a class="forgot-pass small" rel="nofollow" href="${relative_url(UserUrlBuilder::forget_password())}">
						<i class="fa fa-question-circle"></i><span>${LangLoader::get_message('forget-password', 'user-common')}</span>
					</a>
				</div>
			</div>
		</div>
		# IF C_VERTICAL #
		<div class="module-mini-bottom">
		</div>
		# ENDIF #

	# ELSE # <!-- User Connected -->

		# IF C_VERTICAL #
		<div class="module-mini-top">
			<div class="sub-title">{L_PRIVATE_PROFIL}</div>
		</div>
		<div class="module-mini-contents connect-contents">
		# ELSE #
		<div class="connect-contents">
			<a href="" class="js-menu-button" onclick="open_submenu('module-connect', 'active-connect');return false;" title="{@dashboard}">
				<i class="fa fa-bars # IF NUMBER_TOTAL_ALERT # blink alert# ENDIF #"></i><span>{L_PRIVATE_PROFIL}</span>
			</a>
		# ENDIF #
			<ul class="connect-elements-container">
				<li class="connect-element connect-profil">
					<a href="${relative_url(UserUrlBuilder::home_profile())}" class="small">
						<i class="fa fa-profil"></i><span class="pbt-small-screen">{@dashboard}</span>
					</a>
				</li>
				<li class="connect-element connect-pm# IF C_HAS_PM # connect-event# ENDIF #">
					<a href="{U_USER_PM}" class="small" rel="nofollow">
						<i class="fa fa-envelope# IF C_HAS_PM # blink# ENDIF #"></i><span>{L_PM_PANEL}</span># IF C_HAS_PM #<span class="blink">({NUMBER_PM})</span># ENDIF #
					</a>
				</li>
				# IF C_ADMIN_AUTH #
				<li class="connect-element connect-admin# IF C_UNREAD_ALERT # connect-event# ENDIF #">
					<a href="${relative_url(UserUrlBuilder::administration())}" class="small" rel="nofollow">
						<i class="fa fa-wrench# IF C_UNREAD_ALERT # blink# ENDIF #"></i><span>{L_ADMIN_PANEL}</span># IF C_UNREAD_ALERT #<span class="blink">({NUMBER_UNREAD_ALERTS})</span># ENDIF #
					</a>
				</li>
				# ENDIF #
				# IF C_MODERATOR_AUTH #
				<li class="connect-element connect-modo">
					<a href="${relative_url(UserUrlBuilder::moderation_panel())}" class="small" rel="nofollow">
						<i class="fa fa-legal"></i><span>{L_MODO_PANEL}</span>
					</a>
				</li>
				# ENDIF #
				<li class="connect-element connect-contribution# IF C_UNREAD_CONTRIBUTION # connect-event# ENDIF #">
					<a href="${relative_url(UserUrlBuilder::contribution_panel())}" class="small" rel="nofollow">
						<i class="fa fa-file-alt# IF C_UNREAD_CONTRIBUTION # blink# ENDIF #"></i><span>{L_CONTRIBUTION_PANEL}</span># IF C_UNREAD_CONTRIBUTION #<span class="blink">({NUMBER_UNREAD_CONTRIBUTIONS})</span># ENDIF #
					</a>
				</li>
				<li class="connect-element connect-disconnect">
					<a href="${relative_url(UserUrlBuilder::disconnect())}" class="small" rel="nofollow">
						<i class="fa fa-sign-out"></i><span>{@disconnect}</span>
					</a>
				</li>
			</ul>
		</div>
		# IF C_VERTICAL #
		<div class="module-mini-bottom">
		</div>
		# ENDIF #
	# ENDIF #
</div>
