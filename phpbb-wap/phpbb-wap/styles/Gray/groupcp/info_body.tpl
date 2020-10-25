			<div id="main">
				{ERROR_BOX}
				<div class="nav"><a href="./">首页</a>&gt;<a href="{U_GROUP_CP}">小组</a>&gt;{GROUP_NAME}</div>
				<form action="{S_GROUPCP_ACTION}" {S_FORM_ENCTYPE} method="post">
					<div class="title">{GROUP_NAME} 的资料</div>
					{CURRENT_LOGO}
					<div class="module bm-gray">
						名称：<strong>{GROUP_NAME}</strong>
					</div>
					<div class="module bm-gray">
						简介：{GROUP_DESC}
					</div>
					<div class="module bm-gray">
						您在该小组的状态：{GROUP_DETAILS}
					</div>
<!-- BEGIN switch_subscribe_group_input -->
					<input type="submit" name="joingroup" value="加入小组" />
<!-- END switch_subscribe_group_input -->
					
<!-- BEGIN switch_unsubscribe_group_input -->
					<input type="submit" name="unsub" value="退出小组" />
<!-- END switch_unsubscribe_group_input -->
<!-- BEGIN switch_mod_option -->
					<div class="module bm-gray">
						<div>
							小组状态设置：<br />
							<input type="radio" name="group_type" value="{S_GROUP_OPEN_TYPE}" {S_GROUP_OPEN_CHECKED} /> 开放式<br />
							<input type="radio" name="group_type" value="{S_GROUP_CLOSED_TYPE}" {S_GROUP_CLOSED_CHECKED} /> 封闭式<br />
							<input type="radio" name="group_type" value="{S_GROUP_HIDDEN_TYPE}" {S_GROUP_HIDDEN_CHECKED} /> 隐藏式<br />
							<input type="submit" name="groupstatus" value="更新" />
						</div>
					</div>
<!-- END switch_mod_option -->
					<div class="title">小组的管理员</div>
					<div class="module bm-gray">
						<a href="{U_MOD_VIEWPROFILE}">{MOD_USERNAME}</a>
						{MOD_PM}
						{MOD_EMAIL}
					</div>
<!-- BEGIN yeah -->
					<div class="title">小组的Logo</div>
					<div class="module bm-gray">
						{L_AVATAR_EXPLAIN}：<br />
						{AVATAR}
					</div>
	<!-- BEGIN switch_avatar_local_upload -->
					<input type="hidden" name="MAX_FILE_SIZE" value="{AVATAR_SIZE}" />
					<input type="file" name="avatar"/>
	<!-- END switch_avatar_local_upload -->
	<!-- BEGIN switch_avatar_local_upload_om -->
					<div class="module bm-gray">
						本地上传：<br />
						<input type="hidden" name="MAX_FILE_SIZE" value="{AVATAR_SIZE}" />
						<input name="fileupload" value = "">
						<a href="op:fileselect">浏览...</a>
					</div>
	<!-- END switch_avatar_local_upload_om -->
					<div class="module bm-gray">
						URL上传：<br />
						<input type="text" name="avatarurl"/>
						<input type="submit" name="groupicon" value="上传" />
					</div>
<!-- END yeah -->
					{S_HIDDEN_FIELDS}
				</form>
				<form action="{S_GROUPCP_ACTION}" method="post" name="post">
					<div class="title">小组成员</div>
<!-- BEGIN member_row -->
					<div class="module bm-gray">
	<!-- BEGIN switch_mod_option -->
						<input type="checkbox" name="members[]" value="{member_row.USER_ID}" />
	<!-- END switch_mod_option -->
						<a href="{member_row.U_VIEWPROFILE}">{member_row.USERNAME}</a>
						[贴子：{member_row.POSTS} - {member_row.PM} - {member_row.EMAIL}
					</div>
<!-- END member_row -->
<!-- BEGIN switch_no_members -->
					<div class="row1">该小组没有任何成员加入</div>
<!-- END switch_no_members -->
<!-- BEGIN switch_hidden_group -->
					<div class="row1">这是隐藏的小组，您不能查看成员</div>
<!-- END switch_hidden_group -->
<!-- BEGIN switch_mod_option -->
	<!-- BEGIN switch_no_members -->
					<input type="submit" name="remove" value="移除用户" />
	<!-- END switch_no_members -->
					<div class="title">添加用户</div>
					<div class="module bm-gray">
						请输入用户名：<br />
						<input type="text" name="username" maxlength="50" />
						<input type="submit" name="add" value="新增用户" />
					</div>
<!-- END switch_mod_option -->
					{S_HIDDEN_FIELDS}
				</form>
				{PAGINATION}
				<form action="{S_GROUPCP_ACTION}" method="post" name="post">
					{PENDING_USER_BOX}
					{S_HIDDEN_FIELDS}
				</form>
			</div>