<?php
include $_SERVER['DOCUMENT_ROOT']."/libs/function.php";
session();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>留言管理</title>
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
		<ul class="layui-nav layui-inline layui-bg-green" lay-filter="" style="cursor: pointer;">
		  <li class="layui-nav-item" style="line-height: 40px;>
		    <a href="javascript:;">筛选</a>
		    <dl class="layui-nav-child" id="export"> <!-- 二级菜单 -->
		      <dd><a href="index.php">全部</a></dd>
		      <dd><a href="?type=1">已审核</a></dd>
		      <dd><a href="?type=2">未审核</a></dd>
		    </dl>
		  </li>
		</ul>
		<!--div class="layui-inline">
			<a class="layui-btn layui-btn-danger batchDel">批量删除</a>
		</div-->
		<div class="layui-inline">
			<div class="layui-form-mid layui-word-aux"></div>
		</div>
	</blockquote>
	<div class="layui-form links_list">
	  	<table class="layui-table">
		    <colgroup>
		    	<col width="10%">
				<col>
				<col width="8%">
				<col width="10%">
				<col width="10%">
				<col width="10%">
		    </colgroup>
		    <thead>
				<tr>
					<!--th><input type="checkbox" name="" lay-skin="primary" lay-filter="allChoose" id="allChoose"></th-->
					<th>发布日期</th>
					<th>标题</th>
					<th>回复数</th>
					<th>分类</th>
					<th>状态</th>
					<th>操作</th>
				</tr> 
		    </thead>
		<tbody class="news_content">	
			</tbody>
		    <tbody class="chick_content"></tbody>
		</table>
	</div>
	<div id="page"></div>
	<script type="text/javascript" src="/src/layui/layui.js"></script>
	<script type="text/javascript" src="list.js"></script>
</body>
</html>