<?php
/**
 * 变量：
 * --$message：错误信息
 */
?>
@extends('Theme::layout.page-single')

@section('content')

{{ Form::open(array('url' => 'login','method' => 'post','class' => 'form-signin')) }}
<div class="omb_login">
    @if($message)
    <div class="row omb_row-sm-offset-3">
        <div class="col-xs-12 col-sm-6">
            <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                {{ $message }}
            </div>
        </div>
    </div>
    @endif
    <div class="row omb_row-sm-offset-3 omb_loginOr">
        <div class="col-xs-12 col-sm-6">
            <hr class="omb_hrOr">
            <span class="omb_spanOr">登陆</span>
        </div>
    </div>

    <div class="row omb_row-sm-offset-3">
        <div class="col-xs-12 col-sm-6">
            <div class="input-group">
                <span class="input-group-addon glyphicon glyphicon-user"></span>
                <input type="text" class="form-control" name="username_or_email" placeholder="用户名 / 邮箱" autofocus="" maxlength="256" required="">
            </div>
            <span class="help-block"></span>

            <div class="input-group">
                <span class="input-group-addon glyphicon glyphicon-lock"></span>
                <input type="password" class="form-control" name="password" placeholder="密码" maxlength="20" required="">
            </div>

            <label class="checkbox margin-left-20">
                <input type="checkbox" name="remember" value="1">记住我
            </label>
            <input type="hidden" name="back_url" value="{{Request::query('back_url')?Request::query('back_url'):'/'}}">
            <button class="btn btn-lg btn-primary btn-block" type="submit">登录</button>
        </div>
    </div>
    <div class="row omb_row-sm-offset-3">
        <div class="col-xs-12 col-sm-6">
            <p class="omb_forgotPwd">
                <a href="/register">注册</a> | <a href="/password/remind">忘记密码?</a>
            </p>
        </div>
    </div>	    	
</div>
{{ Form::close() }}
@stop