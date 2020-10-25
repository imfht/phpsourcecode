<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?php bloginfo('description'); ?>">
    <meta name="keywords" content="<?php bloginfo('description'); ?>">
    <meta name="author" content="Eyas">

    <meta name="HandheldFriendly" content="True" />
    <meta name="MobileOptimized" content="320" />

    <title><?php ey_seo_title(); ?></title>

    <link href="<?php bloginfo('template_directory'); ?>/style/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php bloginfo('template_directory'); ?>/style/css/font.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory'); ?>/style/css/main.min.css" />


    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
	  <script src="<?php bloginfo('template_directory'); ?>/style/js/html5shiv.min.js"></script>
	  <script src="<?php bloginfo('template_directory'); ?>/style/js/respond.min.js"></script>
	<![endif]-->
    <?php wp_head(); ?>

</head>

<body class="home-template custom-bg" style="background-image: url(<?php bloginfo('template_directory'); ?>/style/images/bg.jpg)">

    <nav class="navbar navbar-ghost" role="navigation">
        <div class="container-fluid">

            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#global-navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="global-navbar">
                <?php ey_the_menu('main_menu'); ?>
                <form action="<?php echo home_url('/'); ?>" class="navbar-form search-form">
                  <input type="text" class="form-control" name="s" value="<?php echo $_GET['s']; ?>" placeholder="搜索">
                </form>
            </div>

        </div>
    </nav>
    <?php if(is_home()||is_front_page()): ?>
    <header id="logo" class="container">
        <div class="row">
            <div class="col-lg-8 col-lg-push-2">
                <h1 class="blog-title">
                <a href="<?php bloginfo('url'); ?>"><?php bloginfo('name'); ?></a>
            </h1>
                <h2 class="blog-desc">
                <a href="<?php bloginfo('url'); ?>"><?php bloginfo('description'); ?></a>
            </h2>
                <div class="social">
                    <a class="btn btn-outline btn-lg" href="<?php echo home_url('/about'); ?>">关于我</a>
                    <a class="btn btn-outline btn-lg" href="http://git.oschina.net/yuesong">git仓库</a>
                </div>
            </div>
        </div>
    </header>
<?php endif; ?>