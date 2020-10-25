@extends('layout.master')

@section('styles')
    <link type="text/css" rel="stylesheet" href="{{asset('css/login-style.css')}}"/>
@stop

@section('topNav')
@stop

@section('content')
    <div class="main-container-fluid login-back-to-home-wrapper">
        <div class="back-to-home-wrapper">
            <a class="btn back-to-home" href="{{asset('/')}}">返回首页</a>
        </div>

    </div>

    <div class="main-container login-wrapper clearfix">
        <img src="{{asset('image/background/login-bg.jpg')}}" class="login-form-bg" />
        <img class="login-text" src="{{asset('image/background/login-text.png')}}" alt="用旅行的方式，分享你我的故事"/>

        <div class="main-container form-wrapper">
            <form action="{{action('Auth\AuthController@postLogin')}}" method="post">
                <div>
                    <label for="identify">账号</label>
                    <input type="text" name="identify" id="identify" placeholder="请输入用户名或手机号" required/>
                </div>

                @if( count($errors) > 0 )
                    <div class="login-error">
                        <p>登录失败，账号或密码错误</p>
                    </div>
                @endif

                <div>
                    <label for="password">密码</label>
                    <input type="password" name="password" id="password" placeholder="请输入密码" required/>
                </div>

                <div id="remember">
                    <label><input type="checkbox" name="remember"/><span class="remember-text">记住我</span></label>
                </div>

                <div style="text-align: center">
                    <button type="submit">登录</button>
                </div>
            </form>
        </div>

    </div>


@endsection