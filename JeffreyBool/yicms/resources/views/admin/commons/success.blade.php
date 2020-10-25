@extends('admin.commons.prompt_layout')

@section('title', '成功提示')

@section('content')
    <div class="sa-icon sa-success animate">
        <span class="sa-line sa-tip animateSuccessTip"></span>
        <span class="sa-line sa-long animateSuccessLong"></span>
        <div class="sa-placeholder"></div>
        <div class="sa-fix"></div>
    </div>
    <h2>{{ $message }}</h2>
    <p>页面将会自动跳转，等待时间：<b id="wait">{{$wait}}</b><a id="href" style="display:none" href="{{$url}}">点击跳转</a></p>
@stop