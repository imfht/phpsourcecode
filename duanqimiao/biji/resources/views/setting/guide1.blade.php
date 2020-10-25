<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    {{--移动或响应式web页面缩放设置--}}
    <meta name="viewport" content="width=device-width,initial-scale=0.6,maximum-scale=1.0,user-scalable=no">
    <title>如何删除笔记和管理“废纸篓”笔记本</title>
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
            <h1>如何删除笔记和管理“废纸篓”笔记本</h1>
        </div>
        <div class="list">
            本文包含以下内容：
            <ul>
                <li>
                    删除笔记
                </li>
                <li>
                    删除笔记本
                </li>
                <li>
                    管理“废纸篓”笔记本
                </li>
            </ul>
            <div  class="content">
                <h2>删除笔记</h2>
                <p>删除一条笔记时，这条笔记会被移动到“废纸篓”笔记本。</p>
                <ul>
                    <li>打开要删除的笔记</li>
                    <li>点击删除笔记按钮</li>
                </ul>
            </div>
            <div  class="content">
                <h2>删除笔记本</h2>
                <p>删除一个笔记本时，其中的所有笔记都会被移动到“废纸篓”笔记本，该笔记本会被从你的帐户中移除。</p>
                <ul>
                    <li>点击想要删除的笔记本名称边的向下箭头</li>
                    <li>在标题旁边选择“删除”</li>
                </ul>
            </div>

            <div  class="content">
                <h2>管理“废纸篓”笔记本</h2>
                <p>一旦将笔记从“废纸篓”笔记本中删除，该笔记将无法恢复，维护人员也无能为力。因此，将笔记从“废纸篓”中删除前，请务必确定你永远不会再用到该笔记。</p>
                <h3>还原笔记</h3>
                <p>如想从“废纸篓”笔记本中还原笔记，请选择想要还原的笔记.</p>
                <h3>清除废纸篓中的笔记</h3>
                <p>任何你从“废纸篓”笔记本中删除的内容将从你的帐户中删除，并且是不可恢复的。</p>
                <ul>
                    <li>点击想要删除的笔记本名称边的向下箭头</li>
                    <li>在标题旁边选择“删除”</li>
                </ul>
            </div>
        </div>

        <div class="help">
            这篇文章有帮助吗？
            <input type="hidden" name="articleId" value="1"/>
            <div class="isHelp yes"><i class="icon yes-img"></i><span> 是</span></div>
            <div class="isHelp no"><i class="icon no-img"></i><span> 否</span></div>
            <div class="fedBack">已有 <span id="count">{{ $count }}</span> 人觉得有帮助</div>
        </div>
    </div>
</body>
</html>
