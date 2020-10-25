<?php
/**
 * 方法：
 * --$errors->all()：获取所有错误信息
 */
?>
@extends('Theme::layout.page-single')

@section('content')

{{ Form::open(array('url' => 'register','method' => 'post','class' => 'form-signin')) }}
<h2 class="form-signin-heading">注册</h2>

@if($errors->all())
<div class="alert alert-danger alert-dismissable">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    @foreach($errors->all() as $error)
    {{$error}}<br/>
    @endforeach
</div>
@endif

<div class="form-group">
    <label for="username">用户名：</label>
    <input type="username" autofocus="" name="username" placeholder="请输入用户名" class="form-control" maxlength="10" required="">
    <small class="help-block">最小4个字符，最大10个字符</small>
</div>

<div class="form-group">
    <label for="email">邮箱：</label>
    <input type="email" autofocus="" name="email" placeholder="请输入邮箱" class="form-control" maxlength="256" required="" >
</div>

<div class="form-group">
    <label for="password">密码：</label>
    <input type="password" class="form-control" name="password" placeholder="密码" maxlength="20" required="">
</div>
<div class="form-group">
    <input type="password" class="form-control" name="password_confirmation" placeholder="请再次输入密码" maxlength="20" required="">
    <small class="help-block">仅允许字母、数字、破折号（-）以及底线（_），最小6个字符，最大20个字符</small>
</div>
<input type="hidden" name="back_url" value="{{Request::query('back_url')}}">
<button type="submit" class="btn btn-lg btn-primary btn-block">注册</button>
<div class="margin-top-20"><a href="/login">登陆</a>   |   <a href="/password/remind">找回密码</a></div>
{{ Form::close() }}

@stop