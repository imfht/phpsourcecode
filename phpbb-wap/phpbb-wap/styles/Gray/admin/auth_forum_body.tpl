			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级面板导航</a>&gt;<a href="{U_AUTH_SELECT}">选择论坛</a>&gt;{FORUM_NAME}的权限</div>
				<p>在这个选项中您可以更改论坛的使用权限。您可以选择使用简单或是高级两种模式，高级模式能提供您完整的权限设定控制。请注意，所有的改变都将会影响到用户的论坛使用权限。</p>
				<form method="post" action="{S_FORUMAUTH_ACTION}">
					<div class="title">{FORUM_NAME}{U_SWITCH_MODE}</div>
<!-- BEGIN forum_auth_titles -->
					<div class="{forum_auth_titles.ROW_CLASS} module">
						{forum_auth_titles.CELL_TITLE}：{forum_auth_titles.S_AUTH_LEVELS_SELECT}
					</div>
<!-- END forum_auth_titles -->
					{S_HIDDEN_FIELDS}
					<div class="center">
						<input type="submit" name="submit" value="保存权限" />
					</div>
				</form>
			</div>