<?php
// +----------------------------------------------------------------------
// | When work is a pleasure, life is a joy!
// +----------------------------------------------------------------------
// | User: ShouKun Liu  |  Email:24147287@qq.com  | Time:2016/12/10 16:50
// +----------------------------------------------------------------------
// | TITLE: this to do?
// +----------------------------------------------------------------------
use yii\helpers\Url;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta charset="utf-8" />
    <title>Login Page - Ace Admin</title>

    <meta name="description" content="User login page" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

    <!-- bootstrap & fontawesome -->
    <link rel="stylesheet" href="<?=Url::base()?>/aceAdmin/assets/css/bootstrap.css" />
    <link rel="stylesheet" href="<?=Url::base()?>/aceAdmin/assets/css/font-awesome.css" />

    <!-- text fonts -->
    <link rel="stylesheet" href="<?=Url::base()?>/aceAdmin/assets/css/ace-fonts.css" />

    <!-- ace styles -->
    <link rel="stylesheet" href="<?=Url::base()?>/aceAdmin/assets/css/ace.css" />

    <!--[if lte IE 9]>
    <link rel="stylesheet" href="<?=Url::base()?>/aceAdmin/assets/css/ace-part2.css" />
    <![endif]-->
    <link rel="stylesheet" href="<?=Url::base()?>/aceAdmin/assets/css/ace-rtl.css" />

    <!--[if lte IE 9]>
    <link rel="stylesheet" href="<?=Url::base()?>/aceAdmin/assets/css/ace-ie.css" />
    <![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->

    <!--[if lt IE 9]>
    <script src="<?=Url::base()?>/aceAdmin/assets/js/html5shiv.js"></script>
    <script src="<?=Url::base()?>/aceAdmin/assets/js/respond.js"></script>
    <![endif]-->
</head>

<body class="login-layout">
<div class="main-container">

    <?= $content ?>

</div><!-- /.main-container -->

<!-- basic scripts -->

<!--[if !IE]> -->
<script type="text/javascript">
    window.jQuery || document.write("<script src='<?=Url::base()?>/aceAdmin/assets/js/jquery.js'>"+"<"+"/script>");
</script>

<!-- <![endif]-->

<!--[if IE]>
<script type="text/javascript">
    window.jQuery || document.write("<script src='./assets/js/jquery1x.js'>"+"<"+"/script>");
</script>
<![endif]-->
<script type="text/javascript">
    if('ontouchstart' in document.documentElement) document.write("<script src='<?=Url::base()?>/aceAdmin/assets/js/jquery.mobile.custom.js'>"+"<"+"/script>");
</script>


</body>
</html>

