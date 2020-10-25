<?php
include $_SERVER['DOCUMENT_ROOT']."/libs/function.php";
session();//权限控制
$userinfo = $database->select("admin", "*", ["uname[=]" =>  $_COOKIE['username']]);
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>个人资料</title>
	<meta name="renderer" content="webkit">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="format-detection" content="telephone=no">
	<link rel="stylesheet" href="/src/layui/css/layui.css" media="all" />
	<link rel="stylesheet" href="user.css" media="all" />
</head>
<body class="childrenBody">
	<form class="layui-form" id="editform">
		<input type="hidden" name="uname" value="<?php print_r($userinfo[0]["uname"]) ?>" class="layui-input layui-disabled">
		<div class="user_left">
			
			<div class="layui-form-item">
			    <label class="layui-form-label">姓名</label>
			    <div class="layui-input-block">
			    	<input type="text" name="name" value="<?php print_r($userinfo[0]["name"]) ?>" lay-verify="required" class="layui-input realName">
			    </div>
			</div>		
			<div class="layui-form-item">
			    <label class="layui-form-label">手机号码</label>
			    <div class="layui-input-block">
			    	<input type="tel" name="phone" value="<?php print_r($userinfo[0]["phone"]) ?>" placeholder="" lay-verify="required|phone" class="layui-input userPhone">
			    </div>
			</div>
			<div class="layui-form-item">
			    <label class="layui-form-label">邮箱</label>
			    <div class="layui-input-block">
			    	<input type="text" name="mail" value="<?php print_r($userinfo[0]["mail"]) ?>" placeholder="" lay-verify="required|email" class="layui-input userEmail">
			    </div>
			</div>
		</div>
		<div class="layui-form-item" style="margin-left: 5%;">
		    <div class="layui-input-block">
				<a class="layui-btn" lay-submit="" lay-filter="edit">确认修改</a>
				<button type="reset" class="layui-btn layui-btn-primary">重置</button>
		    </div>
		</div>
	</form>
	<script type="text/javascript" src="/src/layui/layui.js"></script>
	<script type="text/javascript" src="user.js"></script>
</body>
</html>