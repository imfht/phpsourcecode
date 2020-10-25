<?php require_once('inc/config.php') ?>
<?php require_once('inc/core.php') ?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<?php require_once('inc/title.html') ?>
<link href="img/finance/hengxin_logo_lg_1.png" rel="shortcut icon" type="image/x-icon" />
<meta name="description" content="">
<meta name="viewport" content="width=device-width">

<!-- Place favicon.ico and apple-touch-icon.png in the root directory -->

<link rel="stylesheet" href="css/normalize.css">
<link rel="stylesheet" href="css/main.css">
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/global.css">

<script src="//libs.baidu.com/jquery/1.9.1/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="js/vendor/jquery-1.9.1.min.js"><\/script>')</script>
<script src="js/vendor/modernizr-2.6.2.min.js"></script>
</head>
<body>
<!--[if lt IE 7]>
	    <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
	<![endif]-->

<!-- Add your site or application content here -->
<?php get_info() ?>
<?php require_once('inc/navbar.html') ?>
<?php print_content() ?>
<?php require_once('inc/footer.html') ?>

<script src="js/plugins.js"></script>
<script src="js/main.js"></script>
<script src="js/bootstrap.min.js"></script>

<!-- Baidu Tongji -->
<?php @$baidu_tongji_code = 'inc/baidu_tongji_code/'.$forum_city.'.html'; if(file_exists($baidu_tongji_code)) require_once($baidu_tongji_code); ?>
</body>
</html>
