<?php
/**
 * 变量：
 * --$key：搜索关键字
 * --$result：搜索结果
 * 方法：
 * --$errors->all()：获取所有错误信息
 * --Base::csubstr($node['body'],0,500)：字符串截取，获取内容的最多500个字符
 */
?>
<!--搜索页面-->
@extends('Theme::layout.page')

@section('content')

{{ Form::open(array('method' => 'get','url'=>'search','class'=>'am-form-inline')) }}
@if($errors->all())
@foreach($errors->all() as $error)
<p class="am-text-danger">
    {{$error}}<br/>
</p>
@endforeach
@endif
<div class="am-form-group">
    <input type="text" name="key" value="{{$key}}" class="am-form-field" placeholder="搜索" maxlength="10" required="">
</div>
<button type="submit" class="am-btn am-btn-primary">搜索</button>
{{Form::close()}}

<!--查询结果-->
@if(isset($result))
<h3>关键字：{{$key}}     搜索结果:</h3>

@foreach($result as $node)
<div class="am-panel am-panel-default">
    <div class="am-panel-hd"><a href="/node/{{$node['id']}}" target="_blank">{{$node['title']}}</a></div>
    <div class="am-panel-bd">
        {{Base::csubstr($node['body'],0,500)}}
    </div>
</div>
@endforeach
{{$paginate}}
@endif

@stop