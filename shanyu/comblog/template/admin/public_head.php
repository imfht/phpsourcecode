<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin</title>
    <link rel="stylesheet" href="/assets/admin/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/admin/css/bootstrap-theme.min.css">
    <script src="/assets/admin/js/jquery.min.js"></script>
    <script src="/assets/admin/js/bootstrap.min.js"></script>

    <link rel="stylesheet" href="/assets/admin/css/common.css">
    <script src="/assets/admin/js/common.js"></script>
</head>
<body>

<div class="layout-head clearfix">
    <div class="nav pull-left">
        <ul class="list-inline">
            <li><a href="/admin?c=Home&a=index">管理首页</a></li>
            <li><a href="/admin?c=Article&a=index">文章管理</a></li>
            <li><a href="/admin?c=ArticleCategory&a=index">文章分类</a></li>
            <li><a href="/admin?c=Tag&a=index">分类标签</a></li>
        </ul>
    </div>
    <div class="nav pull-right">
        <ul class="list-inline">
            <li>
                <a href="/admin?c=Home&a=logout">退出</a>
            </li>
        </ul>
    </div>
</div>
<div class="layout-main">
