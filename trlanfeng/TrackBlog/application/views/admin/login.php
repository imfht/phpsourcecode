<!DOCTYPE html>
<html>
<head lang="en">
	<meta charset="UTF-8">
	<title>Login Page | Amaze UI Example</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta name="format-detection" content="telephone=no">
	<meta name="renderer" content="webkit">
	<meta http-equiv="Cache-Control" content="no-siteapp" />
	<link rel="alternate icon" type="image/png" href="/public/i/favicon.png">
	<link rel="stylesheet" href="/public/css/amazeui.min.css"/>
	<style>
		.header {
			text-align: center;
		}
		.header h1 {
			font-size: 200%;
			color: #333;
			margin-top: 30px;
		}
		.header p {
			font-size: 14px;
		}
	</style>
</head>
<body>
<div class="header">
	<div class="am-g">
		<h1>TrackBlog 后台管理</h1>
		<p>World Is Beautiful !<br/>
            愉快的工作，愉快的生活！</p>
	</div>
	<hr />
</div>
<?php echo validation_errors(); ?>
<div class="am-g">
	<div class="am-u-lg-6 am-u-md-8 am-u-sm-centered">
		<form action="/index.php/admin/admin/check_login" method="post" class="am-form">
			<label for="email">用户名：</label>
            <input type="text" name="username" />
			<br>
			<label for="password">密码：</label>
            <input type="password" name="password" />
			<br>
			<label for="remember-me">
				<input id="remember-me" type="checkbox">
				记住密码
			</label>
			<br />
			<div class="am-cf">
				<input type="submit" name="submit" value="登 录" class="am-btn am-btn-primary am-btn-sm am-fl">
				<input type="submit" name="forgetPassword" value="忘记密码 ^_^? " class="am-btn am-btn-default am-btn-sm am-fr">
			</div>
		</form>
		<hr>
		<p>All rights reserved &copy; 2016 孤月蓝风<br/>

            Powered by TrackBlog  Design by Trlanfeng 孤月蓝风</p>
	</div>
</div>
</body>
</html>