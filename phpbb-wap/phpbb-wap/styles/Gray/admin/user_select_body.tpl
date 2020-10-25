			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级面板导航</a>&gt;{L_USER_TITLE}</div>
				<form method="post" name="post" action="{S_USER_ACTION}">
					<div class="title">{L_USER_SELECT}</div>
					<div id="box">
						<p>查看用户的权限，请输入会员的用户名</p>
						<br />
						<div>
							<label>用户名</label>
							&nbsp;&nbsp;
							<input class="input" type="text" name="username" value="" onfocus="this.value=''" onblur="if(this.value==''){this.value='请输入用户名'}" />
						</div>
						<input type="hidden" name="mode" value="edit" />
						{S_HIDDEN_FIELDS}
						<br />
						<div class="center">
							<input class="button" type="submit" name="submituser" value="提交" />
						</div>
					</div>
				</form>
			</div>