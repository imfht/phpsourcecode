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

<div class="row search">
    <div class="col-lg-12">
        {{ Form::open(array('method' => 'get','url'=>'search')) }}
        @if($errors->all())
        <div class="alert alert-danger alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            @foreach($errors->all() as $error)
            {{$error}}<br/>
            @endforeach
        </div>
        @endif

        <div class="form-group">
            <label>关键字<span class="text-danger" title="此项必填">*</span></label>
            <input type="text" name="key" value="{{$key}}" class="form-control" placeholder="搜索" maxlength="10" required="">
        </div>
        <button class="btn btn-default" type="submit">搜索</button>
        {{Form::close()}}

        <!--查询结果-->
        @if(isset($result))
        <div class="page-header">
            <h3>搜索结果:</h3>
        </div>
        @foreach($result as $node)
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><a href="/node/{{$node['id']}}" target="_blank">{{$node['title']}}</a></h3>
            </div>
            <div class="panel-body">
                {{$node['body']}}
            </div>
        </div>
        @endforeach
        {{$paginate}}
        @endif
    </div>
</div>

@stop