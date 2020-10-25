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
                            <div class="shape-text">V1.0</div>
                        </div>
                        <div class="listing-content">
                            <div class="row">
                                <!--中间content-->
                                <div class="col-md-12">
                                    <!--中间内容输出content-->
                                    {{$content_top}}
                                    @yield('content')
                                    {{$content_bottom}}
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