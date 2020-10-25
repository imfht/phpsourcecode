<?php
include $_SERVER['DOCUMENT_ROOT']."/libs/function.php";
session();//权限控制
$userinfo = $database->select("admin", "*", ["uname[=]" =>  $_COOKIE['username']]); 
?>
<script language="javascript" >
var oldpwd=<?php echo json_encode($userinfo[0]['password']);?>; //提取旧密码并赋值给js变量
</script>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>修改密码</title>
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
	<form class="layui-form changePwd">
		<div style="margin:0 0 15px 110px;color:#f00;"></div>
		<div class="layui-form-item">
		    <label class="layui-form-label">用户名</label>
		    <div class="layui-input-block">
		    	<input type="text" id="uname" value="<?php print_r($userinfo[0]["uname"]) ?>" disabled class="layui-input layui-disabled">
		    </div>
		</div>
		<div class="layui-form-item">
		    <label class="layui-form-label">旧密码</label>
		    <div class="layui-input-block">
		    	<input type="password" value="" placeholder="请输入旧密码" lay-verify="required|oldPwd" class="layui-input pwd">
		    </div>
		</div>
		<div class="layui-form-item">
		    <label class="layui-form-label">新密码</label>
		    <div class="layui-input-block">
		    	<input type="password" value="" placeholder="请输入新密码" lay-verify="required|newPwd" id="oldPwd" class="layui-input pwd">
		    </div>
		</div>
		<div class="layui-form-item">
		    <label class="layui-form-label">确认密码</label>
		    <div class="layui-input-block">
		    	<input type="password" value="" id="pwd" placeholder="请确认密码" lay-verify="required|confirmPwd" class="layui-input pwd">
		    </div>
		</div>
		<div class="layui-form-item">
		    <div class="layui-input-block">
		    	<button class="layui-btn" lay-submit="" lay-filter="changePwd">立即修改</button>
				<button type="reset" class="layui-btn layui-btn-primary">重置</button>
		    </div>
		</div>
	</form>
	<script type="text/javascript" src="/src/layui/layui.js"></script>
	<script type="text/javascript" src="md5.js"></script>
	<script type="text/javascript" src="user.js"></script>
</body>
</html>