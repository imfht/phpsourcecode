<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    {{--移动或响应式web页面缩放设置--}}
    <meta name="viewport" content="width=device-width,initial-scale=0.6,maximum-scale=1.0,user-scalable=no">
    <title>如何获得笔记链接</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet">
    <script language="JavaScript" src="{{ URL::asset('/') }}js/jquery.js"></script>
    <script language="JavaScript" src="{{ URL::asset('/') }}js/guide.js"></script>
    <link rel="stylesheet" media="screen" href="{{ asset('/css/guide.css') }}">
    {{--引入artDialog插件--}}
    <link rel="stylesheet" href="{{ asset('/css/ui-dialog.css') }}">

    <script src="{{ URL::asset('/') }}js/dialog-min.js"></script>
    {{--END--}}
</head>
<body>
<div class="container">
    <div class="title">
        <h1>如何获得笔记链接</h1>
    </div>
    <div class="list">
        本文包含以下内容：
        <ul>
            <li>
                如何生成笔记链接
            </li>
            <li>
                查看笔记链接
            </li>
        </ul>
        <div  class="content">
            <h2>如何生成笔记链接</h2>
            <p>自动生成链接</p>
            <ul>
                <li>打开所需的笔记</li>
                <li>点击页面右侧笔记标题的末尾处的按钮</li>
                <li>自动生成链接并复制到剪贴板</li>
                <li>在新标签页黏贴链接打开笔记</li>
            </ul>
        </div>

    <div class="help">
        这篇文章有帮助吗？
        <input type="hidden" name="articleId" value="2"/>
        <div class="isHelp yes"><i class="icon yes-img"></i><span> 是</span></div>
        <div class="isHelp no"><i class="icon no-img"></i><span> 否</span></div>
        <div class="fedBack">已有 <span id="count">{{ $count }}</span> 人觉得有帮助</div>
    </div>
</div>
</body>
</html>
