<!DOCTYPE html>
<html lang="zh">
  <head>
    <meta charset="utf-8">
    <title><?php if ( is_category() ) {
		echo 'Category Archive for &quot;'; single_cat_title(); echo '&quot; | '; bloginfo( 'name' );
	} elseif ( is_tag() ) {
		echo 'Tag Archive for &quot;'; single_tag_title(); echo '&quot; | '; bloginfo( 'name' );
	} elseif ( is_archive() ) {
		wp_title(''); echo ' Archive | '; bloginfo( 'name' );
	} elseif ( is_search() ) {
		echo 'Search for &quot;'.wp_specialchars($s).'&quot; | '; bloginfo( 'name' );
	} elseif ( is_home() ) {
		bloginfo( 'name' ); echo ' | '; bloginfo( 'description' );
	}  elseif ( is_404() ) {
		echo 'Error 404 Not Found | '; bloginfo( 'name' );
	} elseif ( is_single() ) {
		wp_title('');
	} else {
		echo wp_title(''); echo ' | '; bloginfo( 'name' );
	} ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Loading Bootstrap -->
    <link href="<?php bloginfo('template_directory'); ?>/bootstrap/css/bootstrap.css" rel="stylesheet">

    <!-- Loading Flat UI -->
    <link href="<?php bloginfo('template_directory'); ?>/css/flat-ui.css" rel="stylesheet">

    <link rel="shortcut icon" href="<?php bloginfo('template_directory'); ?>/images/favicon.ico">
    
    <link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/style.css">
    <link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/font.css">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
    <!--[if lt IE 9]>
      <script src="<?php bloginfo('template_directory'); ?>/js/html5shiv.js"></script>
      <script src="<?php bloginfo('template_directory'); ?>/js/respond.min.js"></script>
    <![endif]-->
    <?php wp_head(); ?>
  </head>
  <body>
    <a href="#content" class="sr-only sr-only-focusable">Skip to main content （直接进入主内容区）</a>
      <header class="header">
        <nav class="navbar navbar-default fixnavbar" role="navigation">
            <div class="container-fluid">
              <!-- Brand and toggle get grouped for better mobile display -->
              <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                  <span class="sr-only">切换导航</span>
                  <span class="btn btn-primary menu-btn"><i class="icon icon-menu" style="font-size:2em;"></i></span>
                </button>
                <a class="navbar-brand" href="<?php bloginfo('url'); ?>">Eyas</a>
              </div>

              <!-- Collect the nav links, forms, and other content for toggling -->
              <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <?php the_main_menu(); ?>
                <ul class="nav navbar-nav navbar-right">
                  <?php if(is_user_logged_in()): ?>
                  <li><a href="<?php echo get_admin_url(); ?>">管理中心</a></li>
                  <li><a href="<?php echo wp_logout_url('/'); ?>">注销</a></li>
                <?php else: ?>
                  <li><a href="<?php echo wp_login_url('/'); ?>">登陆</a></li>
                  <?php endif; ?>
                </ul>
              </div><!-- /.navbar-collapse -->
            </div><!-- /.container-fluid -->
          </nav>
    </header><!--/顶部-->
