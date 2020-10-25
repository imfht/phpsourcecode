<!doctype html>
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]> <html class="no-js" lang="zh_CN"> <!--<![endif]-->
<head>
    <meta charset="utf-8">

    <title>404错误提示页</title>
    <meta name="description" content="">
    <meta name="author" content="">

    <meta name="viewport" content="width=device-width,initial-scale=1">

    <!-- CSS: implied media=all -->
    <link rel="stylesheet" href="{{ asset('/css/404style.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/404blue.css') }}css/404blue.css">

    <script src="{{ URL::asset('/') }}js/jquery-404.js"></script>
    <script src="{{ URL::asset('/') }}js/404script.js"></script>

    <style type="text/css">
        .STYLE1 {color: #FF0000}
    </style>

</head>
<body>

<div id="error-container">
    <div id="error">
        <div id="pacman"></div>
    </div>
    <div id="container">
        <div id="title">
            <h1>对不起, 你访问的页面不存在!</h1>
        </div>
        <div id="content">
            <div class="left">
                <p class="no-top">&nbsp;&nbsp;&nbsp;可能是如下原因引起了这个错误:</p>
                <ul>
                    <li>&nbsp;&nbsp;&nbsp;URL输入错误</li>
                    <li>&nbsp;&nbsp;&nbsp;链接已失效</li>
                    <li>&nbsp;&nbsp;&nbsp;其他原因...</li>
                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="right">
                <p class="no-top">推荐您通过以下链接继续访问：</p>
                <ul class="links">
                    <li><a href="{{ url('/biji') }}">» 笔友主页</a></li>
                    <li><a href="{{ url('/circle') }}">» 笔友圈</a></li>
                </ul>
                <ul class="links">
                    <li><a href="{{ url('/biji/create') }}">» 新建笔记</a></li>
                    <li><a href="{{ url('/book/create') }}">» 新建笔记本</a></li>
                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="clearfix"></div>
        </div>
        <div id="footer">
            CopyRight © 2016 dqm All Rights Reserved.
        </div>
    </div>
</div>
</body>

</html>