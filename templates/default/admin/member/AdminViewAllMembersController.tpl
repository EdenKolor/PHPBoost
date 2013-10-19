		# INCLUDE FORM #
		
		{L_USERS_MANAGEMENT}
		<table>
			<thead>
				<tr>
					<th>
					</th>
					<th>
						<a href="{SORT_LOGIN_TOP}" class="sort-up"></a>
						{L_LOGIN} 
						<a href="{SORT_LOGIN_BOTTOM}" class="sort-down"></a>
					</th>
					<th>
						<a href="{SORT_LEVEL_TOP}" class="sort-up"></a>
						{L_LEVEL}
						<a href="{SORT_LEVEL_BOTTOM}" class="sort-down"></a>
					</th>
					<th>
						{L_MAIL}
					</th>
					<th>
						<a href="{SORT_REGISTERED_TOP}" class="sort-up"></a>
						{L_REGISTERED}
						<a href="{SORT_REGISTERED_BOTTOM}" class="sort-down"></a>
					</th>
					<th>
						<a href="{SORT_LAST_CONNECT_TOP}" class="sort-up"></a>
						{L_LAST_CONNECT}
						<a href="{SORT_LAST_CONNECT_BOTTOM}" class="sort-down"></a>
					</th>
					<th>
						<a href="{SORT_APPROBATION_TOP}" class="sort-up"></a>
						{L_APPROBATION}
						<a href="{SORT_APPROBATION_BOTTOM}" class="sort-down"></a>
					</th>
				</tr>
			</thead>
			# IF C_PAGINATION #
			<tfoot>
				<tr>
					<th colspan="7">
						{PAGINATION}
					</th>
				</tr>
			</tfoot>
			# ENDIF #
			<tbody>
				# START member_list #
				<tr> 
					<td> 
						<a href="{member_list.EDIT_LINK}" title="{L_UPDATE}" class="edit"></a>
						<a href="{member_list.DELETE_LINK}" title="{L_DELETE}" class="delete"></a>
					</td>
					<td>
						<a href="{member_list.U_PROFILE}" class="{member_list.LEVEL_CLASS}" # IF member_list.C_GROUP_COLOR # style="color:{member_list.GROUP_COLOR}" # ENDIF #>{member_list.LOGIN}</a>
					</td>
					<td> 
						{member_list.LEVEL}
					</td>
					<td>
						<a href="mailto:{member_list.MAIL}" class="small-button">Mail</a>
					</td>
					<td>
						{member_list.REGISTERED}
					</td>
					<td>
						{member_list.LAST_CONNECT}
					</td>
					<td>
						{member_list.APPROBATION}
					</td>
				</tr>
				# END member_list #
			</tbody>
		</table>
