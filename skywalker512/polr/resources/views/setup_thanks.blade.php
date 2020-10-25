@extends('layouts.minimal')

@section('title')
Setup Completed
@endsection

@section('css')
<link rel='stylesheet' href='/css/default-bootstrap.min.css'>
<link rel='stylesheet' href='/css/setup.css'>
@endsection

@section('content')
<div class="navbar navbar-default navbar-fixed-top">
    <a class="navbar-brand" href="/">Polr</a>
</div>

<div class='row'>
    <div class='col-md-3'></div>

    <div class='col-md-6 setup-body well'>
        <div class='setup-center'>
            <img class='setup-logo' src='/img/logo.png'>
        </div>
        <h2>安装完成</h2>
        <p>你可以 <a href='{{route('login')}}'>登录</a> 或者
            访问 <a href='{{route('index')}}'>首页</a>.
        </p>
        <p>你可以访问 <a href='https://forum.flarumchina.org/t/polr'><code>中文论坛</code></a>寻求帮助。</p>
        <p>感谢你使用 Polr!</p>
    </div>

    <div class='col-md-3'></div>
</div>


@endsection
