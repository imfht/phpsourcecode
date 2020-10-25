<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?=$title?> | <?=$config['site_info']['name']?></title>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta name="keywords" content="<?=xss_filter($keywords)?>">
<meta name="description" content="<?=xss_filter($description)?>">
<link rel="stylesheet" href="<?=$config['files']['web']['nprogress.css']?>">
<link rel="stylesheet" href="/static/<?=$theme_id?>/layui/css/layui.css">
<link rel="stylesheet" href="/static/<?=$theme_id?>/css/global.css">
<?php if (!empty($skin)): ?>
<style type="text/css">
    <?=$skin['skin_style']?>
    <?php if ($skin['lock_background'] == 2): ?>
        html{background-attachment: fixed;}
    <?php endif;?>
    body>.main{margin-top: 100px;}
    .skins .skin .skin_img_wrap .skin_link{width: 194px;}
    .topic-list .item{width: 297px;}
</style>
<?php endif;?>
<?php require_once VIEWPATH . "inc/s2c.inc.php";?>
<script type="text/javascript" src="<?=$config['files']['web']['jquery.js']?>"></script>
<script type="text/javascript" src="<?=$config['files']['web']['plupload.js']?>"></script>
<script type="text/javascript" src="<?=$config['files']['web']['qiniu.js']?>"></script>
<script type="text/javascript" src="<?=$config['files']['web']['file_progress.js']?>"></script>
<script type="text/javascript" src='<?=$config['files']['web']['nprogress.js']?>'></script>
<script type="text/javascript" src='<?=$config['files']['web']['pjax.js']?>'></script>
<script type="text/javascript" src="/static/<?=$theme_id?>/layui/lay/dest/layui.all.js"></script>
<script type="text/javascript" src="<?=$config['files']['web']['common.js']?>"></script>
</head>
<body>
<div id="header" class="header">
    <div class="main">
        <a class="logo pjax" href="/" title="<?=$config['site_info']['name']?>"><span><?=$config['site_info']['name']?></span></a>
        <div class="nav">
            <a class="pjax<?=$active == 'all' ? ' nav-this' : ''?>" href="/">
                <span>全部</span>
            </a>
            <a class="pjax<?=$active == 'q' ? ' nav-this' : ''?>" href="/q">
                <span>问答</span>
            </a>
            <a class="pjax<?=$active == 'news' ? ' nav-this' : ''?>" href="/news">
                <span>头条</span>
            </a>
            <a class="pjax<?=$active == 'topic' ? ' nav-this' : ''?>" href="/topic">
                <span>话题</span>
            </a>
            <a<?=$active == 'mall' ? ' class="nav-this"' : ''?> href="/mall">
                <span>商城</span>
            </a>
        </div>
        <div id="search_box">
            <form action="https://www.baidu.com/s" method="get" onsubmit="return search_onsubmit();" accept-charset="utf-8" target="_blank">
                <a class="search_icon" href="javascript:;" title="搜索"><i class="iconfont">&#xe601;</i></a>
                <input id="search_wd" name="wd" type="text" value="" onblur="search_input_blur(this);" autocomplete="off" placeholder="搜索内容，直接回车">
            </form>
        </div>
        <div class="nav-user">
            <span class="icons">
                <a href="/article/add" title="发贴"><i class="iconfont">&#xe6e6;</i></a>
            </span>
            <?php if (!isset($user)): ?>
                <!-- 未登入状态 -->
                <span>
                    <a href="/account/signin">登录</a>
                    <a class="ml10" href="/account/signup">注册</a>
                </span>
                <p class="out-login">
                    <?php require VIEWPATH . "$theme_id/inc/open_signin_btn_lists.inc.php";?>
                </p>
            <?php else: ?>
                <!-- 登入后的状态 -->
                <span>
                    <a class="avatar pjax" href="/u">
                        <img src="<?=create_avatar_url($user['id'], $user['avatar_ext'])?>">
                        <cite><?=$user['nickname']?></cite>
                    </a>
                    <?php if (isset($msg_to_me_counts) && $msg_to_me_counts > 0): ?>
                        <a class="nav-message" href="/u/msg" title="您有<?=$msg_to_me_counts?>条未阅读的消息"><?=$msg_to_me_counts?></a>
                    <?php endif;?>
                </span>
                <div class="nav">
                    <?php if ($user['id'] == 1): ?>
                        <a href="/admin">管理</a>
                    <?php endif;?>
                    <a href="/account/signout">退出</a>
                </div>
            <?php endif;?>
        </div>
    </div>
    <a class="icon_skins pjax" href="/skin"></a>
</div>