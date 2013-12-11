	<div id="admin_contents" style="margin-left:0;padding:0">
		<table class="module-table">
			<tr>
				<th id="stats" colspan="5">
					{L_STATS}
				</th>
			</tr>
			<tr style="text-align:center;">
				<td style="width:20%;padding:15px;" class="row1">
					<a href="admin_stats.php?site=1#stats"><img src="{PATH_TO_ROOT}/stats/templates/images/site.png" alt="" /></a>
					<br /><a href="admin_stats.php?site=1#stats">{L_SITE}</a>
				</td>
				<td style="width:20%;padding:15px;" class="row1">
					<a href="admin_stats.php?members=1#stats"><img src="{PATH_TO_ROOT}/stats/templates/images/member.png" alt="" /></a>
					<br /><a href="admin_stats.php?members=1#stats">{L_USERS}</a>
				</td>
				<td style="width:20%;" class="row1">
					<a href="admin_stats.php?visit=1#stats"><img src="{PATH_TO_ROOT}/stats/templates/images/visitors.png" alt="" /></a>
					<br /><a href="admin_stats.php?visit=1#stats">{L_VISITS}</a>
				</td>
				<td style="width:20%;padding:15px;" class="row1">
					<a href="admin_stats.php?pages=1#stats"><img src="{PATH_TO_ROOT}/stats/templates/images/pages.png" alt="" /></a>
					<br /><a href="admin_stats.php?pages=1#stats">{L_PAGES}</a>
				</td>				
				<td style="width:20%;padding:15px;" class="row1">
					<a href="admin_stats.php?browser=1#stats"><img src="{PATH_TO_ROOT}/stats/templates/images/browsers.png" alt="" /></a>
					<br /><a href="admin_stats.php?browser=1#stats">{L_BROWSERS}</a>
				</td>
			</tr>
			<tr style="text-align:center;">						
				<td style="width:20%;" class="row1">	
					<a href="admin_stats.php?os=1#stats"><img src="{PATH_TO_ROOT}/stats/templates/images/os.png" alt="" /></a>
					<br /><a href="admin_stats.php?os=1#stats">{L_OS}</a>		
				</td>
				<td style="width:20%;padding:15px;" class="row1">
					<a href="admin_stats.php?lang=1#stats"><img src="{PATH_TO_ROOT}/stats/templates/images/countries.png" alt="" /></a>
					<br /><a href="admin_stats.php?lang=1#stats">{L_LANG}</a>
				</td>				
				<td style="width:20%;padding:15px;" class="row1">
					<a href="admin_stats.php?referer=1#stats"><img src="{PATH_TO_ROOT}/stats/templates/images/referer.png" alt="" /></a>
					<br /><a href="admin_stats.php?referer=1#stats">{L_REFERER}</a>
				</td>
				<td style="width:20%;padding:15px;" class="row1">
					<a href="admin_stats.php?keyword=1#stats"><img src="{PATH_TO_ROOT}/stats/templates/images/keyword.png" alt="" /></a>
					<br /><a href="admin_stats.php?keyword=1#stats">{L_KEYWORD}</a>
				</td>
				<td style="width:20%;padding:15px;" class="row1">
					<a href="admin_stats.php?bot=1#stats"><img src="{PATH_TO_ROOT}/stats/templates/images/robots.png" alt="" /></a>
					<br /><a href="admin_stats.php?bot=1#stats">{L_ROBOTS}</a>
				</td>	
			</tr>
		</table>
		
		<br /><br />
		
		# IF C_STATS_SITE #
		<table class="module-table">
			<tr>
				<th colspan="3">
					{L_SITE}
				</th>
			</tr>
			<tr>
				<td class="row1">
					{L_START}: <strong>{START}</strong>
				</td>		
			</tr>
			<tr>
				<td class="row1">
					{L_KERNEL_VERSION} : <strong>{VERSION}</strong>
				</td>		
			</tr>	
		</table>
		# ENDIF #
		
		
		# IF C_STATS_USERS #
		<table class="module-table">
			<tr>
				<th colspan="2">	
					{L_USERS}
				</th>
			</tr>
			<tr>
				<td class="row1" style="text-align:center;width:25%;">
					{L_USERS}
				</td>
				<td class="row2">
					{USERS}
				</td>
			 </tr>
			<tr>
				<td class="row1" style="text-align:center;width:50%;">
					{L_LAST_USER}
				</td>
				<td class="row2">
					<a href="{U_LAST_USER_PROFILE}" class="{LAST_USER_LEVEL_CLASS}" # IF C_LAST_USER_GROUP_COLOR # style="color:{LAST_USER_GROUP_COLOR}" # ENDIF #>{LAST_USER}</a>
				</td>
			</tr>
		</table>
		<br /><br />
		<table class="module-table">
			<tr>
				<th colspan="2">	
					{L_TEMPLATES}
				</th>
			</tr>
			<tr>
				<td style="width:50%;padding-top:30px;vertical-align:top" class="row1">
					<table class="module-table">						
						<tr>
							<th class="center">		
								{L_TEMPLATES} 
							</th>
							<th class="center" style="width:30px">		
								{L_COLORS}
							</th>
							<th class="center">
								{L_USERS}
							</th>				
						</tr>
						
						# START templates #	
						<tr>
							<td style="text-align:center;" class="row2">			
								{templates.THEME} <span class="smaller">({templates.PERCENT}%)</span>
							</td>							
							<td style="text-align:center;" class="row2">			
								<div style="margin:auto;width:10px;margin:auto;height:10px;background:{templates.COLOR};border:1px solid black;"></div>
							</td>
							<td style="text-align:center;" class="row2">
								{templates.NBR_THEME}
							</td>				
						</tr>
						# END templates #		
					</table>
				</td>
				<td style="text-align:center;padding-top:30px;vertical-align:top" class="row1">
					<img src="display_stats.php?theme=1" alt="" />
				</td>
			</tr>
		</table>
		<br /><br />
		<table class="module-table">
			<tr>
				<th colspan="2">	
					{L_SEX}
				</th>
			</tr>
			<tr>
				<td style="width:50%;padding-top:30px;vertical-align:top" class="row1">
					<table class="module-table">						
						<tr>
							<th class="center">		
								{L_SEX} 
							</th>
							<th class="center" style="width:30px">			
								{L_COLORS}
							</th>
							<th class="center">
								{L_USERS}
							</th>				
						</tr>
						
						# START sex #	
						<tr>
							<td style="text-align:center;" class="row2">			
								{sex.SEX} <span class="smaller">({sex.PERCENT}%)</span>
							</td>							
							<td style="text-align:center;" class="row2">			
								<div style="margin:auto;width:10px;margin:auto;height:10px;background:{sex.COLOR};border:1px solid black;"></div>
							</td>
							<td style="text-align:center;" class="row2">
								{sex.NBR_MBR}
							</td>				
						</tr>
						# END sex #	
						
					</table>
				</td>
				<td style="text-align:center;padding-top:30px;vertical-align:top" class="row1">
					{GRAPH_RESULT_SEX}
				</td>
			</tr>
		</table>
		<br /><br />
		<table class="module-table">
			<tr>
				<th colspan="3">	
					{L_TOP_TEN_POSTERS}
				</th>
			</tr>
			<tr>
				<td class="row1" style="text-align:center;">
					N&deg;
				</td>
				<td class="row1" style="text-align:center;">
					{L_PSEUDO}
				</td>
				<td class="row1" style="text-align:center;">
					{L_MSG}
				</td>
			</tr>
			# START top_poster #			
			<tr>
				<td class="row2" style="text-align:center;">
					{top_poster.ID}
				</td>
				<td class="row2" style="text-align:center;">
					<a href="{top_poster.U_USER_PROFILE}" class="{top_poster.USER_LEVEL_CLASS}" # IF top_poster.C_USER_GROUP_COLOR # style="color:{top_poster.USER_GROUP_COLOR}" # ENDIF #>{top_poster.LOGIN}</a>
				</td>
				<td class="row2" style="text-align:center;">
					{top_poster.USER_POST}
				</td>
			</tr>			
			# END top_poster #
		</table>
		# ENDIF #
		
		
		# IF C_STATS_VISIT #
		<form action="admin_stats.php#stats" method="get">
			<table class="module-table">
				<tr>
					<th>
						{L_VISITORS} {MONTH} {U_YEAR}
					</th>
				</tr>
				<tr>
					<td class="row2" style="text-align:center;">
						<div style="width:50%;text-align:center;margin:auto">
							<p class="text-strong">{L_TOTAL}: {VISIT_TOTAL} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {L_TODAY}: {VISIT_DAY}</p>
							<a href="admin_stats{U_PREVIOUS_LINK}#stats">&laquo;</a>&nbsp;&nbsp;&nbsp;&nbsp;
							# IF C_STATS_DAY #
							<select name="d">
								{STATS_DAY}
							</select>
							# ENDIF #
							# IF C_STATS_MONTH #
							<select name="m">
								{STATS_MONTH}
							</select>
							# ENDIF #
							# IF C_STATS_YEAR #
							<select name="y">
								{STATS_YEAR}
							</select>
							# ENDIF #
							<input type="hidden" name="{TYPE}" value="1">
							<button type="submit" name="date" value="true">{L_SUBMIT}</button>
							&nbsp;&nbsp;&nbsp;&nbsp;
							<a href="admin_stats{U_NEXT_LINK}#stats">&raquo;</a>				
						</div>
						<br />
						# IF C_STATS_NO_GD #
						<br />
						<table class="module-table" style="width:400px;margin:auto;">
							<tr>
								<td style="background-color: #000000;width:1px;"></td>
								<td style="height:200px;width:10px;vertical-align:top;text-align:center;font-size:9px;">
									{MAX_NBR}
								</td>
									
								# START values #								
								<td style="height:200px;width:10px;vertical-align:bottom;">
									<table class="module-table" style="width:14px;margin:auto;">
										# START head #
										<tr>
											<td style="margin-left:2px;width:10px;height:4px;background-image: url({PATH_TO_ROOT}/stats/templates/images/stats2.png); background-repeat:no-repeat;">
											</td>
										</tr>
										# END head #
										<tr>
											<td style="margin-left:2px;width:10px;height:{values.HEIGHT}px;background-image: url({PATH_TO_ROOT}/stats/templates/images/stats.png);background-repeat:repeat-y;padding:0px">
											</td>
										</tr>
									</table>
								</td>	
								# END values #
								
								# START end_td #							
									{end_td.END_TD}							
								# END end_td #
							</tr>
							<tr>
								<td style="background-color: #000000;width:1px"></td>
								<td style="width:10px;font-size:9px;">
									0
								</td>								
								# START legend #								
								<td style="text-align:center;width:13px;font-size:9px;">
									{legend.LEGEND}
								</td>								
								# END legend #								
							</tr>
							<tr>
								<td style="height:1px;background-color: #000000;" colspan="{COLSPAN}"></td>
							</tr>
						</table>
						<br />
						# ENDIF #
						
						{GRAPH_RESULT}
					</td>
				</tr>
				<tr>
					<td class="row3" style="text-align:center;" colspan="{COLSPAN}">
						{L_TOTAL}: {SUM_NBR}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{L_AVERAGE}: {MOY_NBR}
					</td>
				</tr>
				<tr>
					<td class="row3" style="text-align:center;">
						{U_VISITS_MORE}
					</td>
				</tr>
			</table>
		</form>	
		<br /><br />
		<table class="module-table" style="width:300px;">
			<tr>
				<th style="width:50%">
					{L_DAY}
				</th>
				<th style="width:50%">
					{L_VISITS_DAY}
				</th>
			</tr>			
			# START value #
			<tr>
				<td class="row3" style="font-size:10px;width:50%">
					{value.U_DETAILS}
				</td>
				<td class="row3" style="font-size:10px;width:50%">
					{value.NBR}
				</td>
			</tr>		
			# END value #
		</table>
		# ENDIF #


		# IF C_STATS_BROWSERS #
		<table class="module-table">
			<tr>
				<th colspan="3">
					{L_BROWSERS}
				</th>
			</tr>
			<tr>
				<td class="row1" style="width:50%;padding-top:30px;vertical-align:top">
					<table class="module-table">
						# START list #			
						<tr style="height:35px;">
							<td style="text-align:center;" class="row2">	
								{list.IMG}
							</td>
							<td style="text-align:center;" class="row2">			
								<div style="margin:auto;width:10px;height:10px;margin:auto;background:{list.COLOR};border:1px solid black;"></div>
							</td>
							<td style="text-align:center;" class="row2">
								 {list.L_NAME} <span class="smaller">({list.PERCENT}%)</span>
							</td>				
						</tr>
						# END list #
					</table>
				</td>
				<td style="text-align:center;padding-top:30px;vertical-align:top" class="row1">
					{GRAPH_RESULT}
				</td>
			</tr>
		</table>
		# ENDIF #


		# IF C_STATS_OS #
		<table class="module-table">
			<tr>
				<th colspan="3">
					{L_OS}
				</th>
			</tr>
			<tr style="height:35px;">
				<td class="row1" style="width:50%;padding-top:30px;vertical-align:top">
					<table class="module-table">
						# START list #			
						<tr style="height:35px;">
							<td style="text-align:center;" class="row2">		
								{list.IMG}
							</td>
							<td style="text-align:center;" class="row2">			
								<div style="margin:auto;width:10px;height:10px;background:{list.COLOR};border:1px solid black;"></div>
							</td>
							<td style="text-align:center;" class="row2">
								{list.L_NAME} <span class="smaller">({list.PERCENT}%)</span>
							</td>				
						</tr>
						# END list #
					</table>
				</td>
				<td style="text-align:center;padding-top:30px;vertical-align:top" class="row1">
					{GRAPH_RESULT}
				</td>
			</tr>
		</table>
		# ENDIF #

		
		# IF C_STATS_LANG #
		<table class="module-table">
			<tr>
				<th colspan="3">
					{L_LANG}
				</th>
			</tr>
			<tr style="height:35px;">
				<td class="row1" style="width:50%;padding-top:30px;vertical-align:top">
					<table class="module-table">
						# START list #			
						<tr>
							<td style="text-align:center;" class="row2">			
								{list.IMG}
							</td>
							<td style="text-align:center;" class="row2">			
								<div style="margin:auto;width:10px;margin:auto;height:10px;background:{list.COLOR};border:1px solid black;"></div>
							</td>
							<td style="text-align:center;" class="row2">
								{list.L_NAME} <span class="smaller">({list.PERCENT}%)</span>
							</td>				
						</tr>
						# END list #
					</table>
				</td>
				<td style="text-align:center;padding-top:30px;vertical-align:top" class="row1">
					{GRAPH_RESULT}
				</td>
			</tr>
			<tr>
				<td colspan="3" style="text-align:center;">
					{L_LANG_ALL}
				</td>
			</tr>
		</table>
		# ENDIF #
		

		# IF C_STATS_REFERER #
		<script type="text/javascript">
		<!--
		function XMLHttpRequest_referer(divid)
		{
			if( document.getElementById('url' + divid).style.display == 'table' )
			{
				display_div_auto('url' + divid, 'table');
				document.getElementById('img_url' + divid).className = 'icon-plus-square-o';
			}
			else
			{
				var xhr_object = null;
				var filename = '{PATH_TO_ROOT}/kernel/framework/ajax/stats_xmlhttprequest.php?stats_referer=1&id=' + divid;
				var data = null;
				
				if(window.XMLHttpRequest) // Firefox
				   xhr_object = new XMLHttpRequest();
				else if(window.ActiveXObject) // Internet Explorer
				   xhr_object = new ActiveXObject("Microsoft.XMLHTTP");
				else // XMLHttpRequest non support� par le navigateur
					return;
				
				document.getElementById('load' + divid).innerHTML = '<i class="icon-spinner icon-spin"></i>';
				
				xhr_object.open("POST", filename, true);
				xhr_object.onreadystatechange = function() 
				{
					if( xhr_object.readyState == 4 && xhr_object.status == 200 && xhr_object.responseText != '' )
					{	
						display_div_auto('url' + divid, 'table');
						document.getElementById('url' + divid).innerHTML = xhr_object.responseText;
						document.getElementById('load' + divid).innerHTML = '';
						document.getElementById('img_url' + divid).className = 'icon-minus-square-o';
					}
					else if( xhr_object.readyState == 4 && xhr_object.responseText == '' )
						document.getElementById('load' + divid).innerHTML = '';
				}
				xmlhttprequest_sender(xhr_object, null);
			}
		}
		-->
		</script>
		
		<table class="module-table">
			<tr>
				<th>			
					{L_REFERER}
				</th>
				<th style="width:100px;">
					{L_TOTAL_VISIT}
				</th>
				<th style="width:100px;">
					{L_AVERAGE_VISIT}
				</th>
				<th style="width:90px;">
					{L_LAST_UPDATE}
				</th>	
				<th style="width:93px;">
					{L_TREND}
				</th>
			</tr>
			# START referer_list #	
			<tr>
				<td class="row2" style="padding:0px;margin:0px;" colspan="5">			
					<table style="width:100%;border:none;border-collapse:collapse;">
						<tr>
							<td style="text-align:center;">		
								{referer_list.IMG_MORE} <span class="smaller">({referer_list.NBR_LINKS})</span> <a href="{referer_list.URL}">{referer_list.URL}</a>	<span id="load{referer_list.ID}"></span>	 			
							</td>
							<td style="width:112px;text-align:center;">
								{referer_list.TOTAL_VISIT}
							</td>
							<td style="width:112px;text-align:center;">
								{referer_list.AVERAGE_VISIT}
							</td>
							<td style="width:102px;text-align:center;">
								{referer_list.LAST_UPDATE}
							</td>
							<td style="width:105px;">
								{referer_list.TREND}
							</td>
						</tr>
					</table>
					<div id="url{referer_list.ID}" style="display:none;width:100%;"></div>					
				</td>	
			</tr>
			# END referer_list #
			<tr>
				<td class="row3" colspan="5" style="text-align:center;">
					{PAGINATION}&nbsp;
				</td>
			</tr>
		</table>
		# ENDIF #
		
		
		# IF C_STATS_KEYWORD #
		<script type="text/javascript">
		<!--
		function XMLHttpRequest_referer(divid)
		{
			if( document.getElementById('url' + divid).style.display == 'table' )
			{
				display_div_auto('url' + divid, 'table');
				document.getElementById('img_url' + divid).className = 'icon-plus-square-o';
			}
			else
			{
				document.getElementById('load' + divid).innerHTML = '<i class="icon-spinner icon-spin"></i>';
				var xhr_object = xmlhttprequest_init('{PATH_TO_ROOT}/kernel/framework/ajax/stats_xmlhttprequest.php?token={TOKEN}&stats_keyword=1&id=' + divid);
				xhr_object.onreadystatechange = function() 
				{
					if( xhr_object.readyState == 4 && xhr_object.status == 200 && xhr_object.responseText != '' )
					{	
						display_div_auto('url' + divid, 'table');
						document.getElementById('url' + divid).innerHTML = xhr_object.responseText;
						document.getElementById('load' + divid).innerHTML = '';
						document.getElementById('img_url' + divid).className = 'icon-minus-square-o';
					}
					else if( xhr_object.readyState == 4 && xhr_object.responseText == '' )
						document.getElementById('load' + divid).innerHTML = '';
				}
				xmlhttprequest_sender(xhr_object, null);
			}
		}
		-->
		</script>
		
		<table class="module-table">
			<tr>
				<th>			
					{L_KEYWORD}
				</th>
				<th style="width:100px;">
					{L_TOTAL_VISIT}
				</th>
				<th style="width:100px;">
					{L_AVERAGE_VISIT}
				</th>
				<th style="width:90px;">
					{L_LAST_UPDATE}
				</th>	
				<th style="width:93px;">
					{L_TREND}
				</th>
			</tr>
			# START keyword_list #	
			<tr>
				<td class="row2" style="padding:0px;margin:0px;" colspan="5">			
					<table style="width:100%;border:none;border-collapse:collapse;">
						<tr>
							<td style="text-align:center;">		
								{keyword_list.IMG_MORE} <span class="smaller">({keyword_list.NBR_LINKS})</span> {keyword_list.KEYWORD}	<span id="load{keyword_list.ID}"></span>	 			
							</td>
							<td style="width:112px;text-align:center;">
								{keyword_list.TOTAL_VISIT}
							</td>
							<td style="width:112px;text-align:center;">
								{keyword_list.AVERAGE_VISIT}
							</td>
							<td style="width:102px;text-align:center;">
								{keyword_list.LAST_UPDATE}
							</td>
							<td style="width:105px;">
								{keyword_list.TREND}
							</td>
						</tr>
					</table>
					<div id="url{keyword_list.ID}" style="display:none;width:100%;"></div>					
				</td>	
			</tr>
			# END keyword_list #
			<tr>
				<td class="row3" colspan="5" style="text-align:center;">
					{PAGINATION}&nbsp;
				</td>
			</tr>
		</table>
		# ENDIF #
		
		
		# IF C_STATS_ROBOTS #
		<form action="admin_stats.php?bot=1#stats" name="form" method="post" style="margin:auto;" onsubmit="return check_form();">
			<table class="module-table">
				<tr> 
					<th colspan="2">
						{L_ROBOTS}
					</th>
				</tr>
				<tr> 
					<td style="width:50%;padding-top:30px;vertical-align:top" class="row1">
						<table class="module-table">						
							<tr>
								<th style="text-align:center">
									{L_ROBOTS} 
								</th>
								<th style="text-align:center">
									{L_COLORS}
								</th>
								<th style="text-align:center">		
									{L_VIEW_NUMBER}
								</th>				
							</tr>
							
							# START list #	
							<tr style="height:35px;">
								<td class="text_center row2">
									 {list.L_NAME}  <span class="smaller">({list.PERCENT}%)</span>
								</td>
								<td class="text_center row2">			
									<div style="margin:auto;width:10px;margin:auto;height:10px;background:{list.COLOR}"></div>
								</td>
								<td class="text_center row2">			
									{list.VIEWS}
								</td>				
							</tr>
							# END list #		
						</table>
					</td>
					<td style="text-align:center;padding-top:30px;vertical-align:top" class="row1">
						<img src="display_stats.php?bot=1" alt="" />
					</td>
				</tr>
			</table>
			<br /><br />
			<fieldset class="fieldset-submit">
				<legend>{L_ERASE_RAPPORT}</legend>
				<button type="submit" name="erase" value="true">{L_ERASE_RAPPORT}</button> 
			</fieldset>
		</form>
		# ENDIF #
		
		<br /><br />
		<form action="admin_stats.php?token={TOKEN}" method="post" class="fieldset-content">
				<fieldset>
					<legend>
						{L_AUTHORIZATIONS}
					</legend>
					<div class="form-element">
						
							<label>
								{L_READ_AUTHORIZATION}
							</label>
						
						<div class="form-field">
							{READ_AUTHORIZATION}
						</div>
					</div>
				</fieldset>
								
				<fieldset class="fieldset-submit">
					<button type="submit" name="valid" value="true">{L_UPDATE}</button>
					&nbsp;&nbsp; 
					<button type="reset" value="true">{L_RESET}</button>
				</fieldset>	
			</form>
	</div>
	