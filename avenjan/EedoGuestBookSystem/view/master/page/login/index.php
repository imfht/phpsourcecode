<?php 
/*
登录页面
BY avenjan
2018年7月26日
*/
include $_SERVER['DOCUMENT_ROOT']."/libs/function.php";
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>登陆 - <?php echo $system_sitename;?></title>
	<meta name="renderer" content="webkit">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="format-detection" content="telephone=no">
	<link rel="stylesheet" href="/src/layui/css/layui.css" media="all" />
	<link rel="stylesheet" href="login.css" media="all" />
	<script type="text/javascript" src="/src/js/jquery.js" ></script>
	<style type="text/css">
	body{
		background:url(bg.jpg); 
		background-size: cover;
		background-repeat: no-repeat;
  		
	}
		#verify{
			cursor: pointer;
		}
		.login{ text-align: center;color: #fff; }
	</style>
</head>
<body >
	<div class="video_mask" ></div>
	<div class="login">
	    <h1><?echo $system_sitename;?></h1>
	    	<div class="layui-form-item">
				<input class="layui-input" name="username" id="uname" value="" placeholder="用户名" lay-verify="required" type="text" autocomplete="off">
		    </div>
		    <div class="layui-form-item">
				<input class="layui-input" name="password" id="pwd" value="" placeholder="密码" lay-verify="required" type="password" autocomplete="off">
		    </div>
		    <div class="layui-form-item form_code" id="verify">
		    </div>
			<button class="layui-btn login_btn" id="btn" lay-submit="" >登录</button>
			<p><br/><?php echo $system_sitename." - ".$system_version ?><br/><br/> &copy; 2018 avenjan MIT license</p>
	</div>
<script type="text/javascript" src="/src/layui/layui.js"></script>
<script type="text/javascript" src="login.js"></script>
<script type="text/javascript" src="/src/js/verify.js" ></script>
</body>
</html>