<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<title>CZFun</title>
	<link rel="stylesheet" type="text/css" href="<{$smarty.const.APP_RES}>/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="<{$smarty.const.APP_RES}>/css/bootstrap-theme.min.css">
	<link rel="stylesheet" type="text/css" href="<{$smarty.const.APP_RES}>/css/style.css">
	<script type="text/javascript" src="<{$smarty.const.APP_RES}>/js/jquery-1.9.1.min.js"></script>
	<script type="text/javascript" src="<{$smarty.const.APP_RES}>/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<{$smarty.const.APP_RES}>/js/bootstrap-tooltip.js"></script>
	<script type="text/javascript" src="<{$smarty.const.APP_RES}>/js/bootstrap-dropdown.js"></script>
	<script type="text/javascript" src="<{$smarty.const.APP_RES}>/js/bootstrap-popover.js"></script>
	<script type="text/javascript" src="<{$smarty.const.APP_RES}>/js/bootstrap-carousel.js"></script>
	<script type="text/javascript" src="<{$smarty.const.APP_RES}>/js/button.js"></script>
</head>
<body>
<!-- 头部 -->
	<div id="top"></div>
	<!-- 需要复制过去的中间布局部分 -->
	<!-- 需要显示的图片需要提前处理，否则显示的时候可能效果不好 -->
	</div>
		<div class="container-fluid"
		style="width: 100%; height: 200px; background: url(<{$smarty.const.APP_RES}>/home/images/123.png); background-repeat:no-repeat;"></div>
				<!-- 此处是导航部分 -->
				<div class="container">
	<nav class="navbar navbar-default">
		<div class="container-fluid">
			<div class="navbar-header">
				<ul class="nav nav-pills" style="position: relative; top: 5px;">
					<li class><a href="<{$smarty.const.__MODULE__}>/index/index">首页</a></li>
				</ul>
			</div>
			<!-- Collect the nav links, forms, and other content for toggling -->
			<div class="collapse navbar-collapse"
				id="bs-example-navbar-collapse-1">
				<ul class="nav navbar-nav">
					<{foreach from="$cat" item=row}>
					<li><a href="<{$smarty.const.__MODULE__}>/index/forward/cat/<{$row.id}>"><{$row.name}></a></li>
					<{/foreach}>
					<li><a href="<{$smarty.const.__MODULE__}>/index/showCat">更多</a></li>
				</ul>
				<form class="navbar-form navbar-left" action="<{$smarty.const.__MODULE__}>/index/isearch" method="GET" role="search">
					<div class="form-group">
						<input type="text" name="query" class="form-control" placeholder="Search"/>
					</div>
					<button type="submit" class="btn btn-primary">搜索</button>
				</form>
				<ul class="nav navbar-nav navbar-right">
					 <{if $smarty.session.userLogin==0}>
						 <li><a href="<{$smarty.const.__MODULE__}>/user/login"><span class="glyphicon glyphicon-user" aria-hidden="true"></span>登录</a></li>
						<li><a href="<{$smarty.const.__MODULE__}>/user/register"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>注册</a></li>
					<{else}>
						 <li><a href="#"><span class="glyphicon glyphicon-user" aria-hidden="true"></span><{$smarty.session.user.name}></a></li>
					<{/if}>
                    <{if $smarty.session.user.allow==1}><li><a href="<{$smarty.const.__MODULE__}>/video/add"><span class="glyphicon glyphicon-open" aria-hidden="true"></span>上传</a></li>
                    <{/if}>
                    <{if $smarty.session.userLogin==1}>
                                        <li><a href="<{$smarty.const.__MODULE__}>/user/logout"><span class="glyphicon glyphicon-open" aria-hidden="true"></span>登出</a></li>
                    <{/if}>
				</ul>
			</div>
			</div>
		</nav>

		</div>
