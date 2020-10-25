			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级面板导航</a>&gt;<a href="{U_UG_SELECT}">{L_AUTH_TITLE}</a>&gt;{L_PERMISSIONS}</div>
				<div class="title">{USERNAME}</div>
				<form method="post" action="{S_AUTH_ACTION}">
<!-- BEGIN switch_user_auth -->
					<div id="admin-user-level">{USER_LEVEL}</div>
					<div id="admin-group-level">{USER_GROUP_MEMBERSHIPS}</div>
<!-- END switch_user_auth -->
	
<!-- BEGIN switch_group_auth -->
					<div>{GROUP_MEMBERSHIP}</div>
<!-- END switch_group_auth -->
	
<!-- BEGIN forums -->
					<div class="title">{forums.FORUM_NAME}</div>
	<!-- BEGIN aclvalues -->
						<div class="module bm-gray">{forums.aclvalues.L_UG_ACL_TYPE}：{forums.aclvalues.S_ACL_SELECT}</div>
	<!-- END aclvalues -->
					
					<div class="module">设为本论坛版主：{forums.S_MOD_SELECT}</div>
<!-- END forums -->
					{S_HIDDEN_FIELDS}
					<div class="center">
						{U_SWITCH_MODE} . <input type="submit" name="submit" value="保存修改" />
					</div>
				</form>
			</div>