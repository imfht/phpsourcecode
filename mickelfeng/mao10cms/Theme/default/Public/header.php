<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="renderer" content="webkit">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title><?php echo mc_title(); ?></title>
<?php echo mc_seo(); ?>
<link rel="icon" href="<?php echo mc_site_url(); ?>/favicon.ico" mce_href="<?php echo mc_site_url(); ?>/favicon.ico" type="image/x-icon">
<link rel="shortcut icon" href="<?php echo mc_site_url(); ?>/favicon.ico" mce_href="<?php echo mc_site_url(); ?>/favicon.ico" type="image/x-icon">
<!-- Bootstrap -->
<link rel="stylesheet" href="<?php echo mc_site_url(); ?>/Theme/default/css/bootstrap.css">
<link rel="stylesheet" href="<?php echo mc_site_url(); ?>/Theme/default/css/font-awesome.min.css">
<link rel="stylesheet" href="<?php echo mc_theme_url(); ?>/style.css">
<link href="<?php echo mc_theme_url(); ?>/css/media.css" rel="stylesheet">
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="<?php echo mc_site_url(); ?>/Theme/default/js/jquery.min.js"></script>
<!--[if lt IE 9]>
<script src="<?php echo mc_site_url(); ?>/Theme/default/js/html5shiv.min.js"></script>
<script src="<?php echo mc_site_url(); ?>/Theme/default/js/respond.min.js"></script>
<![endif]-->
<?php if(mc_option('site_color')) : $site_color = mc_option('site_color'); ?>
<style>
	a,
	.home-pro .nav-terms a:hover,
	.home-pro .nav-terms .active a,
	.home-panel .panel-heading > i,
	.home-nav a:hover,
	.search-title .active a,
	.home-pro h2.title > i,
	.home-pro h4.title > i,
	.delete-cart a:hover,
	.article-brd a:hover,
	#comment h4.title > i,
	footer.footer a:hover,
	.user-nav .nav-tabs a:hover,
	.home-main h4.title a.pull-right:hover,
	.home-side .panel-heading > i,
	.home-side .panel-heading a.pull-right:hover,
	#post-list-default > .nav-pills > li.active > a {color: <?php echo $site_color; ?>; }
	
	.label-warning,
	::-webkit-scrollbar-thumb:vertical:hover,
	.home-pro a.pr:hover > .pa.bg,
	#pro-index-tlin .carousel-indicators li.active,
	header.header,
	.btn-success.btn-like:hover,
	#share a.btn-success.btn-like:hover {background-color: <?php echo $site_color; ?>; }
	
	.btn-warning,
	.btn-warning:hover,
	.container .pagination > li > a:hover,
	.pagination > li.active > a,
	.pagination > li.active > a:hover,
	#pro-index-trin .btn-group button.add-cart {background-color: <?php echo $site_color; ?>; border-color: <?php echo $site_color; ?>; }
	
	.btn-success.btn-like {border-color: <?php echo $site_color; ?>; color: <?php echo $site_color; ?>; }
	
</style>
<?php endif; ?>
</head>
<body>
<a id="site-top"></a>
<header class="header">
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-5 col">
				<form method="get" action="<?php echo mc_option('site_url'); ?>">
					<input type="text" name="keyword">
					<button type="submit">
						<i class="glyphicon glyphicon-search"></i>
					</button>
					<span class="bg"></span>
				</form>
			</div>
			<div class="col-sm-2 col text-center">
				<a href="<?php echo mc_site_url(); ?>"><img src="<?php if(mc_option('logo')) : echo mc_option('logo'); else : ?><?php echo mc_theme_url(); ?>/img/logo-xs.png<?php endif; ?>"></a>
			</div>
			<div class="col-sm-5 col text-right">
				<ul class="list-inline mb-0 wto">
					<li><a href="<?php echo U('pro/index/index'); ?>"><?php echo mc_option('pro_name'); ?></a></li>
					<li><a href="<?php echo U('post/group/index'); ?>"><?php echo mc_option('group_name') ?></a></li>
					<li><a href="<?php echo U('article/index/index'); ?>"><?php echo mc_option('article_name') ?></a></li>
					<?php $condition['type']  = array('in',array('nav','nav2')); $nav = M('option')->where($condition)->order('id asc')->select(); foreach($nav as $val) : ?>
					<li>
						<a <?php if($val['type']=='nav2') : ?>target="_blank"<?php endif; ?> href="<?php echo $val['meta_value']; ?>">
							<?php echo $val['meta_key']; ?>
						</a>
					</li>
					<?php endforeach; ?>
					<?php if(mc_user_id()) : ?>
					<li>
						<a href="<?php echo U('user/index/index?id='.mc_user_id()); ?>">
							用户中心
							<?php if(mc_user_trend_count()) : ?>(<span class="count"><?php echo mc_user_trend_count(); ?></span>)<?php endif; ?>
						</a>
					</li>
					<!--li>
						<a href="<?php //echo U('user/reffer/index?id='.mc_user_id()); ?>">
							<i class="fa fa-trophy"></i>
						</a>
					</li-->
					<?php if(mc_is_admin()) : ?>
					<li>
						<a target="_blank" href="<?php echo U('control/index/index'); ?>">
							管理
						</a>
					</li>
					<?php endif; ?>
					<li>
						<a href="<?php echo U('pro/cart/index'); ?>">
							购物车
							(<span class="count"><?php echo mc_cart_count(); ?></span>)
						</a>
					</li>
					<li>
						<a href="<?php echo U('user/login/logout'); ?>" id="head-logout-btn">
							退出
						</a>
					</li>
					<?php else : ?>
					<li>
						<a href="#" data-toggle="modal" data-target="#loginModal">
							登陆
						</a>
					</li>
					<li>
						<a href="#" data-toggle="modal" data-target="#registerModal">
							注册
						</a>
					</li>
					<?php endif; ?>
				</ul>
			</div>
		</div>
	</div>
</header>