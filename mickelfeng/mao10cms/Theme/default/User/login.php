<!DOCTYPE html>
<html>
<head>
<title><?php echo mc_title(); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link rel="stylesheet" href="<?php echo mc_theme_url(); ?>/css/bootstrap.css">
<link rel="stylesheet" href="<?php echo mc_theme_url(); ?>/style.css" type="text/css" media="screen" />
<?php $site_color = mc_option('site_color'); if($site_color!='') : ?>
<style>
a {color: <?php echo $site_color; ?>;}
a:hover {color: #3f484f;}
.btn-warning {color: #fff; background-color:<?php echo $site_color; ?>; border-color: <?php echo $site_color; ?>;}
.btn-warning:hover {background-color:<?php echo $site_color; ?>; border-color: <?php echo $site_color; ?>;}
.label-warning {background-color: <?php echo $site_color; ?>;}

.home-main h4.title a.pull-right:hover {background-color: <?php echo $site_color; ?>; }
#pro-list .thumbnail h4 a:hover,
.home-side .media-heading a:hover {color: #3f484f;}

.home-side .panel-heading,
#home-top .carousel-indicators .active,
#topnav .navbar-right .count,
#topnav .navbar-right a:hover .count,
#topnav .dropdown-menu > li > a:hover,
#single-top #pro-index-tlin .carousel-indicators li.active,
#user-nav li.active > a,
#user-nav > li.active > a:hover,
#user-nav > li.active > a:focus,
#baobei-term-breadcrumb .pull-right a:hover {background-color: <?php echo $site_color; ?>;}

#site-control,
#backtotop:hover,
#total span,
#checkout .input-group-addon,
#total-true span {color: <?php echo $site_color; ?>;}

#post-list-default .list-group-item > .row {border-left-color: <?php echo $site_color; ?>;}

#group-side ul.nav-stacked li.active a,
#group-side ul.nav-stacked a:hover {background-color: <?php echo $site_color; ?>; border-color: <?php echo $site_color; ?>; }
</style>
<?php endif; ?>
<link href="<?php echo mc_theme_url(); ?>/css/media.css" rel="stylesheet">
<!--[if lt IE 9]>
<script src="<?php echo mc_theme_url(); ?>/js/html5shiv.min.js"></script>
<script src="<?php echo mc_theme_url(); ?>/js/respond.min.js"></script>
<![endif]-->
<style>
	body {background-color: #fff;}
</style>
</head>
<body>
<div class="container" id="login">
	<div class="text-center login-head">
		<a href="<?php echo mc_site_url(); ?>"><img src="<?php echo mc_theme_url(); ?>/img/logo.png"></a>
		<h1>一个帐号，玩转本站所有服务！</h1>
		<p><?php echo mc_option('site_name'); ?></p>
	</div>
	<div class="row">
		<div class="col-sm-4 col-sm-offset-4">
			<form role="form" method="post" action="<?php echo U('user/login/submit'); ?>">
				<div class="form-group">
					<input type="text" name="user_name" class="form-control bb-0 input-lg" placeholder="账号">
					<input type="text" name="user_pass" class="form-control input-lg password" placeholder="密码">
				</div>
				<div class="form-group">
					<button type="submit" class="btn btn-warning btn-block btn-lg">
						立即登陆
					</button>
				</div>
				<div class="form-group">
					<?php if(mc_option('loginqq')==2) : ?>
					<a href="<?php echo mc_site_url(); ?>/connect-qq/oauth/index.php"><img src="<?php echo mc_site_url(); ?>/connect-qq/qq_logo.png"></a>
					<?php endif; ?>
					<?php if(mc_option('loginweibo')==2) : ?>
					<a href="<?php echo mc_site_url(); ?>/connect-weibo/oauth/index.php"><img src="<?php echo mc_site_url(); ?>/connect-weibo/weibo_logo.png"></a>
					<?php endif; ?>
				</div>
				<div class="form-group">
					<p class="help-block">
						<a href="<?php echo U('user/lostpass/index'); ?>">忘记密码？</a>
					</p>
				</div>
				<div class="form-group">
					<a href="<?php echo U('user/register/index'); ?>" class="btn btn-default btn-block btn-lg">
						注册账号
					</a>
				</div>
			</form>
		</div>
	</div>
	<div class="text-center login-foot">
		<p>Copyright <?php echo date('Y'); ?> <?php echo mc_option('site_name'); ?></p>
		由<a href="http://www.mao10.com/">Mao10CMS</a>强力驱动</p>
	</div>
</body>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="<?php echo mc_theme_url(); ?>/js/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="<?php echo mc_theme_url(); ?>/js/bootstrap.min.js"></script>
<script src="<?php echo mc_theme_url(); ?>/js/placeholder.js"></script>
<script type="text/javascript">
	$(function() {
		$('input, textarea').placeholder();
	});
</script>
<script src="<?php echo mc_theme_url(); ?>/js/cat.js"></script>
</html>