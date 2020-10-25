@extends('layout.master')

@section('styles')
    <link rel="stylesheet" href="{{asset('css/personal.css')}}"/>
@stop

@section('content')
    <div class="main-container after-top-nav">

        <div class="personal-center clearfix">
            <div class="slider-bar">
                <h2 class="slider-bar-item">个人信息</h2>

                <ul>
                    <li><a class="slider-bar-item" href="{{url('personal/info')}}">基本资料</a></li>
                    <li><a class="slider-bar-item" href="{{url('personal/head-image')}}">我的头像</a></li>
                    <li><a class="slider-bar-item" href="{{url('personal/password')}}">密码设置</a></li>
                </ul>

            </div>

            @yield('editSection')
        </div>

    </div>
@endsection

@section('scripts')
    <script src="{{asset('packages/bower/requirejs/require.js')}}" data-main="{{asset('js/personal-center-ng.js')}}"></script>
@endsection