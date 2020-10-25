<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>

<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->

<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->

<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->

<!-- BEGIN HEAD -->

<head>

    <meta charset="utf-8" />

    <title>Metronic | Login Page</title>

    <meta content="width=device-width, initial-scale=1.0" name="viewport" />

    <meta content="" name="description" />

    <meta content="" name="author" />

    <!-- BEGIN GLOBAL MANDATORY STYLES -->

    <link href="/tp3.2/Public/Admin/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>

    <link href="/tp3.2/Public/Admin/css/bootstrap-responsive.min.css" rel="stylesheet" type="text/css"/>

    <link href="/tp3.2/Public/Admin/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>

    <link href="/tp3.2/Public/Admin/css/style-metro.css" rel="stylesheet" type="text/css"/>

    <link href="/tp3.2/Public/Admin/css/style.css" rel="stylesheet" type="text/css"/>

    <link href="/tp3.2/Public/Admin/css/style-responsive.css" rel="stylesheet" type="text/css"/>

    <link href="/tp3.2/Public/Admin/css/default.css" rel="stylesheet" type="text/css" id="style_color"/>

    <link href="/tp3.2/Public/Admin/css/uniform.default.css" rel="stylesheet" type="text/css"/>

    <!-- END GLOBAL MANDATORY STYLES -->

    <!-- BEGIN PAGE LEVEL STYLES -->

    <link href="/tp3.2/Public/Admin/css/login.css" rel="stylesheet" type="text/css"/>

    <!-- END PAGE LEVEL STYLES -->

    <link rel="shortcut icon" href="/tp3.2/Public/Admin/image/favicon.ico" />

</head>

<!-- END HEAD -->

<!-- BEGIN BODY -->

<body class="login">

<!-- BEGIN LOGO -->

<div class="logo">

    <img src="/tp3.2/Public/Admin/image/logo-big.png" alt="" />

</div>

<!-- END LOGO -->

<!-- BEGIN LOGIN -->

<div id="noscript" style="text-align: center;color: #fff;font-size: 20px;font-weight: bold">
    您的浏览器不支持javascript!请检查是否禁用！
</div>
<div class="content" style="display: none">

<!-- BEGIN LOGIN FORM -->

<form class="form-vertical login-form" id="login">

    <h3 class="form-title">TAOCMS后台管理系统</h3>

    <div class="alert alert-error hide">

        <button class="close" data-dismiss="alert"></button>

        <span>请输入用户名和密码.</span>

    </div>

    <div class="control-group">

        <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->

        <label class="control-label visible-ie8 visible-ie9">用户名</label>

        <div class="controls">

            <div class="input-icon left">

                <i class="icon-user"></i>

                <input class="m-wrap placeholder-no-fix" type="text" placeholder="用户名" name="username"/>

            </div>

        </div>

    </div>

    <div class="control-group">

        <label class="control-label visible-ie8 visible-ie9">密码</label>

        <div class="controls">

            <div class="input-icon left">

                <i class="icon-lock"></i>

                <input class="m-wrap placeholder-no-fix" type="password" placeholder="密码" name="password"/>

            </div>

        </div>

    </div>

    <div class="form-actions">

        <label class="checkbox">

            <input type="checkbox" name="remember" value="1"/> 记住我

        </label>

        <button type="submit" class="btn green pull-right">

            登录 <i class="m-icon-swapright m-icon-white"></i>

        </button>

    </div>

    <div class="forget-password">

        <h5>忘记密码 ?</h5>

        <p>

            别着急, 点击 <a href="javascript:;" class="" id="forget-password">这里</a>

            重置密码.

        </p>

    </div>

</form>

<!-- END LOGIN FORM -->

<!-- BEGIN FORGOT PASSWORD FORM -->

<form class="form-vertical forget-form" >

    <h3 class="">忘记密码 ?</h3>

    <p>输入您的邮箱重置密码</p>

    <div class="control-group">

        <div class="controls">

            <div class="input-icon left">

                <i class="icon-envelope"></i>

                <input class="m-wrap placeholder-no-fix" type="text" placeholder="Email" name="email" />

            </div>

        </div>

    </div>

    <div class="form-actions">

        <button type="button" id="back-btn" class="btn">

            <i class="m-icon-swapleft"></i> 返回

        </button>

        <button type="submit" class="btn green pull-right">

            提交 <i class="m-icon-swapright m-icon-white"></i>

        </button>

    </div>

</form>

<!-- END FORGOT PASSWORD FORM -->
<!-- END REGISTRATION FORM -->

</div>

<!-- END LOGIN -->

<!-- BEGIN COPYRIGHT -->

<div class="copyright">

    2014 &copy; www.w2ex.com

</div>

<!-- END COPYRIGHT -->

<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->

<!-- BEGIN CORE PLUGINS -->

<script src="/tp3.2/Public/Admin/js/jquery-1.10.1.min.js" type="text/javascript"></script>

<script src="/tp3.2/Public/Admin/js/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>

<!-- IMPORTANT! Load jquery-ui-1.10.1.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->

<script src="/tp3.2/Public/Admin/js/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>

<script src="/tp3.2/Public/Admin/js/bootstrap.min.js" type="text/javascript"></script>

<!--[if lt IE 9]>

<script src="/tp3.2/Public/Admin/js/excanvas.min.js"></script>

<script src="/tp3.2/Public/Admin/js/respond.min.js"></script>

<![endif]-->

<script src="/tp3.2/Public/Admin/js/jquery.slimscroll.min.js" type="text/javascript"></script>

<script src="/tp3.2/Public/Admin/js/jquery.blockui.min.js" type="text/javascript"></script>

<script src="/tp3.2/Public/Admin/js/jquery.cookie.min.js" type="text/javascript"></script>

<script src="/tp3.2/Public/Admin/js/jquery.uniform.min.js" type="text/javascript" ></script>

<!-- END CORE PLUGINS -->

<!-- BEGIN PAGE LEVEL PLUGINS -->

<script src="/tp3.2/Public/Admin/js/jquery.validate.min.js" type="text/javascript"></script>

<!-- END PAGE LEVEL PLUGINS -->

<!-- BEGIN PAGE LEVEL SCRIPTS -->

<script src="/tp3.2/Public/Admin/js/app.js" type="text/javascript"></script>

<script src="/tp3.2/Public/Admin/js/login.js" type="text/javascript"></script>

<script src="/tp3.2/Public/Admin/js/jQuery.md5.js" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->

<script>
    jQuery(document).ready(function() {
        $("#noscript").hide();
        $(".content").show();
        App.init();

        Login.init();

    });

</script>

<!-- END JAVASCRIPTS -->

</html>