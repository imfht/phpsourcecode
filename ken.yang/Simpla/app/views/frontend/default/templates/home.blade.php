<?php
/**
 * 变量：
 * --$nodes：内容列表
 * --$paginate：分页
 * 
 * --$home_content_top:分类页面顶部内容输出
 * --$home_content_bottom:分类页面底部内容输出
 * 方法：
 * --Theme::node_home($nodes)：获取node_home.blade.php中的内容样式
 */
?>

@extends('DefaultTheme::layout.page')

@section('content')

{{$home_content_top}}

{{Theme::node_home($nodes)}}
{{$paginate}}

{{$home_content_bottom}}

@stop