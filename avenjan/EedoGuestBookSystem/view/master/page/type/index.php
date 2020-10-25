<?php
include $_SERVER['DOCUMENT_ROOT']."/libs/function.php";
session();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>分类管理</title>
	<meta name="renderer" content="webkit">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="format-detection" content="telephone=no">
	<link rel="stylesheet" href="/src/layui/css/layui.css" media="all" />
	<style type="text/css">
		td,th{ text-align: center !important;}
	</style>
</head>
<body class="childrenBody">
	<blockquote class="layui-elem-quote news_search">
		<div class="layui-inline">
			<a class="layui-btn linksAdd_btn" style="background-color:#5FB878">添加分类</a>
		</div>
		<div class="layui-inline">
			<div class="layui-form-mid layui-word-aux"></div>
		</div>
	</blockquote>
	<div class="layui-form links_list">
	  	<table class="layui-table">
		    <colgroup>
				<col >
				<col width="15%">
				<col width="20%">
		    </colgroup>
		    <thead>
				<tr>
					<th>分类名称</th>
					<th>留言条数</th>
					<th>操作</th>
				</tr> 
		    </thead>
		    <tbody class="links_content"></tbody>
		</table>
	</div>
	<div id="page"></div>
	<blockquote class="layui-elem-quote news_search">
		<p>提示：<br/>如此分类下存在留言则该分类无法被删除！</p>
	</blockquote>
	<script type="text/javascript" src="/src/layui/layui.js"></script>
	<script type="text/javascript" src="list.js"></script>
</body>
</html>