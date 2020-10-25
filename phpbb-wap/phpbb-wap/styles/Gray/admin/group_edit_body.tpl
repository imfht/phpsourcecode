			<div id="main">	
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级面板导航</a>&gt;<a href="{U_ADMIN_GROUPS}">小组列表</a>&gt;{L_TITLE}</div>
				<div class="title">{L_TITLE}</div>
				<form action="{S_GROUP_ACTION}" method="post" name="post">
					<div>
						<label>小组的名称：</label>
						<div><input type="text" name="group_name" maxlength="40" value="{GROUP_NAME}" /></div>
					</div>
					<div>
						<label>小组的描述：</label>
						<div><textarea name="group_description" rows="5" style="width:99%;">{GROUP_DESCRIPTION}</textarea></div>
					</div>
					<div>
						<label>小组的Logo：</label>
						<p>您可以选择系统目录中的图片或网络中的图片</p>
						<div><input type="text" name="group_logo" maxlength="255" value="{GROUP_LOGO}" /></div>
					</div>
					<div>
						<label>小组的版主：</label>
						<div><input type="text" name="username" maxlength="50" value="{GROUP_MODERATOR}" /></div>
					</div>
					<div>
						<label>小组的状态：</label>
						<div><input type="radio" name="group_type" value="{S_GROUP_OPEN_TYPE}" {S_GROUP_OPEN_CHECKED} /> 开放</div>
						<div><input type="radio" name="group_type" value="{S_GROUP_CLOSED_TYPE}" {S_GROUP_CLOSED_CHECKED} /> 关闭</div>
						<div><input type="radio" name="group_type" value="{S_GROUP_HIDDEN_TYPE}" {S_GROUP_HIDDEN_CHECKED} /> 隐藏</div>
					</div>
<!-- BEGIN group_edit -->
					<div>
						<p>如果您更改小组管理员而且勾选这个选项会将原有的小组管理员从小组中移除，如不勾选，这个用户将成为小组的普通成员</p>
						<div><input type="checkbox" name="delete_old_moderator" value="1" /> 删除原有小组版主</div>
					</div>
					<div><input type="checkbox" name="group_guestbook" value="1" {S_GROUP_GB_ENABLE} /> 开启小组留言板</div>
					<div><input type="checkbox" name="group_delete" value="1" /> 删除小组</div>
<!-- END group_edit -->
					{S_HIDDEN_FIELDS}
					<input type="submit" name="group_update" value="保存" />
				</form>
			</div>