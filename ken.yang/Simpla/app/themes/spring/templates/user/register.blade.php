<?php
/**
 * 方法：
 * --$errors->all()：获取所有错误信息
 */
?>
@extends('Theme::layout.page-single')

@section('content')

{{ Form::open(array('url' => 'register','method' => 'post')) }}
<div class="omb_login">
    @if($errors->all())
    <div class="row omb_row-sm-offset-3">
        <div class="col-xs-12 col-sm-6">
            <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                @foreach($errors->all() as $error)
                {{$error}}<br/>
                @endforeach
            </div>
        </div>
    </div>
    @endif
    <div class="row omb_row-sm-offset-3 omb_loginOr">
        <div class="col-xs-12 col-sm-6">
            <hr class="omb_hrOr">
            <span class="omb_spanOr">注册</span>
        </div>
    </div>

    <div class="row omb_row-sm-offset-3">
        <div class="col-xs-12 col-sm-6">	
            <div class="input-group">
                <span class="input-group-addon glyphicon glyphicon-user"></span>
                <input type="username" autofocus="" name="username" placeholder="用户名" class="form-control" maxlength="10" required="">
            </div>
            <small class="help-block">最小4个字符，最大10个字符</small>

            <div class="input-group">
                <span class="input-group-addon glyphicon glyphicon-envelope"></span>
                <input type="email" autofocus="" name="email" placeholder="邮箱" class="form-control" maxlength="256" required="" >
            </div>
            <small class="help-block"></small>
            <div class="input-group">
                <span class="input-group-addon glyphicon glyphicon-lock"></span>
                <input type="password" class="form-control" name="password" placeholder="密码" maxlength="20" required="">
            </div>
            <small class="help-block"></small>
            <div class="input-group">
                <span class="input-group-addon glyphicon glyphicon-lock"></span>
                <input type="password" class="form-control" name="password_confirmation" placeholder="再次输入密码" maxlength="20" required="">
            </div>
            <small class="help-block">仅允许字母、数字、破折号（-）以及底线（_），最小6个字符，最大20个字符</small>
            <button class="btn btn-lg btn-primary btn-block" type="submit">注册</button>
        </div>
    </div>
    <div class="row omb_row-sm-offset-3">
        <div class="col-xs-12 col-sm-6">
            <p class="omb_forgotPwd">
                <a href="/login">登陆</a>
            </p>
        </div>
    </div>	    	
</div>
{{ Form::close() }}

@stop