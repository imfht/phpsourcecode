<script type="text/javascript">
	//js代码存在着兼容问题，非IE浏览器不能复制
	function getValue()
	{
		var url = document.getElementById("url");
		window.clipboardData.setData("text", url.value);
	}
</script>
			<div id="main">
				<div class="nav"><a href="./">首页</a>&gt;<a href="rules.php">规则</a>&gt;<a href="rules.php?mode=faq">FAQ</a>&gt;{MAIN_TITLE}</div>
				<div class="title">{MAIN_TITLE}</div>
				<form action="rules.php?mode=faq&act=autologin" method="post">
					<p>您可以先复制下面输入框的网址：<br/>
					<input id="url" value="http://{SCRIPT_PATH}login.php?username=用户名&password=密码" />
					<input type="submit" value="复制网址" onclick="getValue()"/><br/>
					然后把用户名、密码替换成你的用户名、密码，然后保存成书签，这样你就不用每次输入密码才能登录了！
					</p>
				</form>
			</div>