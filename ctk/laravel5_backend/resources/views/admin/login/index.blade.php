<!doctype html>
<html lang="en"><head>
    <meta charset="utf-8">
    <title>系统管理</title>
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="<?php echo csrf_token(); ?>" />

    <link rel="stylesheet" type="text/css" href="<?php echo loadStatic('/lib/bootstrap/css/bootstrap.css'); ?>">
    <link rel="stylesheet" href="<?php echo loadStatic('/lib/font-awesome/css/font-awesome.css'); ?>">

    <script src="<?php echo loadStatic('/lib/jquery-1.11.1.min.js'); ?>" type="text/javascript"></script>

    <link rel="stylesheet" type="text/css" href="<?php echo loadStatic('/stylesheets/theme.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo loadStatic('/stylesheets/premium.css'); ?>">
    <style type="text/css">
        #line-chart {
            height:300px;
            width:800px;
            margin: 0px auto;
            margin-top: 1em;
        }
        .navbar-default .navbar-brand, .navbar-default .navbar-brand:hover { 
            color: #fff;
        }
        .none {
            display: none;
        }
        .notic {
            color: red;
            display: none;
        }
    </style>

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="<?php echo loadStatic('/lib/html5.js'); ?>"></script>
      <script type="text/javascript">
        $(document).ready(function(){
            $('body').html('您使用的是IE浏览器，仅支持IE9以上版本，建议使用firefox、chrome浏览器，以或得最佳体验。');
        });
        
      </script>
    <![endif]-->
  
</head>
<body class="theme-blue">

    <!--[if lt IE 7 ]> <body class="ie ie6"> <![endif]-->
    <!--[if IE 7 ]> <body class="ie ie7 "> <![endif]-->
    <!--[if IE 8 ]> <body class="ie ie8 "> <![endif]-->
    <!--[if IE 9 ]> <body class="ie ie9 "> <![endif]-->
    <!--[if (gt IE 9)|!(IE)]><!--> 
   
    <!--<![endif]-->

    <div class="dialog">
    <div class="panel panel-default">
        <p class="panel-heading no-collapse">用户登陆</p>
        <div class="panel-body" style="text-align:center;" id="loading"><img src="/images/loading-icons/loading7.gif"></div>
        <div id="login-form" class="panel-body none">
            <div class="form-group notic" id="msg"></div>
            <div class="form-group input-group-sm">
                <label>用户帐号</label>
                <input type="text" class="form-control span12 login-form" id="username">
            </div>
            <div class="form-group input-group-sm">
            <label>用户密码</label>
                <input type="password" class="form-control span12 login-form" id="password">
            </div>
            <a href="javascript:;" class="btn btn-sm btn-primary pull-right" id="submit">登陆</a>

            <label class="remember-me none"><input type="checkbox"> 记住</label>
            <div class="clearfix"></div>
        </div>
    </div>
    </div>
    <div style="color:#bbb;font-size:12px; margin:0 auto; width:200px;">最佳体验在IE9+、firefox、chrome</div>
    <script src="<?php echo loadStatic('/lib/bootstrap/js/bootstrap.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo loadStatic('/crypto/md5.js'); ?>" ></script>
    <script type="text/javascript" src="<?php echo loadStatic('/lib/seajs/sea.js'); ?>" ></script>

    <script type="text/javascript">
        seajs.config({
            base: "/lib/seajs/modules/",
            alias : {
                "jquery" : "jquery.js"
            }
        });

        seajs.use('login', function(login) {
            login.submit();//侦听登录按钮的点击事件
            login.prelogin();//取得一次性的密钥
        });

        $(document).ready(function(){
            $('.login-form').keyup(function(event){
                if (event.keyCode == 13) {
                    $('#submit').trigger('click');
                }
            });
        });

    </script>
  
</body></html>