<!--404页面没有找到-->
@extends('Theme::layout.page-single')

@section('content')


<p></p>
<div class="am-panel am-panel-warning">
    <div class="am-panel-hd">
        <h3 class="am-panel-title">页面未找到</h3>
    </div>
    <div class="am-panel-bd">
        <p>对不起，你所访问的页面不见了！</p>
        <p>你可以选择回到<a href="/">首页</a>或者返回<a href="javascript:(history.back())">上一页</a></p>
    </div>
</div>
<p></p>
@stop