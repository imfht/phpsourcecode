<?php
include $_SERVER['DOCUMENT_ROOT']."/libs/function.php";				
$loginfo = $database->select("log", "*", ["id[=]" =>$_GET['sid']]);
session();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>查看日志</title>
	<meta name="renderer" content="webkit">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="format-detection" content="telephone=no">
	<link rel="stylesheet" href="/src/layui/css/layui.css" media="all" />
</head>
<body class="childrenBody">
	<form class="layui-form" style="width:80%;"id="editsiteform">
		<div class="layui-form-item">
			<div class="layui-inline">		
				<label class="layui-form-label">ID</label>
				<div class="layui-input-inline">
					 <input type="text" id="id" name="id" value="<?php echo $loginfo[0]["id"]?>" disabled lay-verify="required" placeholder="" class="layui-input">
				</div>
			</div>
		<div class="layui-inline">		
				<label class="layui-form-label">日期</label>
				<div class="layui-input-inline">
					 <input type="text" name="name" value="<?php echo $loginfo[0]["data"]?>"  lay-verify="required" placeholder="" class="layui-input">
				</div>
			</div>	
		</div>
		<div class="layui-form-item">
			<label class="layui-form-label">日志信息</label>
			<div class="layui-input-block">
				<textarea placeholder="日志信息" name="work" class="layui-textarea linksDesc"><?php echo $loginfo[0]["info"]?></textarea>
			</div>
		</div>
	</form>
</body>
</html>