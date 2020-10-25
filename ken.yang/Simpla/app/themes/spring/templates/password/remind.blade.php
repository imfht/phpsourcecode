@extends('Theme::layout.page-single')

@section('content')

{{ Form::open(array('url' => '/password/getremind','method' => 'post')) }}
<div class="omb_login">
    @if(isset($error))
    <div class="row omb_row-sm-offset-3">
        <div class="col-xs-12 col-sm-6">
            <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                {{$error}}<br/>
            </div>
        </div>
    </div>
    @endif
    @if(isset($status))
    <div class="row omb_row-sm-offset-3">
        <div class="col-xs-12 col-sm-6">
            <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                {{$status}}<br/>
            </div>
        </div>
    </div>
    @endif
    <div class="row omb_row-sm-offset-3 omb_loginOr">
        <div class="col-xs-12 col-sm-6">
            <hr class="omb_hrOr">
            <span class="omb_spanOr find-password">找回密码</span>
        </div>
    </div>

    <div class="row omb_row-sm-offset-3">
        <div class="col-xs-12 col-sm-6">	
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                <input type="email" class="form-control" name="email" placeholder="请输入邮箱" autofocus="" maxlength="256" required="">
            </div>
            <span class="help-block"></span>
            
            <input type="hidden" name="back_url" value="{{Request::query('back_url')}}">
            <button class="btn btn-lg btn-primary btn-block" type="submit">确认发送邮件</button>
        </div>
    </div>
    <div class="row omb_row-sm-offset-3">
        <div class="col-xs-12 col-sm-6">
            <p class="omb_forgotPwd">
                <a href="/login">登陆</a> | <a href="/register">注册</a>
            </p>
        </div>
    </div>	    	
</div>
{{ Form::close() }}

@stop