<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>后台管理中心 - @yield('title', config('app.name', 'Laravel'))</title>
    <meta name="keywords" content="{{ config('app.name', 'Laravel') }}">
    <meta name="description" content="{{ config('app.name', 'Laravel') }}">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="shortcut icon" href="/favicon.ico">
    <link href="{{loadEdition('/admin/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{loadEdition('/admin/css/font-awesome.min.css')}}" rel="stylesheet">
    <link href="{{loadEdition('/admin/css/animate.min.css')}}" rel="stylesheet">
    <link href="{{loadEdition('/admin/css/style.min.css')}}" rel="stylesheet">
    @yield('css')
</head>
<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    @include('flash::message')
    @yield('content')
</div>
<script src="{{loadEdition('/js/jquery.min.js')}}"></script>
<script src="{{loadEdition('/admin/js/bootstrap.min.js')}}"></script>
@yield('js')
<script>
    $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
</script>
@yield('footer-js')
</body>
</html>