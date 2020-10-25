<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title><?=$title?> | <?=$config['site_info']['name']?>管理后台</title>
<meta name="renderer" content="webkit">
<meta name="force-rendering" content="webkit">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="format-detection" content="telephone=no">
<meta http-equiv="Cache-Control" content="no-siteapp">
<meta name="keywords" content="<?=$config['site_info']['meta']['keywords']?>">
<meta name="description" content="<?=$config['site_info']['meta']['description']?>">
<link rel="stylesheet" href="<?=$config['files']['web']['amazeui.css']?>">
<link rel="stylesheet" href="<?=$config['files']['web']['admin_common.css']?>">
<?php require_once VIEWPATH . 'inc/s2c.inc.php';?>
<script type="text/javascript" src="<?=$config['files']['web']['jquery.js']?>"></script>
<script type="text/javascript" src="<?=$config['files']['web']['amazeui.js']?>"></script>
<script type="text/javascript" src="<?=$config['files']['web']['layer.js']?>"></script>
<script type="text/javascript" src="<?=$config['files']['web']['zeroclipboard.js']?>"></script>
<script type="text/javascript" src="<?=$config['files']['web']['common.js']?>"></script>
<script type="text/javascript" src="<?=$config['files']['web']['admin_common.js']?>"></script>
</head>
<body>
<div class="am-topbar am-topbar-inverse">
    <div class="am-topbar-brand">
        <a href="/admin"><strong><?=$config['site_info']['name']?></strong></a>
        <a class="am-margin-left-sm" href="/"><small>返回首页</small></a>
    </div>
    <div class="am-collapse am-topbar-collapse" id="topbar-collapse">
        <ul class="am-nav am-nav-pills am-topbar-nav am-topbar-right admin-header-list">
            <li class="am-dropdown" data-am-dropdown>
                <a class="am-dropdown-toggle" data-am-dropdown-toggle href="javascript:;">
                    <?=$user['nickname']?> <span class="am-icon-caret-down"></span>
                </a>
                <ul class="am-dropdown-content">
                    <li><a href="/account/signout"><span class="am-icon-power-off"></span> 退出</a></li>
                </ul>
            </li>
        </ul>
    </div>
</div>
<div class="am-cf admin-main">
    <!-- sidebar start -->
    <div id="admin-sidebar" class="admin-sidebar">
        <ul id="admin-sidebar_nav" class="am-list admin-sidebar-list">
            <li<?=$active == 'article' ? ' class="active"' : ''?>>
                <a href="/admin/article"><i class="iconfont">&#xe650;</i> 文章</a>
            </li>
            <li<?=$active == 'comment' ? ' class="active"' : ''?>>
                <a href="/admin/comment"><i class="iconfont">&#xe703;</i> 评论</a>
            </li>
            <li<?=$active == 'u' ? ' class="active"' : ''?>>
                <a href="/admin/u"><i class="iconfont">&#xe621;</i> 用户</a>
            </li>
            <li<?=$active == 'topic' ? ' class="active"' : ''?>>
                <a href="/admin/topic"><i class="iconfont">&#xe666;</i> 话题</a>
            </li>
        </ul>
        <div id="online_box"></div>
    </div>
    <!-- sidebar end -->
