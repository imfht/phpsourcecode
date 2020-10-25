<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name', 'Laravel'))</title>
    <meta name="keywords" content="@yield('title', config('app.name', 'Laravel'))">
    <meta name="description" content="@yield('title', config('app.name', 'Laravel'))">
    <link rel="shortcut icon" href="/favicon.ico">
    <link href="{{loadEdition('/admin/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{loadEdition('/admin/css/font-awesome.min.css')}}" rel="stylesheet">
    <link href="{{loadEdition('/admin/css/animate.min.css')}}" rel="stylesheet">
    <link href="{{loadEdition('/admin/css/style.min.css')}}" rel="stylesheet">
    <link href="{{loadEdition('/admin/css/plugins/sweetalert/sweetalert.css')}}" rel="stylesheet">
</head>
<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeIn">
    <div class="sweet-alert" style="display:block;margin-top:-125px;">
        @yield('content')
    </div>
</div>
<script type="text/javascript">
    (function(){
        var wait = document.getElementById('wait'),href = document.getElementById('href').href;
        var interval = setInterval(function(){
            var time = --wait.innerHTML;
            if(time <= 0) {
                top.location.href = href;
                clearInterval(interval);
            };
        }, 1000);
    })();
</script>
</body>
</html>
