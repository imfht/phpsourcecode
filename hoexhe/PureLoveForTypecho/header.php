<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<!DOCTYPE HTML>
<html class="no-js">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta charset="<?php $this->options->charset(); ?>">
    <link rel="dns-prefetch" href="//sdn.geekzu.org">
    <link rel='dns-prefetch' href="<?php $this->options->siteUrl(); ?>"/>
    <link rel="dns-prefetch" href="//cdn.staticfile.org">
    <link rel="dns-prefetch" href="//secure.gravatar.com">
    <link rel="dns-prefetch" href="//cn.gravatar.com">
    <link rel="dns-prefetch" href="//cdn.v2ex.com">
    <meta name="viewport" content="initial-scale=1.0,user-scalable=no">
    <meta name="theme-color" content="#ff8c83">
    <meta name="renderer" content="webkit">
    <link rel="canonical" href="<?php $this->options->siteUrl(); ?>"/>
    <title><?php $this->archiveTitle(array(
            'category'  =>  _t('分类 %s 下的文章'),
            'search'    =>  _t('包含关键字 %s 的文章'),
            'tag'       =>  _t('标签 %s 下的文章'),
            'author'    =>  _t('%s 发布的文章')
        ), '', ' - '); ?><?php $this->options->title(); ?></title>

    <!-- 主题样式 -->
    <link rel="stylesheet" href="<?php $this->options->themeUrl('css/style.css'); ?>">
    <!-- emojionearea表情 -->
    <link rel="stylesheet" href="//cdn.staticfile.org/emojionearea/3.4.1/emojionearea.min.css">
    <!-- icon -->
    <link rel="icon" href="<?php $this->options->iconUrl ? $this->options->iconUrl() : $this->options->themeUrl('images/favicon.ico'); ?>" type="image/x-icon"/>
    <!-- 图标库 -->
    <link rel="stylesheet" href="//cdn.staticfile.org/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- 代码高亮 -->
    <link rel="stylesheet" href="//cdn.staticfile.org/highlight.js/9.13.1/styles/vs.min.css">
    <!-- nprogress -->
    <link rel="stylesheet" href="//cdn.staticfile.org/nprogress/0.2.0/nprogress.min.css">
    <!-- fancybox 图片灯箱插件 -->
    <link rel="stylesheet" href="//cdn.staticfile.org/fancybox/3.5.7/jquery.fancybox.min.css">
    <!-- 通过自有函数输出HTML头部信息 -->
    <?php $this->header(); ?>
    <!-- End 通过自有函数输出HTML头部信息 -->
    <?php if ($this->options->advertisingJs): // 广告代码 ?>
        <?php $this->options->advertisingJs(); ?>
    <?php endif; // 广告代码 ?>
</head>
<body>
<!--[if lt IE 8]>
    <div class="browsehappy" role="dialog">当前网页 <strong>不支持</strong> 你正在使用的浏览器. 为了正常的访问, 请 <a href="https://browsehappy.com/">升级你的浏览器</a>.</div>
<![endif]-->
<!--头部-->
<header class="header">
    <div id="navbar">
        <div class="inner clearfix">
            <div id="caption">
                <a href="<?php $this->options->siteUrl(); ?>">
                    <img  src="<?php $this->options->logoUrl ? $this->options->logoUrl() : $this->options->themeUrl('images/logo-160x60.png'); ?>" alt="<?php $this->options->title() ?>">
                </a>
            </div>
            <?php if ($this->options->sidebarBlock && in_array('showSiteInfo', $this->options->sidebarBlock)): ?>
                <div class="site-info">
                    <p class="title"><?php $this->options->title(); ?></p>
                    <p class="description"><?php $this->options->description() ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</header>
<!--菜单-->
<div class="menu-btn">
    <div class="menu-left">
        <a href="#" class="fa fa-bars" aria-hidden="true"></a>
    </div>
    <div class="menu-right"><?php $this->options->title() ?></div>
</div>
<div id="navigation">
    <div class="inner clearfix">
        <div id="menus" class="mynav">
            <div class="menu-menu-container">
                <ul id="menu-menu" class="menu font-size-14">
                    <li>
                        <a href=<?php $this->options->siteUrl(); ?>><i class="fa fa-home"></i> 首页</a>
                    </li>
                    <!--分类-->
                    <li class="menu-item">
                        <a href="#" class="sub-menu-click"><i class="fa fa-chevron-down"></i> 分类</a>
                        <ul class="sub-menu">
                            <?php $this->widget('Widget_Metas_Category_List')->to($cats);?>
                            <?php while ($cats->next()): ?>
                                <li>
                                    <a href="<?php $cats->permalink()?>" title="<?php $cats->name()?>">
                                        <i class="fa fa-circle-o"></i>
                                        <span><?php $cats->name()?></span>
                                    </a>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    </li>
                    <!--独立的页面-->
                    <?php $this->widget('Widget_Contents_Page_List')->to($pages); ?>
                    <?php while($pages->next()): ?>
                    <li>
                        <a href="<?php $pages->permalink(); ?>" title="<?php $pages->title(); ?>"><i class="fa fa-list-ol"></i> <?php $pages->title(); ?></a>
                    </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </div>
        <div class="navsearch">
            <form action="<?php $this->options->siteUrl(); ?>" method="get" id="searchform" data-pjax>
                <input name="s" type="text" class="searchtext" value="" placeholder="输入关键字搜索"/>
                <input id="searchbtn" type="submit" class="button" value="搜索">
            </form>
        </div>
    </div>
</div>
<section id="container">
    <section id="content">
        <!-- Pjax需要替换window.TypechoComment对象 通过自有函数输出评论JS -->
        <?php $this->header('commentReply=1&description=0&keywords=0&generator=0&template=0&pingback=0&xmlrpc=0&wlw=0&rss2=0&rss1=0&antiSpam=0&atom'); ?>
        <!-- End Pjax需要替换window.TypechoComment对象 通过自有函数输出评论JS -->