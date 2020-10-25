<?php
/**
 * 变量：
 * --$node：内容
 * --$id:内容ID
 * --$title：标题
 * --$author:作者
 * --$url:连接地址
 * --$created:创建时间
 * --$view:浏览数量
 * --$promote:是否首页显示
 * --$sticky:是否置顶
 * --$plusfine:是否为精华
 * --$category：分类
 * 
 * --$content：内容展示
 * 
 * --$comment_code:评论代码
 * 
 * --$node_content_top:内容顶部内容输出
 * --$node_content_bottom:内容底部内容输出
 * 
 */
?>
<!--Node-Default内容-->
@extends('DefaultTheme::layout.page')

@section('content')

{{$node_content_top}}

<!--页面顶部-->
<div class="page-header">
    <h3>{{$title}}</h3>
    <p>
        发布于：{{$created}} | 作者：{{$author}} | 浏览量：{{$view}}
        @if($category)
         | 分类：
        @foreach($category as $row)
        <a href="{{$row['url']}}">{{$row['title']}}</a>
        @endforeach
        @endif
    </p>
</div>
<!--页面内容-->
<div class="panel panel-default">
    <div class="panel-body">
        {{$content}}
    </div>
</div>
<!--页面评论-->
@if($comment_code)
<div class="well well-sm">
    {{$comment_code}}
</div>
@endif

{{$node_content_bottom}}

@stop