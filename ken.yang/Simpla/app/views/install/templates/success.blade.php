
@extends('InstallTheme::layout.page')

@section('content')
<div class="listing listing-success">
    <div class="shape">
        <div class="shape-text">{{$version}}</div>
    </div>
    <div class="listing-content">
        <h3 class="lead">Simpla安装向导<small>安装完成</small></h3>
        <hr>
        <h1 class="text-align-center">恭喜你,完成安装!</h1>
        <br/>
        <div class="text-align-center">
            <a href="/" class="margin-right-20">去首页</a>
            <a href="/admin/login" class="margin-left-20">去后台</a>
        </div>
    </div>
</div>
@stop