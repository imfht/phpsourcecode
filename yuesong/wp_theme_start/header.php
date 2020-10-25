<!DOCTYPE html>
<html <?php language_attributes(); ?>>
  <head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <title><?php wp_seo_title(); ?></title>
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta name="renderer" content="webkit">
    <meta name="keyword" content="<?php echo get_option('ets_keyword'); ?>">
    <meta name="description" content="<?php echo get_option('ets_description'); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <?php wp_head(); ?>

    <!-- 加载自己的样式 -->
    <link href="<?php bloginfo('template_url'); ?>/style/bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="<?php bloginfo('template_url'); ?>/style/css/flat-ui.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/style/css/style.css">
    <link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/style/css/font.css">

    <!-- favicon -->
    <link rel="shortcut icon" href="<?php bloginfo('template_url'); ?>/style/images/favicon.ico">

    <!-- 加载自己的脚本 -->
    <script src="<?php bloginfo('template_url'); ?>/style/js/jquery.min.js"></script>
    <script src="<?php bloginfo('template_url'); ?>/style/js/bootstrap.min.js"></script>

    <!-- 无线加载内容 -->
    
    

    <!-- 让IE6-8支持部分html5元素 -->
    <!--[if lt IE]>
      <script src="<?php bloginfo('template_url'); ?>/style/js/html5shiv.js"></script>
      <script src="<?php bloginfo('template_url'); ?>/style/js/respond.min.js"></script>
    <![endif]-->
  </head>
  <body <?php body_class(); ?>>
    <div style="display:none;">
    <p><a href="#content"><?php _e('Skip to Content'); ?></a></p><?php /* 特别照顾特殊设备友好的访问 */ ?>
  </div><!--.none-->
      <header class="header">
        <!-- 主导航 -->
        <nav class="navbar navbar-default fixnavbar" role="navigation">
          <div class="container">
            <!-- 响应式导航条 移动端 -->
            <div class="navbar-header">
              <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">切换导航</span>
                <span class="btn btn-primary menu-btn"><i class="icon icon-menu" style="font-size:2em;"></i></span>
              </button>
              <a class="navbar-brand" href="<?php echo home_url('/'); ?>">Eyas</a>
            </div>

            <!-- 响应式导航条 PC端 -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
              <!-- 主导航 -->
              <?php wp_bootstrap_nav_menu('main_menu'); ?>
              <!-- 主导航右侧 -->
              <ul class="nav navbar-nav navbar-right">
                <?php if(is_user_logged_in()): ?>
                  <li><a href="<?php echo home_url('/'); ?>/wp-admin/">管理中心</a></li>
                  <li><a href="<?php echo wp_logout_url(home_url('/')); ?>">注销</a></li>
                <?php else: ?>
                  <li><a href="<?php echo wp_login_url() ?>">登陆</a></li>
                <?php endif; ?>
              </ul>
            </div><!-- /.navbar-collapse -->
          </div><!-- /.container -->
        </nav>
    </header><!--/顶部-->
    
    <?php if(is_home() || is_front_page()): ?>
    <!-- 巨幕 -->
    <div class="jumbotron" style="background-image: url(<?php bloginfo('template_url'); ?>/style/images/title-bg.png);">
      <div class="jumbotron-content">
        <h1><?php bloginfo('name'); ?></h1>
        <p><?php bloginfo('description'); ?></p>
        <p><a class="btn btn-primary btn-lg" role="button">关于我</a></p>
      </div>
    </div>
    <?php endif; ?>

    <div class="container">













