<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    {{--移动或响应式web页面缩放设置--}}
    <meta name="viewport" content="width=device-width,initial-scale=0.6,maximum-scale=1.0,user-scalable=no">
    <title>如何分享笔记</title>
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
        <h1>如何分享笔记</h1>
    </div>
    <div class="list">
        本文包含以下内容：
        <ul>
            <li>
                通过发送邮件的方式分享笔记
            </li>
            <li>
                发表到笔友圈
            </li>
            <li>
                管理笔友圈
            </li>
        </ul>
        <div  class="content">
            <h2>通过发送邮件的方式分享笔记</h2>
            <p>选择要发送的笔记，验证邮箱合法，分享笔记</p>
            <ul>
                <li>打开要分享的笔记</li>
                <li>点击共享按钮</li>
                <li>填写你要分享的合法有效的电子邮箱地址</li>
                <li>分享笔记</li>
            </ul>
        </div>
        <div  class="content">
            <h2>发表到笔友圈</h2>
            <p>发表到笔友圈的笔记，对所有人可见，在笔友圈可收藏、点赞、评论</p>
            <ul>
                <li>打开要分享的笔记</li>
                <li>点击共享按钮</li>
                <li>填写你要分享的笔记的标签</li>
                <li>分享笔记</li>
            </ul>
        </div>

        <div  class="content">
            <h2>管理笔友圈</h2>
            <p>一旦将笔记将笔记发表到笔友圈，便是对所有人可见。</p>
            <h3>取消分享</h3>
            <p>如想从笔友圈中撤回，请进入笔友圈中，在我的分享请选择不在分享的笔记，</p>
            <h3>收藏笔记</h3>
            <p>查看笔记，点击收藏按钮，收藏过的笔记不可再重复收藏。</p>
            <h3>点赞功能</h3>
            <p>查看笔记，点击点赞按钮，赞过的笔记不可再重复点赞。</p>
            <h3>评论功能</h3>
            <p>查看笔记，评论内容不能为空，只支持二级评论。</p>
        </div>
    </div>

    <div class="help">
        这篇文章有帮助吗？
        <input type="hidden" name="articleId" value="3"/>
        <div class="isHelp yes"><i class="icon yes-img"></i><span> 是</span></div>
        <div class="isHelp no"><i class="icon no-img"></i><span> 否</span></div>
        <div class="fedBack">已有 <span id="count">{{ $count }}</span> 人觉得有帮助</div>
    </div>
</div>
</body>
</html>
