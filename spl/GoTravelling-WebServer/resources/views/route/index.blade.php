@extends('layout.master')

@section('styles')
    @if( Config::get('app.debug') )
        <link type="text/css" rel="stylesheet" href="{{asset('css/route-app-style.css')}}" />
        <link type="text/css" rel="stylesheet" href="{{asset('css/route-app-directive.css')}}"/>
    @else
        @section('default-styles')
        @stop
        <link type="text/css" rel="stylesheet" href="{{asset('css/app.min.css')}}"/>
    @endif

    <meta name="csrf_token" content="{{ csrf_token() }}" />
@stop

@section('content')

<div id="loading-wrapper">
    <img src="{{asset('image/routeApp/loading.gif')}}" alt="载入中"/>
</div>

<div class="after-top-nav" id="RouteNgApp">
    <ui-view></ui-view>
</div>
@stop

@section('scripts')
    <script src="http://api.map.baidu.com/api?v=2.0&ak={{env('BAIDU_MAP_KEY', '')}}"></script>
    @if( Config::get('app.debug') )
        <script src="{{asset('packages/bower/requirejs/require.js')}}" data-main="{{asset('js/route-app-ng.js')}}"></script>
    @else
        <script src="{{asset('packages/bower/requirejs/require.js')}}" data-main="{{asset('route-app-ng.min.js')}}"></script>
    @endif
@endsection