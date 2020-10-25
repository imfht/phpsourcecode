<!--403没有权限访问-->
@extends('Theme::layout.page-single')


@section('content')

<p></p>
<div class="am-panel am-panel-danger">
    <div class="am-panel-hd">
        <h3 class="am-panel-title">你没有访问权限</h3>
    </div>
    <div class="am-panel-bd">
        <p>亲，你没有访问权限，请联系管理员!</p>
        <p>你可以选择回到<a href="/">首页</a>或者返回<a href="javascript:(history.back())">上一页</a></p>
    </div>
</div>
<p></p>

@stop