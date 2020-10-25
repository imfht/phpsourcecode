<!doctype html>

<html lang="zh-cmn-hans">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="shortcut icon" href="{{ URL::to('/') .'/image/min-logo.ico' }}" type="image/x-icon" />
    <title>
        @if( isset($pageTitle) )
            {{$pageTitle}}
        @else
            去旅游网 —— 私人订制的旅游信息网
        @endif
    </title>
    @section('default-styles')
        <link type="text/css" rel="stylesheet" href="{{asset('css/reset.css')}}"/>
        <link type="text/css" rel="stylesheet" href="{{asset('css/common-style.css')}}" />
        <link type="text/css" rel="stylesheet" href="{{asset('css/top-nav.css')}}"/>
        <link href="{{ asset('packages/bower/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" />
    @show

    @yield('styles')
</head>
<body>

    @section('topNav')
        @include('layout.topNav')
    @show

    @yield('content')

    <script src="{{asset('js/require-global-config.js')}}"></script>
    @yield('scripts')
</body>
</html>