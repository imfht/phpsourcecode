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

<article class="blog-main node-{{$id}}">
    <h3 class="am-article-title blog-title">
        {{$title}}
    </h3>
    <h4 class="am-article-meta blog-meta">作者 <a href="">{{$author}}</a> 时间 {{$created}} 浏览 {{$view}} 
        @if($category)
        分类 
        @foreach($category as $row)
        <a href="{{$row['url']}}" class="am-badge am-badge-success">{{$row['title']}}</a>
        @endforeach
        @endif
    </h4>

    <div class="am-g blog-content">
        <div class="am-u-lg-12">
            {{$content}}
        </div>
    </div>
</article>
<!--页面评论-->
@if($comment_code)
<div class="am-container">
    {{$comment_code}}
</div>
@endif

{{$node_content_bottom}}

@stop