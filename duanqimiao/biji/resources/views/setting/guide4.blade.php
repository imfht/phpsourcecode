<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    {{--移动或响应式web页面缩放设置--}}
    <meta name="viewport" content="width=device-width,initial-scale=0.6,maximum-scale=1.0,user-scalable=no">
    <title>如何修改密码</title>
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
        <h1>如何修改密码</h1>
    </div>
    <div class="list">
        本文包含以下内容：
        <ul>
            <li>
                如何修改密码
            </li>
            <li>
                密码格式的要求
            </li>
        </ul>
        <div  class="content">
            <h2>如果忘记了密码，请按照以下步骤操作：</h2>
            <ul>
                <li>尝试通过登陆帐户</li>
                <li>点击“忘记密码？”</li>
                <li>输入你的邮箱地址</li>
                <li>在重设密码链接中检查邮箱地址。</li>
            </ul>
        </div>
        <div  class="content">
            <h2>如果你知道当前密码但想要更改密码：</h2>
            <p>如果想要修改密码，请按照以下步骤操作：</p>
            <ul>
                <li>打开我的设置</li>
                <li>更改密码</li>
                <li>填写原密码</li>
                <li>填写新密码</li>
            </ul>
        </div>

        <div  class="content">
            <h2>密码的格式要求</h2>
            <p>至少6位任意字符。</p>
        </div>
    </div>

    <div class="help">
        这篇文章有帮助吗？
        <input type="hidden" name="articleId" value="4"/>
        <div class="isHelp yes"><i class="icon yes-img"></i><span> 是</span></div>
        <div class="isHelp no"><i class="icon no-img"></i><span> 否</span></div>
        <div class="fedBack">已有 <span id="count">{{ $count }}</span> 人觉得有帮助</div>
    </div>
</div>
</body>
</html>
