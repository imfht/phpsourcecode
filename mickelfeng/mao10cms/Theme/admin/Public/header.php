<!DOCTYPE html>
<html>
<head>
<title><?php echo mc_title(); ?></title>
<?php echo mc_seo(); ?>
<meta name="renderer" content="webkit">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" href="<?php echo mc_site_url(); ?>/favicon.ico" mce_href="<?php echo mc_site_url(); ?>/favicon.ico" type="image/x-icon">
<link rel="shortcut icon" href="<?php echo mc_site_url(); ?>/favicon.ico" mce_href="<?php echo mc_site_url(); ?>/favicon.ico" type="image/x-icon">
<!-- Bootstrap -->
<link rel="stylesheet" href="<?php echo mc_site_url(); ?>/Theme/admin/css/bootstrap.css">
<link rel="stylesheet" href="<?php echo mc_site_url(); ?>/Theme/admin/css/font-awesome.min.css">
<link rel="stylesheet" href="<?php echo mc_site_url(); ?>/Theme/admin/style.css" type="text/css" media="screen" />
<link href="<?php echo mc_site_url(); ?>/Theme/admin/css/media.css" rel="stylesheet">
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="<?php echo mc_site_url(); ?>/Theme/admin/js/jquery.min.js"></script>
<!--[if lt IE 9]>
<script src="<?php echo mc_site_url(); ?>/Theme/admin/js/html5shiv.min.js"></script>
<script src="<?php echo mc_site_url(); ?>/Theme/admin/js/respond.min.js"></script>
<![endif]-->
</head>
<body>
<div class="container-fluid">
	<div class="row">
		<div class="col-sm-3 col-md-2 sidebar">
			<h1 class="mt-0 mb-50">
				<a href="<?php echo U('control/index/index'); ?>"><img src="<?php echo mc_site_url(); ?>/Theme/admin/img/logo-s.png"></a>
			</h1>
			<ul class="nav nav-sidebar">
				<li><a href="<?php echo U('control/index/index'); ?>"><i class="fa fa-circle-o"></i> 后台首页</a></li>
				<li><a href="<?php echo mc_site_url(); ?>"><i class="fa fa-circle-o"></i> 前台首页</a></li>
			</ul>
			<ul class="nav nav-sidebar">
				<li><a href="<?php echo U('control/index/pro_index'); ?>"><i class="fa fa-circle-o"></i> 管理商品</a></li>
				<li><a href="<?php echo U('publish/index/index'); ?>"><i class="fa fa-circle-o"></i> 发布商品</a></li>
			</ul>
			<ul class="nav nav-sidebar">
				<li><a href="<?php echo U('control/index/pro_all'); ?>"><i class="fa fa-circle-o"></i> 订单管理</a></li>
				<li><a href="<?php echo U('control/index/paytools'); ?>"><i class="fa fa-circle-o"></i> 支付接口</a></li>
				<li><a href="<?php echo U('control/index/tixian'); ?>"><i class="fa fa-circle-o"></i> 提现记录</a></li>
			</ul>
			<ul class="nav nav-sidebar">
				<li><a href="<?php echo U('publish/index/add_group'); ?>"><i class="fa fa-circle-o"></i> 新建版块</a></li>
				<li><a href="<?php echo U('control/index/post_pending'); ?>"><i class="fa fa-circle-o"></i> 待审主题(<?php echo M('page')->where('type="pending"')->count(); ?>)</a></li>
			</ul>
			<ul class="nav nav-sidebar">
				<li><a href="<?php echo U('control/index/article_index'); ?>"><i class="fa fa-circle-o"></i> 管理文章</a></li>
				<li><a href="<?php echo U('publish/index/add_article'); ?>"><i class="fa fa-circle-o"></i> 发布文章</a></li>
			</ul>
			<ul class="nav nav-sidebar">
				<li><a href="<?php echo U('control/index/topic_index'); ?>"><i class="fa fa-circle-o"></i> 管理单页</a></li>
				<li><a href="<?php echo U('publish/index/add_topic'); ?>"><i class="fa fa-circle-o"></i> 发布单页</a></li>
			</ul>
			<ul class="nav nav-sidebar">
				<li><a href="<?php echo U('control/index/set'); ?>"><i class="fa fa-circle-o"></i> 网站设置</a></li>
				<li><a href="<?php echo U('control/weixin/index'); ?>"><i class="fa fa-circle-o"></i> 微信连接</a></li>
				<li><a href="<?php echo U('control/index/manage'); ?>"><i class="fa fa-circle-o"></i> 用户管理</a></li>
				<li><a href="<?php echo U('control/index/images'); ?>"><i class="fa fa-circle-o"></i> 图片管理</a></li>
				<li><a href="<?php echo U('control/index/module'); ?>"><i class="fa fa-circle-o"></i> 模块管理</a></li>
				<li><a href="<?php echo U('control/index/nav'); ?>"><i class="fa fa-circle-o"></i> 导航设置</a></li>
			</ul>
			<ul class="nav nav-sidebar">
				<li><a href="javascript:;" id="head-logout-btn"><i class="fa fa-circle-o"></i> 退出登陆</a></li>
			</ul>
			<form method="post" class="inline" id="head-logout" action="<?php echo U('user/login/logout'); ?>">
				<input type="hidden" name="logout" value="ok">
			</form>
			<script>
				$('#head-logout-btn').click(function(){
					$('#head-logout').submit();
				});
			</script>
		</div>
		<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">