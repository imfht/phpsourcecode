<!DOCTYPE html>
<html lang="zh-CN">
	<head>
		<meta charset="utf-8">
		<title><?php echo lang('title'); ?></title>
        <meta name="renderer" content="webkit">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,IE=8,IE=9,chrome=1">
        <meta content="width=device-width, initial-scale=1" name="viewport"/>
        <meta name="keywords" content="菜鸟CMS"/>
        <meta name="description" content="菜鸟CMS">
        <meta name="author" content="二阳">
		<!-- 样式 -->
		<link id="bs-css" href="<?php echo SITE_ADMIN_CSS; ?>/bootstrap-<?php
		if ($this -> _manager) {
			echo $this -> _manager -> skin;
		} else {
			echo 'cerulean';
		}
		?>.css" rel="stylesheet">
		<style type="text/css">
			body {
				padding-bottom: 40px;
			}
			.sidebar-nav {
				padding: 9px 0;
			}
		</style>
		<link href="<?php echo SITE_ADMIN_CSS; ?>/bootstrap-responsive.css" rel="stylesheet">
		<link href="<?php echo SITE_ADMIN_CSS; ?>/charisma-app.css" rel="stylesheet">
		<link href="<?php echo SITE_ADMIN_CSS; ?>/jquery-ui-1.8.21.custom.css" rel="stylesheet">
		<link href="<?php echo SITE_ADMIN_CSS; ?>/fullcalendar.css" rel="stylesheet">
		<link href="<?php echo SITE_ADMIN_CSS; ?>/fullcalendar.print.css" rel="stylesheet"  media="print">
		<link href="<?php echo SITE_ADMIN_CSS; ?>/chosen.css" rel="stylesheet">
		<link href="<?php echo SITE_ADMIN_CSS; ?>/uniform.default.css" rel="stylesheet">
		<link href="<?php echo SITE_ADMIN_CSS; ?>/colorbox.css" rel="stylesheet">
		<link href="<?php echo SITE_ADMIN_CSS; ?>/jquery.cleditor.css" rel="stylesheet">
		<link href="<?php echo SITE_ADMIN_CSS; ?>/jquery.noty.css" rel="stylesheet">
		<link href="<?php echo SITE_ADMIN_CSS; ?>/noty_theme_default.css" rel="stylesheet">
		<link href="<?php echo SITE_ADMIN_CSS; ?>/elfinder.min.css" rel="stylesheet">
		<link href="<?php echo SITE_ADMIN_CSS; ?>/elfinder.theme.css" rel="stylesheet">
		<link href="<?php echo SITE_ADMIN_CSS; ?>/jquery.iphone.toggle.css" rel="stylesheet">
		<link href="<?php echo SITE_ADMIN_CSS; ?>/opa-icons.css" rel="stylesheet">
		<link href="<?php echo SITE_ADMIN_CSS; ?>/uploadify.css" rel="stylesheet">
		<link href="<?php echo SITE_ADMIN_ART; ?>/skins/twitter.css" rel="stylesheet">

		<!-- The HTML5 shim, for IE6-8 support of HTML5 elements -->
		<!--[if lt IE 9]>
		<script src="/assets/admin/js/html5.js"></script>
		<![endif]-->
		<!--图标-->
		<link rel="shortcut icon" href="/assets/admin/img/favicon.ico">
	</head>
	<body>
		<!--头部菜单开始-->
		<div class="navbar">
			<div class="navbar-inner navbar-fixed-top">
				<div class="container-fluid">
					<a class="btn btn-navbar" data-toggle="collapse" data-target=".top-nav.nav-collapse,.sidebar-nav.nav-collapse"> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </a>


					<!--更换皮肤开始-->
					<div class="btn-group pull-right theme-container" >
						<a class="btn dropdown-toggle" data-toggle="dropdown" href="#"> <i class="icon-tint"></i><span class="hidden-phone"><?php echo lang('change_skin'); ?></span> <span class="caret"></span> </a>
						<ul class="dropdown-menu" id="themes">
								<li>
								<a data-value="cerulean" href="#"><i class="icon-blank"></i><?php echo lang('skin_cerulean'); ?></a>
							</li>
							<li>
								<a data-value="classic" href="#"><i class="icon-blank"></i> <?php echo lang('skin_classic'); ?></a>
							</li>
						
							<li>
								<a data-value="cyborg" href="#"><i class="icon-blank"></i> <?php echo lang('skin_cyborg'); ?></a>
							</li>
							<li>
								<a data-value="redy" href="#"><i class="icon-blank"></i> <?php echo lang('skin_redy'); ?></a>
							</li>
							<li>
								<a data-value="journal" href="#"><i class="icon-blank"></i> <?php echo lang('skin_journal'); ?></a>
							</li>
							<li>
								<a data-value="simplex" href="#"><i class="icon-blank"></i> <?php echo lang('skin_simplex'); ?></a>
							</li>
							<li>
								<a data-value="slate" href="#"><i class="icon-blank"></i> <?php echo lang('skin_slate'); ?></a>
							</li>
							<li>
								<a data-value="spacelab" href="#"><i class="icon-blank"></i> <?php echo lang('skin_spacelab'); ?></a>
							</li>
							<li>
								<a data-value="united" href="#"><i class="icon-blank"></i> <?php echo lang('skin_united'); ?></a>
							</li>
						</ul>
					</div><!--/.更换皮肤结束-->
					

					<!--用户菜单开始-->
					<div class="btn-group pull-right" >
						<a class="btn dropdown-toggle" data-toggle="dropdown" href="#"> <i class="icon-user"></i><span class="hidden-phone"> <?php echo $this -> _manager -> username; ?></span> <span class="caret"></span> </a>
						<ul class="dropdown-menu">
							<li id="btn_change_password">
								<a href="#"><?php echo lang('change_password'); ?></a>
							</li>
							<li class="divider"></li>
							<li>
								<a href="<?php echo site_url($this -> config -> item('admin_folder') . 'login/logout'); ?>"><?php echo lang('logout'); ?></a>
							</li>
						</ul>
					</div>
					<!--/. 用户菜单结束 -->

					<div class="top-nav nav-collapse">
						<ul class="nav">
								<li>
								<a id="local_time"></a>
							</li>
						</ul>
					</div>

				</div>
			</div>
		</div>
		<!--/.头部结束 -->
		
		<!--中间部分开始-->
		<div class="container-fluid clearfix" id="container_menu">
			<div class="row-fluid">
				<!-- 左边菜单开始 -->
				<div class="span2 main-menu-span">
				<?php foreach($this -> power_menu as $menu=>$menu_data ): ?>
           		<?php
				if ($menu_data['url'] != 'default_view') {
					continue;
				}
				?>	
				<div class="well nav-collapse sidebar-nav" id="left_menu" >
				<ul class="nav nav-tabs nav-stacked main-menu">
				<?php foreach($menu_data['sub_power'] as $sub_n => $sub_data): ?>
					<li class="nav-header hidden-tablet"><?php echo $sub_data['name']; ?></li>
					<?php foreach($sub_data['sub_power'] as $sub_sub_data): ?>
                  <li><a class="ajax-link" href="<?php echo site_url($this -> config -> item('admin_folder').$sub_sub_data['url']); ?>"><i class="<?php echo $sub_sub_data['icon']; ?>"></i><span class="hidden-tablet"> <?php echo $sub_sub_data['name']; ?></span></a></li>
                    <?php endforeach; ?>
				<?php endforeach; ?>
			</ul>
				</div>
				<?php endforeach; ?>
				</div>
				<!--/.左边菜单结束 -->


				<div id="content" class="span10">
				<!-- 右边 内容开始-->