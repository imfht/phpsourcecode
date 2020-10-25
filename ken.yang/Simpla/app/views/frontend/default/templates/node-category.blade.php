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
<div  class="node-{{$id}}">
    <div class="page-header">
        <h3><a href="{{$url}}">{{$title}}</a></h3>
        <p>
            @if($sticky)
                <span class="label label-warning pull-left">置顶</span>  
            @endif
            发布于：{{$created}} | 作者：{{$author}} | 浏览量：{{$view}}
            @if($category)
             | 分类：
            @foreach($category as $row)
            <a href="{{$row['url']}}">{{$row['title']}}</a>
            @endforeach
            @endif
        </p>
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
            {{Base::csubstr($content,0,500)}}
        </div>
    </div>
    <p><a href="{{$url}}">查看更多></a></p>
</div>