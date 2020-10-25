<!DOCTYPE html>
<html lang="zh-cn">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{$title}}</title>

        <!-- Bootstrap -->
        <link href="/views/install/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link href="/views/install/css/main.css" rel="stylesheet">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
        <!--头部-->
        @include('InstallTheme::layout.header')
        <!--中间-->
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    @yield('content')
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    @include('InstallTheme::layout.footer')
                </div>
            </div>
        </div>
    </body>
</html>