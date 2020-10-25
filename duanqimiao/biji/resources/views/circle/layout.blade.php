<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>笔友 | Be yourself</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/css/circle.css') }}">
    {{--移动或响应式web页面缩放设置--}}
    <meta name="viewport" content="width=device-width,initial-scale=0.4,maximum-scale=1.0,user-scalable=no">
    <script language="JavaScript" src="{{ URL::asset('/') }}js/jquery.js"></script>
    <script type="text/javascript" src="{{ URL::asset('/') }}js/bootstrap.js"></script>
    <link href="{{ asset('/css/animate.css') }}" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="{{ URL::asset('/') }}js/user.js"></script>
    <script type="text/javascript" src="{{ URL::asset('/') }}js/up.js"></script>
    <script type="text/javascript" src="{{ URL::asset('/') }}js/loading.js"></script>
    {{--引入artDialog插件--}}
    <link rel="stylesheet" href="{{ asset('/css/ui-dialog.css') }}">

    <script src="{{ URL::asset('/') }}js/dialog-min.js"></script>
    {{--END--}}
    @yield('script')
</head>
<body>
    <div id="loading" style="position: fixed;top: -50%;left: -50%;width: 200%;height: 200%;background: #fff;z-index: 100;overflow: hidden;">
        <img src="{{ asset('/images/loading.gif') }}" style="position: absolute;top: 0;left: 0;right: 0;bottom: 0;margin: auto;"/>
    </div>
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-menu">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{ url('circle/') }}">笔友圈</a>
            </div>
                <div class="collapse navbar-collapse" id="navbar-menu">
                    @include('circle.navbar')
                </div>
        </div>
    </nav>
    <div class="container">
        @yield('content')
    </div>
    <div id="up"><i class="icon return-top-img"></i></div>
</body>
</html>