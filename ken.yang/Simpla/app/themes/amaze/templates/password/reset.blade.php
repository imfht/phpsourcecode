@extends('Theme::layout.page-single')

@section('content')

<div class="am-g">
    <div class="am-u-lg-6 am-u-md-8 am-u-sm-centered">
        <p></p>
        <h3>找回密码</h3>
        <hr>
        @if(Session::get('error'))
        <p class="am-text-danger">
            {{Session::get('error')}}
        </p>
        @endif
        {{ Form::open(array('url' => '/password/getreset','method' => 'post','class' => 'am-form')) }}
        <label for="email">密码:</label>
        <input type="password" class="form-control" name="password" placeholder="密码" maxlength="20" required="">
        <br>
        <label for="email">再次输入密码:</label>
        <input type="password" class="form-control" name="password_confirmation" placeholder="再次输入密码" maxlength="20" required="">
        <br>
        <small class="help-block">仅允许字母、数字、破折号（-）以及底线（_），最小6个字符，最大20个字符</small>
        <div class="am-cf">
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="submit" name="" value="保存" class="am-btn am-btn-primary am-btn-sm am-fl btn-loading">
        </div>
        {{ Form::close() }}
        <br>
    </div>
</div>

@stop