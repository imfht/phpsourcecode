@extends('Theme::layout.page-single')

@section('content')

{{ Form::open(array('url' => '/password/getreset','method' => 'post','class' => 'form-signin')) }}

<div class="omb_login">
    @if(Session::get('error'))
    <div class="row omb_row-sm-offset-3">
        <div class="col-xs-12 col-sm-6">
            <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                {{Session::get('error')}}<br/>
            </div>
        </div>
    </div>
    @endif
    <div class="row omb_row-sm-offset-3 omb_loginOr">
        <div class="col-xs-12 col-sm-6">
            <hr class="omb_hrOr">
            <span class="omb_spanOr reset-password">重置密码</span>
        </div>
    </div>

    <div class="row omb_row-sm-offset-3">
        <div class="col-xs-12 col-sm-6">	
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                <input type="password" class="form-control" name="password" placeholder="密码" maxlength="20" required="">
            </div>
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                <input type="password" class="form-control" name="password_confirmation" placeholder="再次输入密码" maxlength="20" required="">
            </div>
            <small class="help-block">仅允许字母、数字、破折号（-）以及底线（_），最小6个字符，最大20个字符</small>

            <input type="hidden" name="token" value="{{ $token }}">
            <button class="btn btn-lg btn-primary btn-block" type="submit">保存</button>
        </div>
    </div>	    	
</div>
{{ Form::close() }}

@stop