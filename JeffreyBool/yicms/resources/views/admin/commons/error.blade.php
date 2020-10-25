@extends('admin.commons.prompt_layout')

@section('title', '错误提示')

@section('content')
<div class="sa-icon sa-error">
        <span class="sa-x-mark animateXMark">
            <span class="sa-line sa-left"></span>
            <span class="sa-line sa-right"></span>
        </span>
</div>
    <h2>{{ $message }}</h2>
    <p>页面将会自动跳转，等待时间：<b id="wait">{{$wait}}</b><a id="href" style="display:none" href="{{$url}}">点击跳转</a></p>
@stop