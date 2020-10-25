<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">


    <title><?= $title ?></title>

    <link rel="stylesheet" type="text/css" media="screen" href="<?= THEMEPATH ?>css/<?=$style?>.css" id="bootstrapStyle"/>
    <link rel="stylesheet" type="text/css" media="screen" href="<?= THEMEPATH ?>/css/font-awesome.min.css"/>
    <link rel="stylesheet" type="text/css" media="screen" href="<?= THEMEPATH ?>/css/custom.css"/>

    <script src="<?= THEMEPATH ?>/js/jquery.min.js"></script>
    <script src="<?= THEMEPATH ?>/js/jquery.cookie.js"></script>
    <script src="<?= THEMEPATH ?>/js/bootstrap.min.js"></script>
    <script src="<?= THEMEPATH ?>/js/custom.js"></script>

    <!--[if lt IE 9]>
    <script src="<?=THEMEPATH?>/js/html5shiv.min.js"></script>
    <script src="<?=THEMEPATH?>/js/respond.min.js"></script>
    <script src="<?=THEMEPATH?>/css/font-awesome-ie7.min.js"></script>
    <![endif]-->

    <script type="text/javascript">
        $(function(){
                $('.maskLayer').remove();
        });
    </script>

</head>
<body>
<div class="maskLayer">
    <img src="<?=THEMEPATH?>images/loading.gif">
</div>
<!-- Header -->
<nav class="navbar navbar-fixed-top navbar-inverse">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-2" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?=SITEPATH?>">
                <img src="<?= THEMEPATH ?>/images/index.png" width="20px"/>
            </a>
        </div>
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-2">
            <ul class="nav navbar-nav">
                <li><a href="<?= SITEPATH ?>">首页</a></li>
                <li><a href="<?= site_url('/admin') ?>">后台</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- End Header -->


<!-- Main -->
<div class="main">
