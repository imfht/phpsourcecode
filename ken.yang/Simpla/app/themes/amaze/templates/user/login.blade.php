<?php
/**
 * 变量：
 * --$message：错误信息
 */
?>
@extends('Theme::layout.page-single')

@section('content')

<div class="am-g">
    <div class="am-u-lg-6 am-u-md-8 am-u-sm-centered">
        <p></p>
        <h3>登录</h3>
        <hr>
        @if($message)
        <p class="am-text-danger">
            {{ $message }}
        </p>
        @endif
        {{ Form::open(array('url' => 'login','method' => 'post','class' => 'am-form')) }}
        <label for="email">用户名 / 邮箱:</label>
        <input type="text" class="form-control" name="username_or_email" placeholder="用户名 / 邮箱" autofocus="" maxlength="256" required="">
        <br>
        <label for="password">密码:</label>
        <input type="password" class="form-control" name="password" placeholder="密码" maxlength="20" required="">
        <br>
        <label for="remember-me">
            <input id="remember-me" type="checkbox" name="remember" value="1">记住我
        </label>
        <br />
        <div class="am-cf">
            <input type="submit" name="" value="登 录" class="am-btn am-btn-primary am-btn-sm am-fl btn-loading">
            <a href="/register" class="am-btn am-btn-default am-btn-sm am-fr">注册</a>
            <a href="/password/remind" class="am-btn am-btn-default am-btn-sm am-fr">忘记密码 ^_^?</a>
        </div>
        {{ Form::close() }}
        <br>
    </div>
</div>

@stop