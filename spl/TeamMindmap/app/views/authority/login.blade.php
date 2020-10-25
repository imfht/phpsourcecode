@extends('layout.master')

{{--add css file here--}}
{{HTML::style('css/authority/authority-common.css')}}
{{--add script file here--}}
{{HTML::script('packages/bower/requirejs/require.js',array('data-main'=>'../js/home-main.js'))}}


@section("title")
登入
@stop

{{--不要导航栏--}}
@section('mainTopNav')

@stop

@section("content")
    {{-----------------------巨幕---------------------------}}
    <div class="jumbotron authority-jumbotron">
        <h1>用户登录</h1>
        <a href="{{ URL::to('/') }}" class="btn btn-success"><i class="fa fa-mail-reply"></i>  返回首页</a>
    </div>
    <div class="container">
       {{ Form::open(['action'=>'AuthorityController@postLogin',
          'id'=>'login-form',
          'class'=>'form-horizontal authority-form',
          'role'=>'form'])
        }}
                <!-- username-block -->
                <div class="form-group">
                    <div class="input-group">
                      <div class="input-group-addon"><span class="glyphicon glyphicon-user"></span></div>
                      <input type="text" class="form-control  input-box" id="login-identify" name="identify" placeholder="请输入您的用户名或邮箱" required>
                    </div>
                    {{ $errors->first('loginFailed', '<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>:message</div>') }}
                </div>

                <!-- password-block -->
                <div class="form-group">
                    <div class="input-group">
                      <div class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></div>
                      <input type="password" class="form-control  input-box" id="password" name="password" placeholder="请输入您的密码" required>
                    </div>
                </div>

                {{--------提交按钮--------}}
                <div class="form-group">
                    <button type="submit" class="btn btn-default authority-submit">登入</button>
                </div>

                <!-- jump-to-signin -->
                <div id="jump-to-signin">
                    <p id="jump">没有账号?点击<a href="signin">注册</a></p>
                </div>

        {{ Form::close() }}

    </div>
@stop
