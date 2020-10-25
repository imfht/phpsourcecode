			<div id="main">
				<div class="title">聊天室</div>
				{INFO}
				<div class="module">点击<a href="{UPDATE}">这里</a>刷新聊天消息</div>
<!-- BEGIN switch_user_logged_out -->
				<p><font color="red">您好，你还没有登录！请 <b><a href="{U_LOGIN}">登录</a></b> 后再发言！</font></p>
<!-- END switch_user_logged_in -->

<!-- BEGIN switch_user_logged_in -->
				<form action="{U_SHOUTBOX}" method="post" name="post">
					<div class="module">
						<textarea id="text" name="message" tabindex="3" style="width:99%"></textarea><br />
						{S_HIDDEN_FORM_FIELDS}
						{SMILES_SELECT}
						<input type="submit" name="submit" value="发表" />
					</div>
				</form>
<!-- END switch_user_logged_in -->

<!-- BEGIN shoutrow -->
				<div class="module {shoutrow.ROW_CLASS}">
					{shoutrow.SHOUT_USERNAME}{shoutrow.POSTER_ONLINE_STATUS}说：{shoutrow.SHOUT} ---{TIME}
				</div>
<!-- END shoutrow -->
				{PAGINATION}
				<div class="nav"><a href="mods.php">返回上级</a> / <a href="{U_INDEX}">返回首页</a></div>
			</div>