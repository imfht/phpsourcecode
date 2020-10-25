<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<!doctype html>
<html>
<head>
    <meta charset="<?php $this->options->charset(); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
    <meta name="renderer" content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title><?php $this->archiveTitle(array(
            'category'  =>  _t('分类 %s 下的文章'),
            'search'    =>  _t('包含关键字 %s 的文章'),
            'tag'       =>  _t('标签 %s 下的文章'),
            'author'    =>  _t('%s 发布的文章')
        ), '', ' - '); ?><?php $this->options->title(); ?></title>

    <!-- 使用url函数转换相关路径 -->
    <link rel="stylesheet" href="<?php $this->options->themeUrl('dist/css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="<?php $this->options->themeUrl('dist/css/material.min.css'); ?>">
    <link rel="stylesheet" href="<?php $this->options->themeUrl('dist/css/ripples.min.css'); ?>">
    <link rel="stylesheet" href="<?php $this->options->themeUrl('dist/css/material-wfont.min.css'); ?>">
    <link rel="stylesheet" href="<?php $this->options->themeUrl('dist/css/customs.css'); ?>">

    <!--[if lt IE 9]>
    <script src="http://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
    <script src="http://cdn.staticfile.org/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    <!-- 通过自有函数输出HTML头部信息 -->
    <?php $this->header(); ?>
</head>
<body>

<header id="header">
    <div class="navbar navbar-inverse navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-inverse-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?php $this->options->siteUrl(); ?>">
                    <?php $this->options->title(); ?>
                </a>
            </div>
            <div class="navbar-collapse collapse navbar-inverse-collapse">
                <ul class="nav navbar-nav">
                    <li<?php if($this->is('index')): ?> class="active"<?php endif; ?>>
                        <a href="<?php $this->options->siteUrl(); ?>"><?php _e('首页'); ?></a>
                    </li>
                    <?php $this->widget('Widget_Contents_Page_List')->to($pages); ?>
                    <?php while($pages->next()): ?>
                    <li<?php if($this->is('page', $pages->slug)): ?> class="active"<?php endif; ?>><a href="<?php $pages->permalink(); ?>"><?php $pages->title(); ?></a></li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </div>
    </div>
</header>

<div class="container">
    <div class="row">

    
    
