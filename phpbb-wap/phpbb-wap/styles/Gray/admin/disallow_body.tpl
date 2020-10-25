			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级面板导航</a>&gt;敏感用户名</div>
				<p>在这里您可以禁止一些敏感的用户名，请注意：您无法禁用已经注册使用的用户名，您必须先删除或更改这个用户名，才能使用禁止管理员用户名的功能</p>
				<form method="post" action="{S_FORM_ACTION}">
					<div class="title">添加敏感用户名</div>
					<p>请输入用户名，您可以使用通配符 * 来禁用范围较大的用户名</p>
					<div>
						<input type="text" name="disallowed_user" size="30" />
						<input type="submit" name="add_name" value="添加" />
					</div>
					<div class="title">删除敏感用户名</div>
					<div>
						{S_DISALLOW_SELECT}
<!-- BEGIN not_disallowed -->
						<input type="submit" name="delete_name" value="删除" />
<!-- END not_disallowed -->
					</div>
				</form>
			</div>