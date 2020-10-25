@extends('layout.master')
{{--add css file here--}}
{{HTML::style('css/authority/authority-common.css')}}
{{--add script file here--}}
{{HTML::script('packages/bower/requirejs/require.js',array('data-main'=>'../js/home-main.js'))}}


@section("title")
注册
@stop

{{--不要导航栏--}}
@section('mainTopNav')

@stop

@section("content")
  <div class="jumbotron authority-jumbotron">
    <h1>用户注册</h1>
    <a href="{{ URL::to('/') }}" class="btn btn-success"><i class="fa fa-mail-reply"></i>  返回首页</a>
  </div>

  <div class="container">
        {{ Form::open(['to'=>'/authority/signin', 'id'=>'content-form', 'class'=>'form-horizontal authority-form', 'role'=>'form']) }}
          <!-- username-block -->
          <div class="form-group">
            <div class="input-group">
              <div class="input-group-addon"><span class="glyphicon glyphicon-user"></span></div>
              <input type="text" class="form-control input-box" id="username" name="username" placeholder="请输入注册的用户名" required>
            </div>
            {{ $errors->first('username', '<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>:message</div>') }}
          </div>

          <!-- email-block -->
          <div id="email-block" class="form-group">
            <div class="input-group">
              <div class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span></div>
              <input type="text" class="form-control input-box" id="email" name="email" placeholder="请输入您的邮箱地址">
            </div>
            <div id="emailIM"></div>
            {{ $errors->first('email', '<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>:message</div>') }}
          </div>


          <!-- password-block -->
          <div class="form-group">
            <div class="input-group">
              <div class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></div>
              <input type="password" class="form-control input-box" id="password" name="password" placeholder="请输入注册的密码" required>
            </div>
            {{ $errors->first('password', '<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>:message</div>') }}
          </div>

          <!-- password-confirmation-block -->
          <div id="password-confirmation-block" class="form-group">
            <div class="input-group">
              <div class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></div>
              <input type="password" class="form-control input-box" id="password_confirmation" name="password_confirmation" placeholder="请再次输入密码" required>
            </div>
            <div id="pwcIM"></div>
            {{ $errors->first('password_confirmation', '<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>:message</div>') }}
          </div>

         <!-- submit-block -->
          <div id="submit-block" class="form-group">
            <button id="signin-submit" type="submit" class="btn btn-default authority-submit">注册</button>
          </div>
          <span id="jump-login">已有账号?点击<a href="{{ URL::to('/authority/login') }}">登入</a></span>

        {{ Form::close() }}
  </div>

@stop
