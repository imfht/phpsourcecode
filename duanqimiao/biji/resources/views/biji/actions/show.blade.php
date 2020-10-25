<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    {{--移动或响应式web页面缩放设置--}}
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=no">
    <title>笔记信息</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet">
    <style type="text/css">
        .bs-callout {
            width: 70%;
            padding: 20px;
            margin: 50px auto;
            border: 1px solid #eee;
            border-left-width: 5px;
            border-radius: 3px;
        }

        .bs-callout-info {
            border-left-color: #5bc0de;
        }
        footer{
            margin-top: 1em;
        }
        .home{
            font-size: 1.2em;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div style="text-align: right">
    <ul class="list-group">
        <li class="list-group-item list-group-item-info">笔友 Be Yourself</li>
    </ul>
</div>
<blockquote class="bs-callout bs-callout-info">
    <p>
        <a  class="home" href="/biji/">主页</a> » 笔记信息
    </p>
    <footer>标题：{{ $biji->title }} <cite title="Source Title">By {{ Auth::user()->name }}</cite></footer>
    <footer>创建时间 ：<cite title="Source Title">{{ $biji->created_at }}</cite></footer>
    <footer>更新时间：<cite title="Source Title">{{ $biji->updated_at }}</cite></footer>
</blockquote>
</body>
</html>
