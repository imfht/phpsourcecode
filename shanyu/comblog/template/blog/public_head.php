<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= $SEO_DESCRIPTION ?>">
    <meta name="keywords" content="<?= $SEO_KEYWORDS ?>">
    <title><?php if($TITLE): echo $TITLE.' - '; endif; ?><?= $SEO_TITLE ?></title>
    <link rel="stylesheet" href="/assets/blog/css/normalize.css">
    <link rel="stylesheet" href="/assets/blog/css/public.css">
</head>
<body>
<div id="layout">
    <div id="layout-left" class="layout-left">
        <div class="header">
            <div class="logo">
                <a href="/"><?= $SEO_TITLE ?></a>
            </div>
            <div class="nav" id="navList">
                <ul>
                    <li>
                        <a href="/">博客首页</a>
                    </li>
                    <li>
                        <a href="/article/category.html">文章分类</a>
                    </li>
                    <li>
                        <a href="/article/archive.html">日期归档</a>
                    </li>
                    <li>
                        <a href="/article/tag.html">相关标签</a>
                    </li>
                    <li>
                        <a href="/about.html">关于博主</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="footer">
            <div class="container">
                <div class="copyright">
                    Copyright © <?php echo getenv('APP_NAME'); ?>
                </div>
            </div>
        </div>
    </div>
    <div id="layout-right" class="layout-right">
        <div class="top-mini cl">
            <div class="logo">
                <a href="/">悠悠山雨</a>
            </div>
            <div class="btns">
                <a href="javascript:;" id="navListBtn">菜单</a>
            </div>
        </div>

<?php
$_scripts.=<<<str
        <script>
            $('#navListBtn').click(function(){
                var menu = $('#layout-left');
                if(menu.is(':hidden')){
                    menu.show();
                }else{
                    menu.hide();
                }
            });
        </script>
str;
?>

