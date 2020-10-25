<!DOCTYPE html>
<html>
    <head lang="en">
        <meta charset="UTF-8">
        <title>{{$title.'-'.$siteName}}</title>
        <meta name="description" content="{{$description}}">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <meta name="format-detection" content="telephone=no">
        <meta name="renderer" content="webkit">
        <meta http-equiv="Cache-Control" content="no-siteapp"/>

        <link rel="icon" href="/favicon.ico" />

        <!-- Bootstrap -->
        {{$theme_css}}

    </head>
    <body>
        <!--头部-->
        @include('Theme::layout.header')
        <div class="am-g am-g-fixed blog-g-fixed">
            <div class="am-u-md-12">
                <!--中间内容输出content-->
                {{$content_top}}
                @yield('content')
                {{$content_bottom}}
            </div>
        </div>
        @include('Theme::layout.footer')

        <!--[if lt IE 9]>
            <script src="http://libs.baidu.com/jquery/1.11.1/jquery.min.js"></script>
            <script src="http://cdn.staticfile.org/modernizr/2.8.3/modernizr.js"></script>
            <script src="/themes/amazeui/js/polyfill/rem.min.js"></script>
            <script src="/themes/amazeui/js/polyfill/respond.min.js"></script>
            <script src="/themes/amazeui/js/amazeui.legacy.js"></script>
        <![endif]-->
        {{$theme_js}}
    </body>
</html>
{{exit}}