<!doctype html>
<html class="no-js">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>TrackBlog 提示信息</title>
    <meta name="description" content="这是一个 index 页面">
    <meta name="keywords" content="index">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp"/>
    <link rel="icon" type="image/png" href="/public/i/favicon.png">
    <link rel="apple-touch-icon-precomposed" href="/public/i/app-icon72x72@2x.png">
    <meta name="apple-mobile-web-app-title" content="Amaze UI"/>
    <link rel="stylesheet" href="/public/css/amazeui.min.css"/>
    <link rel="stylesheet" href="/public/css/admin.css">
    <meta http-equiv="refresh" content="3;url=<?php echo $url; ?>">
</head>
<body>
    <!--[if lte IE 9]>
    <p class="browsehappy">你正在使用<strong>过时</strong>的浏览器，请升级浏览器</a>
        以获得更好的体验！</p>
    <![endif]-->

    <div class="am-g" style="margin-top: 15%;">
        <div class="am-u-sm-11 am-u-md-8 am-u-lg-4 am-u-sm-centered am-u-md-centered am-u-lg-centered">
            <div class="am-panel am-panel-<?php echo $type; ?>">
                <div class="am-panel-hd am-text-center">信息提示</div>
                <div class="am-panel-bd">
                    <p class="am-text-center"><?php echo $content; ?></p>
                </div>
                <div class="am-panel-footer am-text-center">
                    <a href="<?php echo $url; ?>">如果未自动跳转，请点击这里</a>
                </div>
            </div>
        </div>
    </div>


    <!--[if lt IE 9]>
    <script src="http://libs.baidu.com/jquery/1.11.1/jquery.min.js"></script>
    <script src="http://cdn.staticfile.org/modernizr/2.8.3/modernizr.js"></script>
    <script src="/public/js/amazeui.ie8polyfill.min.js"></script>
    <![endif]-->


    <!--[if (gte IE 9)|!(IE)]><!-->
    <script src="/public/js/jquery.min.js"></script>
    <!--<![endif]-->
    <script src="/public/js/amazeui.min.js"></script>
    <script src="/public/js/app.js"></script>

    <script>
        var t = 0;
        $(function(){
            setInterval(loadgo,50);
        });
        function loadgo()
        {
            $.AMUI.progress.set(t);
            t+=50/3000;
        }
    </script>
</body>
</html>
