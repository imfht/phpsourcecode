<?php
/**
 * 变量：
 * --$message：错误信息
 */
?>
@extends('DefaultTheme::layout.page-single')

@section('content')

{{ Form::open(array('url' => 'login','method' => 'post','class' => 'form-signin')) }}
<h2 class="form-signin-heading">登录</h2>

@if($message)
<div class="alert alert-danger alert-dismissable">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    {{ $message }}
</div>
@endif

<div class="form-group">
    <label for="email">用户名/邮箱：</label>
    <input type="text" class="form-control" name="username_or_email" placeholder="请输入用户名/邮箱" autofocus="" maxlength="256" required="">
</div>

<div class="form-group">
    <label for="password">密码：</label>
    <input type="password" class="form-control" name="password" placeholder="请输入密码" maxlength="20" required="">
</div>

<div class="checkbox">
    <label>
        <input type="checkbox" name="remember" value="1"> 记住我
    </label>
</div>
<input type="hidden" name="back_url" value="{{Request::query('back_url')}}">
<button type="submit" class="btn btn-lg btn-primary btn-block">登录</button>
<div class="margin-top-20"><a href="/register">注册</a>   |   <a href="/password/remind">找回密码</a></div>
{{ Form::close() }}

@stop