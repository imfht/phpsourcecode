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
@extends('Theme::layout.page')

@section('content')

{{$node_content_top}}

<div class="node-{{$id}}">
    <h4>
        <strong>
            <a href="{{$url}}" class="post-title">{{$title}}</a>
        </strong>
    </h4>

    <p class="post-info">
        <span class="glyphicon glyphicon-user" aria-hidden="true"></span> 作者 {{$author}}
        | <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> 时间 {{$created}}
        | <span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> 浏览 {{$view}}
        @if($category)
        | <span class="glyphicon glyphicon-tags"></span> 分类
        @foreach($category as $row)
        <a href="{{$row['url']}}"><span class="label label-primary">{{$row['title']}}</span></a>
        @endforeach
        @endif
    </p>

    <div class="post-content">
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