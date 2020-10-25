			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级面板导航</a>&gt;{L_AUTH_TITLE}</div>
				<form method="post" action="{S_AUTH_ACTION}">
					<div class="title">{L_AUTH_SELECT}</div>
					<div class="module">
						{S_AUTH_SELECT}
<!-- BEGIN not_group -->
						{S_HIDDEN_FIELDS}
						<input type="submit" value="选择" />
<!-- END not_group -->

<!-- BEGIN forum_auth_select -->
						<input type="submit" value="选择" />
<!-- END forum_auth_select -->
					</div>
				</form>
			</div>