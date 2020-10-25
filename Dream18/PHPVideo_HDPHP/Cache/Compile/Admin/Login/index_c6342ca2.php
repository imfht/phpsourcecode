<?php if(!defined('HDPHP_PATH'))exit;C('SHOW_NOTICE',FALSE);?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<title>后台登录</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/jquery-1.11.0.js"></script>
<link rel="stylesheet" type="text/css" href="http://localhost/PHPUnion/Static/Pintuer/pintuer.css" />
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/pintuer.js"></script>
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/respond.js"></script>
</head>
<body>
<form action="<?php echo U('login');?>" method="POST">
	<table class="table table-striped table-bordered table-hover table-condensed">
		<tr>
			<td align="right">
				账户
			</td>
			<td>
				<input type="text" name="username" />
			</td>
		</tr>
		<tr>
			<td align="right">
				密码
			</td>
			<td>
				<input type="password" name="password" />
			</td>
		</tr>
		<tr>
			<td>
				
			</td>
			<td>
				<button class="button bg-main">立即登录</button>
				<a href="<?php echo U('Index/Index/index');?>">返回首页</a>
			</td>
		</tr>
	</table>
</form>
</body>
</html>