@extends('layouts.app')
@section('importCss')
 <link href="{{ asset('css/loginBackground.css') }}" rel="stylesheet">
@endsection
@section('content')
<div id="backgoundImage"><img src="/img/auth/1.jpg" /></div> 
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">注册</div>
                <div class="panel-body">
                    <form class="form-horizontal" id="registerForm" role="form" method="POST" action="/register/post">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">姓名</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control name" name="name" 
                                value="{{ old('name') }}" required autofocus>

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('companyName') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">公司名称</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control companyName" name="companyName" 
                                value="{{ old('companyName') }}" required autofocus>

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('companyName') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">邮箱</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control email" name="email" 
                                value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">密码</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control password" autocomplete="off"
                                oncontextmenu="return false" onpaste="return false" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password-confirm" class="col-md-4 control-label">确认密码</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control confirmPassword" name="confirmPassword"
                                 oncontextmenu="return false" onpaste="return false" required>
                            </div>
                        </div>

						<div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                            <label for="phone" class="col-md-4 control-label">电话</label>

                            <div class="col-md-6">
                                <input id="phone" type="number" class="form-control phone" name="phone" 
                                value="{{ old('phone') }}" required>
                                @if ($errors->has('phone'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
						<div class="form-group{{ $errors->has('checkCode') ? ' has-error' : '' }}">
                            <label for="phone" class="col-md-4 control-label">验证码</label>

                            <div class="col-md-4">
                                <input id="checkCode" type="number" class="form-control" name="checkCode" value="{{ old('checkCode') }}">
                                @if ($errors->has('checkCode'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('checkCode') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <button class="btn btn-info col-md-2" id="getSMSCode">获取验证码</button>
                        </div>
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary" id="submit">
                                    	注册
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('importJs')
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/jquery.form.js') }}"></script>
<script src="{{ asset('js/plugins/layer/layer.min.js') }}"></script>
<script src="{{ asset('js/plugins/auth/common.js') }}"></script>
<script src="{{ asset('js/jquery.validate.min.js?var1.14.0.js') }}"></script>
<script src="{{ asset('js/plugins/auth/register.js') }}"></script>
@endsection