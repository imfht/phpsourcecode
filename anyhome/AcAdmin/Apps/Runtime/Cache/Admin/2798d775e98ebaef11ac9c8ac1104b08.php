<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
   

    <title>ApiCloud云端管理平台</title>

    <!-- Required CSS Files -->
    <link type="text/css" href="assets/css/required/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href='http://fonts.useso.com/css?family=Roboto:400,300&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
    <link type="text/css" href="assets/js/required/jquery-ui-1.11.0.custom/jquery-ui.min.css" rel="stylesheet" />
    <link type="text/css" href="assets/js/required/jquery-ui-1.11.0.custom/jquery-ui.structure.min.css" rel="stylesheet" />
    <link type="text/css" href="assets/js/required/jquery-ui-1.11.0.custom/jquery-ui.theme.min.css" rel="stylesheet" />
    <link type="text/css" href="assets/css/required/mCustomScrollbar/jquery.mCustomScrollbar.min.css" rel="stylesheet" />
    <link type="text/css" href="assets/css/required/icheck/all.css" rel="stylesheet" />
    <link type="text/css" href="assets/fonts/metrize-icons/styles-metrize-icons.css" rel="stylesheet">

  
    <!-- add CSS files here -->

    <!-- More Required CSS Files -->
    <link type="text/css" href="assets/css/styles-core.css" rel="stylesheet" />
    <link type="text/css" href="assets/css/styles-core-responsive.css" rel="stylesheet" />

    <!-- Demo CSS Files -->
    <link type="text/css" href="assets/css/demo-files/pages-signin-signup.css" rel="stylesheet" />

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="assets/js/required/misc/ie10-viewport-bug-workaround.js"></script>
<link rel="stylesheet" type="text/css" href="assets/css/animate.css" />

    <!--[if IE 7]>
    <link type="text/css" href="assets/css/required/misc/style-ie7.css" rel="stylesheet">
    <script type="text/javascript" src="assets/fonts/lte-ie7.js"></script>
    <![endif]-->
    <!--[if IE 8]>
    <link type="text/css" href="assets/css/required/misc/style-ie8.css" rel="stylesheet">
    <![endif]-->
    <!--[if lte IE 8]>
    <script type="text/javascript" src="assets/css/required/misc/excanvas.min.js"></script>
    <![endif]-->
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="assets/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="assets/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<style type="text/css">
body{
    background: url('assets/main_bg.jpg');
    background-size: 100%;
    background-repeat: no-repeat;
}
</style>
<body>

    <div class="container-fluid">
        <div id="body-container">
            <div class="page animated bounceInDown">
            <div class="standalone-page">
                <div class="standalone-page-logo">
                    
                </div>
                <div class="standalone-page-content" data-border-top="multi">
                    <div class="standalone-page-block">
                        <div class="row">
                            <div class="col-xs-12">
                                <h2 class="heading">
                                    <span aria-hidden="true" class="icon icon-key"></span>
                                    <span class="main-text">
                                        ApiCloud云端管理平台
                                    </span>
                                </h2>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <form submit-ajax role="form" class="form-horizontal" method="post" action="<?php echo U('Admin/Public/cklogin');?>">
                                    <div class="form-group">
                                        <label for="inputEmail" class="col-sm-3 control-label">登陆账号</label>
                                        <div class="col-sm-9">
                                            <input autocomplete="off" class="form-control" id="inputEmail" placeholder="请输入登陆账号" type="text" name="account">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputPassword" class="col-sm-3 control-label">登陆密码</label>
                                        <div class="col-sm-9">
                                            <input autocomplete="off" class="form-control" id="inputPassword" placeholder="请输入登陆密码" type="password" name="password">
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <div class="col-sm-offset-3 col-sm-9">
                                            <button type="submit" class="btn btn-success">登陆</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="assets/js/required/jquery-1.11.1.min.js"></script>
    <script type="text/javascript" src="assets/js/required/jquery-ui-1.11.0.custom/jquery-ui.min.js"></script>
    <script type="text/javascript" src="assets/js/required/bootstrap/bootstrap.min.js"></script>
    <script type="text/javascript" src="assets/js/required/jquery.easing.1.3-min.js"></script>
    <script type="text/javascript" src="assets/js/required/jquery.mCustomScrollbar.min.js"></script>
    <script type="text/javascript" src="assets/js/required/misc/jquery.mousewheel-3.0.6.min.js"></script>
    <script type="text/javascript" src="assets/js/required/misc/retina.min.js"></script>
    <script type="text/javascript" src="assets/js/required/icheck.min.js"></script>
    <script type="text/javascript" src="assets/js/required/misc/jquery.ui.touch-punch.min.js"></script>
    <script type="text/javascript" src="assets/js/required/circloid-functions.js"></script>

   
    <!-- add optional JS plugin files here -->

    <!-- REQUIRED: User Editable JS Files -->
    <script type="text/javascript" src="assets/js/app.js"></script>
    <!-- add additional User Editable files here -->

    <!-- Demo JS Files -->
    <script type="text/javascript" src="assets/js/demo-files/pages-signin-1.js"></script>
<script type="text/javascript" src="assets/js/jquery.pjax.js"></script>
<script type="text/javascript" src="assets/nprogress/nprogress.js"></script>
<link rel="stylesheet" type="text/css" href="assets/nprogress/nprogress.css" />
<script type="text/javascript" src="assets/js/bootstrap-confirmation.js"></script>
<script type="text/javascript" src="assets/js/bootstrap-dialog.js"></script>
<script type="text/javascript" src="assets/js/jquery.bootstrap-growl.js"></script>
<script type="text/javascript" src="assets/js/function.js"></script>    
 
</body>
</html>