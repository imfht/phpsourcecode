@extends('layout.master')

@section('styles')
    <link type="text/css" rel="stylesheet" href="{{asset('css/home-style.css')}}" />
@stop

@section('topNav')
@stop

@section('content')

<div class="main-container-fluid home-content">

    <section class="download-helper">
        <div id="home-nav-top">
            <a href="{{url('/')}}" class="home-logo">
                <img  src="{{asset('image/home-logo.png')}}" alt="GoTravelling Logo"/>
            </a>

            <ul class="home-nav-list nav">
                @if( Auth::check() )
                    <li><a href="{{url('personal/info')}}" style="width: 6em">个人中心</a></li>
                @else
                    <li><a href="{{url('auth/login')}}">登录</a></li>
                    <li><a href="{{url('auth/register')}}">注册</a></li>
                @endif
            </ul>
        </div>

        <div class="description main-container">
            <img class="download-pic" src="{{asset('image/home/download-pic.png')}}" alt="GoTravelling App 截图"/>

            <div class="download-link">
                <img src="{{asset('image/background/desc-text.png')}}" alt=""/>
                <div class="download-btn-wrapper">
                    <a href="{{url('/download/android')}}" class="btn download-btn"> 立即体验 </a>
                </div>
            </div>
        </div>
    </section>

    <section class="description-text">
        <div class="main-container clearfix">

            <h2>Welcome to GoTravelling</h2>

            <div class="description-box">
                <img src="{{asset('image/home/description-convenient.png')}}" alt="简单快捷"/>

                <h3>简单快捷</h3>

                <p>方便快捷的操作，让旅行变得更加简单。轻松触发，随时记录，随心分享旅游图中的点点滴滴，尽在指尖，精彩立即呈现。</p>
            </div>

            <div class="description-box">
                <img src="{{asset('image/home/description-diy.png')}}" alt="简单快捷"/>

                <h3>私人定制</h3>

                <p>路线设计完全依据您的个人需求和预算进行规划，旅游从此随心所欲，灵动惬意。定制您的专属路线，景点交通尽在掌握之中，让旅行有个性，有智慧。</p>
            </div>

            <div class="description-box">
                <img src="{{asset('image/home/description-routes.png')}}" alt="简单快捷"/>

                <h3>精彩路线</h3>

                <p>网集最精彩的路线，丰富您的旅游选择，走别人走过的路，留下不一样的故事，收货不一般的旅游体验，全程贴心为您服务。</p>
            </div>
        </div>
    </section>

    <section class="app-screen">
        <div class="main-container">
            <div class="pic-wrapper">
                <img src="{{asset('image/home/screen-1.png')}}"/>
            </div>

            <div class="pic-wrapper">
                <img src="{{asset('image/home/screen-2.png')}}"/>
            </div>

            <div class="pic-wrapper">
                <img src="{{asset('image/home/screen-3.png')}}"/>
            </div>
        </div>
    </section>

    <section class="bottom-wrapper">

        <div class="back-btn-wrapper">
            <a href="#home-nav-top"><img src="{{asset('image/home/to-top.png')}}" alt="back"/></a>
        </div>
        <div class="main-container-fluid footer"></div>
    </section>

</div>

@stop
