				<h1>{L_AUTH_TITLE}</h1>
				<h2>{L_USER_OR_GROUPNAME}: {USERNAME}</h2>
				<form method="post" action="{S_AUTH_ACTION}">
				<!-- BEGIN switch_user_auth -->
				<p>{USER_LEVEL}</p>
				<p>{USER_GROUP_MEMBERSHIPS}</p>
				<!-- END switch_user_auth -->
				<!-- BEGIN switch_group_auth -->
				<p>{GROUP_MEMBERSHIP}</p>
				<!-- END switch_group_auth -->
				<h2>{L_PERMISSIONS}</h2>
				<p>{L_AUTH_EXPLAIN}</p>
				<table cellspacing="1">
				<thead>
				<tr>
					<th>{L_FORUM}</th>
					<!-- BEGIN acltype -->
					<th>{acltype.L_UG_ACL_TYPE}</th>
					<!-- END acltype -->
					<th>{L_MODERATOR_STATUS}</th>
				</tr>
				</thead>
				<tbody>
				<!-- BEGIN forums -->
				<tr class="{forums.ROW_CLASS}">
					<td>{forums.FORUM_NAME}</td>
					<!-- BEGIN aclvalues -->
					<td>{forums.aclvalues.S_ACL_SELECT}</td>
						<!-- END aclvalues -->
					<td>{forums.S_MOD_SELECT}</td>
					</tr>
					<!-- END forums -->
				<tr class="row1">
					<td colspan="{S_COLUMN_SPAN}" style="text-align:center">{U_SWITCH_MODE}</td>
				</tr>
				</table>
				<fieldset class="submit-buttons">
					<legend>{L_SUBMIT}</legend>
					{S_HIDDEN_FIELDS}
					<input class="button1" type="submit" name="submit" value="{L_SUBMIT}" />&nbsp; 
					<input class="button2" type="reset" name="reset" value="{L_RESET}" />
				</fieldset>
				</form>