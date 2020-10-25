@extends('Theme::layout.page-single')

@section('content')


<div class="am-g">
    <div class="am-u-lg-6 am-u-md-8 am-u-sm-centered">
        <p></p>
        <h3>找回密码</h3>
        <hr>
        @if(isset($error))
        <p class="am-text-danger">
            {{$error}}
        </p>
        @endif
        @if(isset($status))
        <p class="am-text-danger">
            {{$status}}
        </p>
        @endif
        {{ Form::open(array('url' => '/password/getremind','method' => 'post','class' => 'am-form')) }}
        <label for="email">请输入邮箱:</label>
        <input type="email" class="form-control" name="email" placeholder="请输入邮箱" autofocus="" maxlength="256" required="">
        <br>
        <div class="am-cf">
            <input type="submit" name="" value="确认发送邮件" class="am-btn am-btn-primary am-btn-sm am-fl btn-loading">
            <a href="/register" class="am-btn am-btn-default am-btn-sm am-fr">注册</a>
            <a href="/login" class="am-btn am-btn-default am-btn-sm am-fr">登陆</a>
        </div>
        {{ Form::close() }}
        <br>
    </div>
</div>


@stop