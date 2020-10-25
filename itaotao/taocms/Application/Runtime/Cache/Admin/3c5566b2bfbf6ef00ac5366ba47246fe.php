<?php if (!defined('THINK_PATH')) exit();?><html><!DOCTYPE html>

<!-- 
Template Name: Metronic - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.1.1
Version: 2.0.2
Author: KeenThemes
Website: http://www.keenthemes.com/
Contact: support@keenthemes.com
Purchase: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes
License: You must have a valid license purchased only from themeforest(the above link) in order to legally use the theme for your project.
-->
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" class="no-js">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
    <meta charset="utf-8"/>
    <title>Metronic | Login Options - Login Form 2</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta content="" name="description"/>
    <meta content="" name="author"/>

    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="/tp3.2/Public/static/font-awesome/css/font-awesome.min.css" tppabs="http://www.keenthemes.com/preview/metronic_admin//tp3.2/Public/static/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="/tp3.2/Public/static/bootstrap/css/bootstrap.min.css" tppabs="http://www.keenthemes.com/preview/metronic_admin//tp3.2/Public/static/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="/tp3.2/Public/static/uniform/css/uniform.default.css" tppabs="http://www.keenthemes.com/preview/metronic_admin//tp3.2/Public/static/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN PAGE LEVEL STYLES -->
    <link rel="stylesheet" type="text/css" href="/tp3.2/Public/static/select2/select2.css" tppabs="http://www.keenthemes.com/preview/metronic_admin//tp3.2/Public/static/select2/select2.css"/>
    <link rel="stylesheet" type="text/css" href="/tp3.2/Public/static/select2/select2-metronic.css" tppabs="http://www.keenthemes.com/preview/metronic_admin//tp3.2/Public/static/select2/select2-metronic.css"/>
    <!-- END PAGE LEVEL SCRIPTS -->
    <!-- BEGIN THEME STYLES -->
    <link href="/tp3.2/Public/static/css/style-metronic.css" tppabs="http://www.keenthemes.com/preview/metronic_admin/assets/css/style-metronic.css" rel="stylesheet" type="text/css"/>
    <link href="/tp3.2/Public/static/css/style.css" tppabs="http://www.keenthemes.com/preview/metronic_admin/assets/css/style.css" rel="stylesheet" type="text/css"/>
    <link href="/tp3.2/Public/static/css/style-responsive.css" tppabs="http://www.keenthemes.com/preview/metronic_admin/assets/css/style-responsive.css" rel="stylesheet" type="text/css"/>
    <link href="/tp3.2/Public/static/css/plugins.css" tppabs="http://www.keenthemes.com/preview/metronic_admin/assets/css/plugins.css" rel="stylesheet" type="text/css"/>
    <link href="/tp3.2/Public/static/css/themes/default.css" tppabs="http://www.keenthemes.com/preview/metronic_admin/assets/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color"/>
    <link href="/tp3.2/Public/static/css/pages/login-soft.css" tppabs="http://www.keenthemes.com/preview/metronic_admin/assets/css/pages/login-soft.css" rel="stylesheet" type="text/css"/>
    <link href="/tp3.2/Public/static/css/custom.css" tppabs="http://www.keenthemes.com/preview/metronic_admin/assets/css/custom.css" rel="stylesheet" type="text/css"/>
    <!-- END THEME STYLES -->
    <link rel="shortcut icon" href="favicon.ico" tppabs="http://www.keenthemes.com/preview/metronic_admin/favicon.ico"/>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="login">
<!-- BEGIN LOGO -->
<div class="logo">
    <a href="index.html" >
        <img src="/tp3.2/Public/static/img/logo-big.png"  alt=""/>
    </a>
