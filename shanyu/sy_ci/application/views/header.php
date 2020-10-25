<!DOCTYPE html>
<!--[if lte IE 6 ]> <html class="ie ie6 lte_ie7 lte_ie8" lang="zh-CN"> <![endif]-->
<!--[if IE 7 ]> <html class="ie ie7 lte_ie7 lte_ie8" lang="zh-CN"> <![endif]-->
<!--[if IE 8 ]> <html class="ie ie8 lte_ie8" lang="zh-CN"> <![endif]-->
<!--[if IE 9 ]> <html class="ie ie9" lang="zh-CN"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="zh-CN"> <!--<![endif]-->
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <meta name="renderer" content="webkit">
    <meta name="author" content="shanyu" />
    <meta name="robots" content="all" />
    <base href="<?= config_item('base_url') ?>" />
    <title><?= $seo['title'] ?></title>
    <meta name="description" content="<?= $seo['description'] ?>" />
    <meta name="keywords" content="<?= $seo['keywords'] ?>" />
    <link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
    <!--[if lt IE 9]>
      <script src="//cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="//cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
      <![endif]-->
  </head>
  <body>
    <link rel="stylesheet" href="/static/css/base.css">
    <div id="wrapper" class="container">
        <div class="row">
            <div class="col-xs-2">
                <div class="menu">
                    <a href="/">首页</a>
                    <?php foreach ($category_list as $key => $value): ?>
                        <a href="<?= '/article/'.$value['name'].'.html' ?>"><?= $value['title'] ?></a>
                    <?php endforeach ?>
                </div>
            </div>
            <div class="col-xs-10">
                <div class="main-tit clearfix">
                    <div id="title" class="pull-left"><?= $seo['title'] ?></div>
                </div>
                <div id="container">