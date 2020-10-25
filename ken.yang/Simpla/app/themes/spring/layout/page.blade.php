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
<html lang="zh-cn">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="/favicon.ico" />
        <title>{{$title.'-'.$siteName}}</title>
        <meta name="description" content="{{$description}}">


        <!-- Bootstrap -->
        {{$theme_css}}

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="listing listing-success">
                        <div class="shape">
                            <div class="shape-text">Spr</div>
                        </div>
                        <div class="listing-content">
                            <!--头部-->
                            @include('Theme::layout.header')
                            <!--头部区块-->
                            <div class="row">
                                <div class="col-md-12">
                                    {{Breadcrumb::get()}}
                                </div>
                                <div class="col-md-12">
                                    {{Blockarea::get('header')}}
                                </div>
                            </div>
                            <div class="row">
                                <!--中间content-->
                                <div class="col-md-9">
                                    <!--中间顶部区块content-header-->
                                    {{Blockarea::get('content_top')}}

                                    <!--中间内容输出content-->
                                    {{$content_top}}
                                    @yield('content')
                                    {{$content_bottom}}

                                    <!--中间底部区块content-footer-->
                                    {{Blockarea::get('content_bottom')}}
                                </div>
                                <!--侧边栏-->
                                <div class="col-md-3">
                                    <!--侧边栏区块-->
                                    {{Blockarea::get('sidebar_right')}}
                                </div>
                            </div>
                            <!--底部区块footer-block-->
                            <div class="row">
                                <div class="col-md-12">
                                    {{Blockarea::get('footer')}}
                                </div>
                            </div>
                            <!--底部footer-->
                            <div class="row">
                                <div class="col-md-12">
                                    @include('Theme::layout.footer')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{$theme_js}}
    </body>
</html>
{{exit}}