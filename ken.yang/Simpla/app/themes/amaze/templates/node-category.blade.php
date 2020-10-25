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
<article class="blog-main node-{{$id}}">
    <h3 class="am-article-title blog-title">
        <a href="{{$url}}">{{$title}}</a>
    </h3>
    <h4 class="am-article-meta blog-meta">作者 <a href="">{{$author}}</a> 时间 {{$created}} 浏览 {{$view}} 
        @if($category)
        分类 
        @foreach($category as $row)
        <a href="{{$row['url']}}" class="am-badge am-badge-success">{{$row['title']}}</a>
        @endforeach
        @endif
        <a href="{{$url}}" class="am-fr">查看更多</a>
    </h4>

    <div class="am-g blog-content">
        <div class="am-u-lg-12">
            {{Base::csubstr($content,0,500)}}
        </div>
    </div>
</article>
<hr>