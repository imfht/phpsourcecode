<?php
/**
 * 变量：
 * --$user：用户信息
 * --$nodes：用户发布的所有内容
 * --$paginate:分页
 * 
 * --$user_content_top:用户顶部内容输出
 * --$user_content_bottom:用户底部内容输出
 */
?>
@extends('Theme::layout.page')

@section('content')

{{$user_content_top}}

<div class="page-header">
    <div class='pull-left margin-right-20'>
        <img src="/{{$user['picture']}}" alt="{{$user['username']}}的头像" class="img-rounded author-head" width="55" height="55">
    </div>
    <h3>
        {{$user['username']}}
        @if($user['id'] == Auth::user()->id)
        <small>(<a href="/user/{{$user['id']}}/edit">编辑个人信息</a>)</small>
        @endif
    </h3>
    <p>最近登录：{{$user['updated_at']}}</p>

</div>
<table class="table table-striped">
    <thead>
        <tr>
            <th>标题</th>
            <th>时间</th>
        </tr>
    </thead>
    <tbody>
        @foreach($nodes as $node)
        <tr>
            <th scope="row"><a href="/node/{{$node['id']}}" target="_blank">{{$node['title']}}</a></th>
            <td>{{$node['created_at']}}</td>
        </tr>
        @endforeach
    </tbody>
</table>
{{$paginate}}

{{$user_content_bottom}}

@stop