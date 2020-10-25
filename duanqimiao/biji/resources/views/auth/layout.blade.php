<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

    {{--移动或响应式web页面缩放设置--}}
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">

    <title>笔友 | Be yourself</title>

    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet">
    @yield('script')

</head>
<body>
<div class="container" style="margin: 60px auto;">
    <div class="container-fluid">
        <div class="row">
            @yield('return')
            <div class="col-md-5 col-md-offset-3">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">@yield('title')</h3>
                    </div>
                    <div class="panel-body">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>