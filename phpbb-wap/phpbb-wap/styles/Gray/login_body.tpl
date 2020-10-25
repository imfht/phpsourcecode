			<div id="main">
				<div class="title">会员登录</div>
				<form action="{S_LOGIN_ACTION}" method="post" target="_top">
					<div id="box">
<!-- BEGIN login -->					
						<div>
							<label>帐号</label>
							&nbsp;&nbsp;
							<input class="input" type="text" name="username" value="{USERNAME}" onfocus="this.value=''" onblur="if(this.value==''){this.value='用户名或邮箱'}" />
						</div>
<!-- END login -->
<!-- BEGIN admin -->
						<p>当你使用后台面板的功能时，系统需要重新验证您的密码！</p>
						<br />
						<input type="hidden" name="username" value="{USERNAME}" />

<!-- END admin -->
						<br />
						<div>
							<label>密码</label>
							&nbsp;&nbsp;
							<input class="input" type="password" name="password" onfocus="this.value=''"/>
						</div>
						<input type="hidden" name="autologin" value="0" />
						{S_HIDDEN_FIELDS}
						<br />
						<input type="hidden" name="login" value="ture" />
						<div class="center">
							<input class="button" type="submit" value="登录" />
							&nbsp;&nbsp;
							<a href="{U_SEND_PASSWORD}">忘记密码？</a>
							&nbsp;&nbsp;
							<a href="{U_REGISTER}">注册会员</a>
							&nbsp;&nbsp;
							
						</div>
<!-- BEGIN login -->
						<br />
						<hr />
						<div><a href="{U_QQ_LOGIN}"><img src="./mods/qqlogin/images/qq_login.png" />QQ登录</a></div>
<!-- END login -->
					</div>
				</form>
				<div class="nav">
					<a href="{U_INDEX}">返回上级</a> / <a href="{U_INDEX}">返回首页</a>
				</div>
			</div>