<?php
/**
 * 变量
 * --$breadcrumb：面包屑数组
 */
?>
<!-- 面包屑 -->
@if(!$is_front)
<ol class="am-breadcrumb am-breadcrumb-slash">
    <li><a href="/" class="am-icon-home">首页</a></li>
    @foreach($breadcrumb as $row)
    <li><a href="{{$row['url']}}">{{$row['title']}}</a></li>
    @endforeach
</ol>
@endif