</div>
<!-- END LOGO -->
<!-- BEGIN LOGIN -->
<div class="content">
<!-- BEGIN LOGIN FORM -->
<form class="login-form" action="#" method="post">
    <h3 class="form-title">TAOCMS后台管理系统登录</h3>
    <div class="alert alert-danger display-hide">
        <button class="close" data-close="alert"></button>
			<span>
				 请输入用户名和密码.
			</span>
    </div>
    <div class="form-group">
        <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
        <label class="control-label visible-ie8 visible-ie9">用户名</label>
        <div class="input-icon">
            <i class="fa fa-user"></i>
            <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="用户名" name="username"/>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label visible-ie8 visible-ie9">密码</label>
        <div class="input-icon">
            <i class="fa fa-lock"></i>
            <input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="密码" name="password"/>
        </div>
    </div>
    <div class="form-actions">
        <label class="checkbox">
            <input type="checkbox" name="remember" value="1"/> 记住我 </label>
        <button type="submit" class="btn blue pull-right">
            登录 <i class="m-icon-swapright m-icon-white"></i>
        </button>
    </div>

    <div class="forget-password">
        <h4>忘记密码 ?</h4>
        <p>
            别急, 点击
            <a href="javascript:;" id="forget-password">
                这里
            </a>
            重置你的密码.
        </p>
    </div>
</form>
<!-- END LOGIN FORM -->
<!-- BEGIN FORGOT PASSWORD FORM -->
<form class="forget-form" action="http://www.keenthemes.com/preview/metronic_admin/index.html" method="post">
    <h3>忘记密码 ?</h3>
    <p>
        输入邮箱重置你的密码.
    </p>
    <div class="form-group">
        <div class="input-icon">
            <i class="fa fa-envelope"></i>
            <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Email" name="email"/>
        </div>
    </div>
    <div class="form-actions">
        <button type="button" id="back-btn" class="btn">
            <i class="m-icon-swapleft"></i> 返回 </button>
        <button type="button" class="btn blue pull-right">
            重置 <i class="m-icon-swapright m-icon-white"></i>
        </button>
    </div>
</form>
<!-- END FORGOT PASSWORD FORM -->
</div>
<!-- END LOGIN -->
<!-- BEGIN COPYRIGHT -->
<div class="copyright">
    2014 &copy; Metronic - Admin Dashboard Template.
</div>
<!-- END COPYRIGHT -->
<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="/tp3.2/Public/static/respond.min.js"></script>
<script src="/tp3.2/Public/static/excanvas.min.js"></script>
<![endif]-->
<script src="/tp3.2/Public/static/jquery-1.10.2.min.js" type="text/javascript"></script>
<script src="/tp3.2/Public/static/jquery-migrate-1.2.1.min.js"  type="text/javascript"></script>
<script src="/tp3.2/Public/static/bootstrap/js/bootstrap.min.js"  type="text/javascript"></script>
<script src="/tp3.2/Public/static/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
<script src="/tp3.2/Public/static/jquery-slimscroll/jquery.slimscroll.min.js"  type="text/javascript"></script>
<script src="/tp3.2/Public/static/jquery.blockui.min.js"  type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="/tp3.2/Public/static/jquery-validation/dist/jquery.validate.min.js" ></script>
<script src="/tp3.2/Public/static/backstretch/jquery.backstretch.min.js"  type="text/javascript"></script>
<script type="text/javascript" src="/tp3.2/Public/static/select2/select2.min.js" ></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="/tp3.2/Public/static/scripts/core/app.js"  type="text/javascript"></script>

<script src="/tp3.2/Public/Admin/js/jQuery.md5.js"></script>
<script src="/tp3.2/Public/static/bootbox/bootbox.min.js"  type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
<script>
    var SITE_URL = "/tp3.2/index.php/Admin/Public";
    var ACTION = "/tp3.2/index.php/Admin/Public/login";
    var APP = "/tp3.2/index.php";
    var SELF = "/tp3.2/index.php/Admin/Public/login";
    jQuery(document).ready(function() {
        App.init();
        Login.init();
        $.backstretch([
            "/tp3.2/Public/static/img/bg/1.jpg",
            "/tp3.2/Public/static/img/bg/2.jpg",
            "/tp3.2/Public/static/img/bg/3.jpg",
            "/tp3.2/Public/static/img/bg/4.jpg"
        ], {
            fade: 1000,
            duration: 8000
        });
    });

</script>
<script src="/tp3.2/Public/Admin/js/login.js"  type="text/javascript"></script>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>