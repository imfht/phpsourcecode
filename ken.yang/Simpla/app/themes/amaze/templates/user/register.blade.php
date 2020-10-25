<?php
/**
 * 方法：
 * --$errors->all()：获取所有错误信息
 */
?>
@extends('Theme::layout.page-single')

@section('content')

<div class="am-g">
    <div class="am-u-lg-6 am-u-md-8 am-u-sm-centered">
        <p></p>
        <h3>注册</h3>
        <hr>
        @if($errors->all())
        <p class="am-text-danger">
        @foreach($errors->all() as $error)
        {{$error}}<br>
        @endforeach
        </p>
        @endif
        {{ Form::open(array('url' => 'register','method' => 'post','class' => 'am-form')) }}
        <label for="username">用户名:</label>
        <input type="text" autofocus="" name="username" placeholder="用户名" class="form-control" maxlength="10" required="">
        <small class="help-block">最小4个字符，最大10个字符</small>
        <br>
        <label for="email">邮箱:</label>
        <input type="email" autofocus="" name="email" placeholder="邮箱" class="form-control" maxlength="256" required="" >
        <br>
        <label for="password">密码:</label>
        <input type="password" class="form-control" name="password" placeholder="密码" maxlength="20" required="">
        <br>
        <label for="password">再次输入密码:</label>
        <input type="password" class="form-control" name="password_confirmation" placeholder="再次输入密码" maxlength="20" required="">
        <small class="help-block">仅允许字母、数字、破折号（-）以及底线（_），最小6个字符，最大20个字符</small>
        <br>
        <div class="am-cf">
            <input type="submit" name="" value="注 册" class="am-btn am-btn-primary am-btn-sm am-fl btn-loading">
            <a href="/login" class="am-btn am-btn-default am-btn-sm am-fr">登陆</a>
        </div>
        {{ Form::close() }}
        <br>
    </div>
</div>


@stop