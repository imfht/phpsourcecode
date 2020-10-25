<?php
include $_SERVER['DOCUMENT_ROOT']."/libs/function.php";
session();				
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>日志记录</title>
	<meta name="renderer" content="webkit">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="format-detection" content="telephone=no">
	<link rel="stylesheet" href="/src/layui/css/layui.css" media="all" />
</head>
<body class="childrenBody">
	<blockquote class="layui-elem-quote news_search">
		<div class="layui-inline">
		    <div class="layui-input-inline">
		    	<input type="text" value="" placeholder="请输入关键字" class="layui-input search_input">
		    </div>
		    <a class="layui-btn search_btn">查询</a>
		</div>
		<div class="layui-inline">
			<div class="layui-form-mid layui-word-aux"></div>
		</div>
	</blockquote>
	<div class="layui-form links_list">
	  	<table class="layui-table" lay-size="sm">
		    <colgroup>
				
				<col >
				<col>
				
				<col width="20%">
		    </colgroup>
		    <thead>
				<tr>
					<th>日期</th>
					<th>日志信息</th>
					<th>操作</th>
				</tr> 
		    </thead>
		    <tbody class="links_content"></tbody>
		</table>
	</div>
	<div id="page"></div>
	<script type="text/javascript" src="/src/layui/layui.js"></script>
	<script type="text/javascript" src="list.js"></script>
</body>
</html>