<!doctype html>
<html class="no-js">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>TrackBlog后台管理</title>
    <meta name="description" content="这是一个 index 页面">
    <meta name="keywords" content="index">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <link rel="icon" type="image/png" href="/public/i/favicon.png">
    <link rel="apple-touch-icon-precomposed" href="/public/i/app-icon72x72@2x.png">
    <meta name="apple-mobile-web-app-title" content="Amaze UI" />
    <link rel="stylesheet" href="/public/css/amazeui.min.css"/>
    <link rel="stylesheet" href="/public/css/admin.css">
</head>
<body>
<!--[if lte IE 9]>
<p class="browsehappy">你正在使用<strong>过时</strong>的浏览器，请升级浏览器</a>
    以获得更好的体验！</p>
<![endif]-->

<header class="am-topbar admin-header">
    <div class="am-topbar-brand">
        <strong>TrackBlog</strong> <small>后台管理</small>
    </div>

    <button class="am-topbar-btn am-topbar-toggle am-btn am-btn-sm am-btn-success am-show-sm-only" data-am-collapse="{target: '#topbar-collapse'}"><span class="am-sr-only">导航切换</span> <span class="am-icon-bars"></span></button>

    <div class="am-collapse am-topbar-collapse" id="topbar-collapse">

        <ul class="am-nav am-nav-pills am-topbar-nav am-topbar-right admin-header-list">
            <li><a href="javascript:;"><span class="am-icon-envelope-o"></span> 收件箱 <span class="am-badge am-badge-warning">5</span></a></li>
            <li class="am-dropdown" data-am-dropdown>
                <a class="am-dropdown-toggle" data-am-dropdown-toggle href="javascript:;">
                    <span class="am-icon-users"></span> 管理员 <span class="am-icon-caret-down"></span>
                </a>
                <ul class="am-dropdown-content">
                    <li><a href="#"><span class="am-icon-user"></span> 资料</a></li>
                    <li><a href="#"><span class="am-icon-cog"></span> 设置</a></li>
                    <li><a href="#"><span class="am-icon-power-off"></span> 退出</a></li>
                </ul>
            </li>
            <li class="am-hide-sm-only"><a href="javascript:;" id="admin-fullscreen"><span class="am-icon-arrows-alt"></span> <span class="admin-fullText">开启全屏</span></a></li>
        </ul>
    </div>
</header>

<div class="am-cf admin-main">
    <!-- sidebar start -->
    <div class="admin-sidebar am-offcanvas" id="admin-offcanvas">
        <div class="am-offcanvas-bar admin-offcanvas-bar">
            <ul class="am-list admin-sidebar-list">
                <li><a href="/index.php/admin"><span class="am-icon-home"></span> 首页</a></li>
                <li class="admin-parent">
                    <a class="am-cf" data-am-collapse="{target: '#collapse-managers'}"><span class="am-icon-file"></span> 内容管理 <span class="am-icon-angle-right am-fr am-margin-right"></span></a>
                    <ul class="am-list am-collapse admin-sidebar-sub am-in" id="collapse-managers">
                        <li><a href="/index.php/admin/article/add" class="am-cf"><span class="am-icon-check"></span> 添加文章</a></li>
                        <li><a href="/index.php/admin/article/show_list"><span class="am-icon-puzzle-piece"></span> 管理文章</a></li>
                        <li><a href="/index.php/admin/category/show_list"><span class="am-icon-th"></span> 管理栏目</a></li>
<!--                        <li><a href="admin-log.html"><span class="am-icon-calendar"></span> 管理评论</a></li>-->
<!--                        <li><a href="admin-404.html"><span class="am-icon-bug"></span> 友情链接</a></li>-->
                    </ul>
                </li>
                <li class="admin-parent">
                    <a class="am-cf" data-am-collapse="{target: '#collapse-settings'}"><span class="am-icon-file"></span> 综合设置 <span class="am-icon-angle-right am-fr am-margin-right"></span></a>
                    <ul class="am-list am-collapse admin-sidebar-sub am-in" id="collapse-settings">
                        <li><a href="/" class="am-cf"><span class="am-icon-check"></span> 网站首页</a></li>
<!--                        <li><a href="admin-help.html"><span class="am-icon-puzzle-piece"></span> 网站设置</a></li>-->
<!--                        <li><a href="admin-gallery.html"><span class="am-icon-th"></span> 账户修改</a></li>-->
<!--                        <li><a href="admin-log.html"><span class="am-icon-calendar"></span> 导入导出</a></li>-->
                    </ul>
                </li>
                <li><a href="/index.php/admin/admin/logout"><span class="am-icon-table"></span> 注销</a></li>
            </ul>

            <div class="am-panel am-panel-default admin-sidebar-panel">
                <div class="am-panel-bd">
                    <p><span class="am-icon-bookmark"></span> 公告</p>
                    <p>时光静好，与君语；细水流年，与君同。</p>
                </div>
            </div>
        </div>
    </div>
    <!-- sidebar end -->
