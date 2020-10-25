<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="x-ua-compatible" content="text/html;" />
		<title>LzCMS-博客版</title>
		<meta name="Keywords" content="LzCMS-博客版" />
		<meta name="Description" content="LzCMS-博客版" />
		<link rel="stylesheet" type="text/css" href="/static/css/install_style.css"/>
	</head>
	<body>
		<div class="content">
			<div class="con-head">
				<span class="update fr"><?php echo LZ_VERSION?></span>
			</div>
			<div class="con-body">
				<!--成功安装信息-->
				<div class="State">
					<h2>安装成功，欢迎您使用LzCMS-博客版</h2>
					<h4>请牢记您的管理员账号和密码用于网站后台登陆</h4>
					<li>管理员账号：<span class="name"><?php echo $_GET['admin_user']?></span></li>
				</div>
				<div class="btn mt85">
						<a class="agree-btn m195 fl" href="/index">进入前台</a>
						<a class="agree-btn3 fl" href="<?php echo url('admin/login/login')?>">进入后台</a>
				</div>
				<a class="state-bg bg-img fr"></a>
			</div>
		</div>
		<p class="copy">©2016-2017 lzcms.top (LzCMS)</p>
	</body>
</html>
