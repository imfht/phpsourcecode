<?php
/**
 * 数组或者对象变量
 * --$nodes：该分类下的内容列表
 * --$category：分类信息
 * --$paginate：分页
 * 
 * --$category_content_top:分类页面顶部内容输出
 * --$category_content_bottom:分类页面底部内容输出
 * 
 * 方法
 * --Theme::node_category($nodes, $category)：可以直接输出一页内容列表，并且调用node-category.blade.php中的内容样式,需配合$paginate使用
 */
?>

@extends('Theme::layout.page')

@section('content')

<div class="{{$category['id']}}">
    {{$category_content_top}}

    <!--获取内容-->
    {{Theme::node_category($nodes, $category)}}
    <!--分页-->
    {{$paginate}}

    {{$category_content_bottom}}
</div>

@stop