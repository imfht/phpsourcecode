<?php
include $_SERVER['DOCUMENT_ROOT']."/libs/function.php";
session();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>添加分类</title>
	<meta name="renderer" content="webkit">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="format-detection" content="telephone=no">
	<link rel="stylesheet" href="/src/layui/css/layui.css" media="all" />
</head>
<body class="childrenBody">
	<form class="layui-form" style="width:80%;" id="addtype">
		<br/>
		<div class="layui-form-item">
			<div class="layui-inline">		
				<label class="layui-form-label">分类名称</label>
				<div class="layui-input-inline">
					 <input type="text" name="name"  lay-verify="required" placeholder="请输入分类名称" class="layui-input">
				</div>
			</div>
		</div>
		<div class="layui-form-item">
			<div class="layui-input-block">
				<a class="layui-btn" lay-submit="" lay-filter="addtype">立即提交</a>
				<button type="reset" class="layui-btn layui-btn-primary">重置</button>
		    </div>
		</div>
	</form>
	<script type="text/javascript" src="/src/layui/layui.js"></script>
	<script type="text/javascript" src="add.js"></script>
</body>
</html>