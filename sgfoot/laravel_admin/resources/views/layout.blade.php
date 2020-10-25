<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>@yield('title','')</title>
    <link rel="stylesheet" href="{{asset('frame/layui/css/layui.css')}}">
    <link rel="stylesheet" href="{{asset('css/layout.css')}}">
    <link rel="icon" href="{{asset('frame/static/image/code.png')}}">
    <link rel="stylesheet" href="https://cdn.bootcss.com/font-awesome/4.7.0/css/font-awesome.css">
    @yield('style')
</head>
<body class="body">
@section('body')
@show
<script type="text/javascript" src="{{asset('frame/static/js/jquery.2.1.1.min.js')}}"></script>
<script type="text/javascript" src="{{asset('frame/layui/layui.js')}}"></script>
<script type="text/javascript" src="{{asset('js/layout.js')}}"></script>
<script type="text/javascript">
$.ajaxSetup({
    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
});
</script>
@yield('script')
</body>
</html>