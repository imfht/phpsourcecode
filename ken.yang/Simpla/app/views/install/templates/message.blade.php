
@extends('InstallTheme::layout.page')

@section('content')
<div class="listing listing-success">
    <div class="shape">
        <div class="shape-text">{{$version}}</div>
    </div>
    <div class="listing-content">
        <h3 class="lead">Simpla安装向导 <small>消息提示</small></h3>
        <hr>
        <h1 class="text-align-center">程序已经安装，若要重新安装，请删除app/lock.txt文件!</h1>
        <br/>
        <div class="text-align-center">
            <a href="/">去首页</a>
        </div>
    </div>
</div>

@stop