@extends('Theme::layout.page-single')

@section('content')

{{ Form::open(array('url' => '/password/getremind','method' => 'post','class' => 'form-signin')) }}
<h2 class="form-signin-heading">找回密码</h2>

@if(isset($error))
<div class="alert alert-danger alert-dismissable">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    {{$error}}<br/>
</div>
@endif
@if(isset($status))
<div class="alert alert-danger alert-dismissable">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    {{$status}}<br/>
</div>
@endif

<div class="form-group">
    <label for="email">邮箱：</label>
    <input type="email" class="form-control" name="email" placeholder="请输入邮箱" autofocus="" maxlength="256" required="">
</div>

<button type="submit" class="btn btn-lg btn-primary btn-block">确认发送邮件</button>

<div class="margin-top-20"><a href="/login">登陆</a>   |   <a href="/register">注册</a></div>
{{ Form::close() }}

@stop