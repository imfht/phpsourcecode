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
 * 方法：
 * --Base::csubstr($content,0,500):截取内容为最多500个字符
 */
?>
<div class="node-{{$id}}">

    <h4>
        <strong>
            <a href="{{$url}}" class="post-title">{{$title}}</a>
        </strong>
    </h4>

    <p class="post-info">
        @if($sticky)
            <span class="label label-warning pull-left">置顶</span>  
        @endif
        <span class="glyphicon glyphicon-user" aria-hidden="true"></span> 作者 {{$author}}
        | <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> 时间 {{$created}}
        | <span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> 浏览 {{$view}}
        @if($category)
        | <span class="glyphicon glyphicon-tags"></span> 分类
        @foreach($category as $row)
        <a href="{{$row['url']}}"><span class="label label-primary">{{$row['title']}}</span></a>
        @endforeach
        @endif
        <a href="{{$url}}" class="pull-right">查看更多</a>
    </p>

    <div class="post-content">
        {{Base::csubstr($content,0,500)}}
    </div>
</div>
<hr>