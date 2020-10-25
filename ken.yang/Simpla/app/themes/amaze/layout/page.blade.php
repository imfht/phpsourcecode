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
 * 
 * 方法：
 * --Breadcrumb::get():获取面包屑，对应文件为breadcrumb.blade.php
 * --Blockarea::get('header')：头部区域展示
 * --Blockarea::get('content_top')：内容顶部区域展示
 * --Blockarea::get('content_bottom')：内容底部区域展示
 * --Blockarea::get('sidebar_right')：右边栏区域展示
 * --Blockarea::get('footer')：底部区域展示
 * --Blockarea::get('sidebar_left'):左边来区域展示
 * 
 */
?>
<!DOCTYPE html>
<html>
    <head lang="en">
        <meta charset="UTF-8">
        <title>{{$title.'-'.$siteName}}</title>
        <meta name="description" content="{{$description}}">
        <meta name="keywords" content="{{$keywords}}">
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
        <div class="am-g am-g-fixed blog-g-fixed page-content">

            <div class="am-u-md-12">
                {{Breadcrumb::get()}}
                {{Blockarea::get('header')}}
            </div>
            <div class="am-u-md-8">
                <!--中间顶部区块content-header-->
                {{Blockarea::get('content_top')}}

                <!--中间内容输出content-->
                {{$content_top}}
                @yield('content')
                {{$content_bottom}}

                <!--中间底部区块content-footer-->
                {{Blockarea::get('content_bottom')}}
            </div>
            <div class="am-u-md-4 blog-sidebar">
                <div class="am-panel-group">
                    <form class="am-form-inline" role="form" action="/search" method="get">
                        <div class="am-form-group">
                            <input type="text" name="key" value="" class="am-form-field" placeholder="搜索" maxlength="10" required="">
                        </div>
                        <button type="submit" class="am-btn am-btn-primary">搜索</button>
                    </form>
                    <br>
                    <!--侧边栏区块-->
                    {{Blockarea::get('sidebar_right')}}
                </div>
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