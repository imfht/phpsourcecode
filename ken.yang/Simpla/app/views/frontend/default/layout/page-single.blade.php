<?php
/**
 * 变量：
 * --$title：标题
 * --$siteName：站点名字
 * --$description:描述
 * --$theme_css：css样式文件
 * --$theme_js：js样式文件
 * 
 * --$content_top顶部内容
 * --$content_bottom底部内容
 * 
 * --$base_path基本路径
 * --$is_front是否为首页
 * --$logged_in是否登陆
 * --$is_admin是否为管理员
 * 
 * --$user已登录用户信息，未登录为空
 */
?>
<!DOCTYPE html>
<html lang="zh-cn">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{$title.'-'.$siteName}}</title>
        <meta name="description" content="{{$description}}">

        <!-- Bootstrap -->
        <link href="/themes/default/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link href="/themes/default/css/main.css" rel="stylesheet">
        {{$theme_css}}

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
        <!--头部-->
        @include('DefaultTheme::layout.header')
        <!--中间-->
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    {{$content_top}}
                    @yield('content')
                    {{$content_bottom}}
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    @include('DefaultTheme::layout.footer')
                </div>
            </div>
        </div>
        <script src="/themes/default/bootstrap/js/jquery.min.js"></script>
        <script src="/themes/default/bootstrap/js/bootstrap.min.js"></script>
        {{$theme_js}}
    </body>
</html>
{{exit}}