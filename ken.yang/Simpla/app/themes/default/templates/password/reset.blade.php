@extends('Theme::layout.page-single')

@section('content')

{{ Form::open(array('url' => '/password/getreset','method' => 'post','class' => 'form-signin')) }}
<h2 class="form-signin-heading">重置密码</h2>

@if(Session::get('error'))
<div class="alert alert-danger alert-dismissable">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    {{Session::get('error')}}<br/>
</div>
@endif

<div class="form-group">
    <label for="password">密码：</label>
    <input type="password" class="form-control" name="password" placeholder="密码" maxlength="20" required="">
</div>
<div class="form-group">
    <input type="password" class="form-control" name="password_confirmation" placeholder="请再次输入密码" maxlength="20" required="">
    <small class="help-block">仅允许字母、数字、破折号（-）以及底线（_），最小6个字符，最大20个字符</small>
</div>
<input type="hidden" name="token" value="{{ $token }}">
<button type="submit" class="btn btn-lg btn-primary btn-block">保存</button>
{{ Form::close() }}

@stop