@extends('layouts.app')

@section('title', '登录 - 蒙太奇')

@section('content')

<div class="container">
    <div class="row">
    	<div class="col-md-6 right">
            <div class="card">
                <div class="card-header">快速注册</div>
                <div class="card-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/register') }}">
                        {!! csrf_field() !!}

                        <div class="form-group row {{ $errors->has('name') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">用户名</label>

                            <div class="col-md-6">
                                <input type="text" class="form-control" name="name" value="{{ old('name') }}">

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('email') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">邮箱地址</label>

                            <div class="col-md-6">
                                <input type="email" class="form-control" name="email" value="{{ old('email') }}">

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('password') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">密码</label>

                            <div class="col-md-6">
                                <input type="password" class="form-control" name="password">

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">确认密码</label>

                            <div class="col-md-6">
                                <input type="password" class="form-control" name="password_confirmation">

                                @if ($errors->has('password_confirmation'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6  mx-auto">
                                <button type="submit" class="btn btn-outline-info">
                                    <i class="fa fa-btn fa-user"></i>注册
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 left">
            <div class="card">
                <div class="card-header">
                	<!-- 
                	@if(isset($_SERVER['HTTP_REFERER']))
                		登陆后将返至上页<a title="{{ $_SERVER['HTTP_REFERER'] }}" href="javascript:void(0)">?</a>
                	@else
                		登录
                	@endif
                	 -->
                	登录
                	<!-- 
                	<div style="float: right">
                		<a class="" href="{{ url('/register') }}"><img src="/img/icon/quick.png" width="25px" class=""><span style="color: #007bff;width: 50px;" class="">快速注册</span></a>
                	</div>
                	 -->
                </div>
                <div class="card-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">
                        {!! csrf_field() !!}

                        <div class="form-group row {{ $errors->has('email') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">邮箱地址</label>

                            <div class="col-md-6">
                                <input type="email" class="form-control" name="email" value="{{ old('email') }}">

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('password') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">密码</label>

                            <div class="col-md-6">
                                <input type="password" class="form-control" name="password">

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6  mx-auto">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember"> 记住我
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6  mx-auto">
                                <button type="submit" class="btn btn-outline-info">
                                    <i class="fa fa-btn fa-sign-in"></i>登录
                                </button>

                                <a class="btn btn-link" href="{{ url('/password/reset') }}">忘记密码?</a><br/>
                                或通过<a href="{{url('/login/third/weibo')}}">weibo</a>、<a href="{{url('/login/third/github')}}">github</a>登录
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
$(document).ready(function () {
	var ua =  navigator.userAgent;
	isAndroid = /Android/i.test(ua);
	isBlackBerry = /BlackBerry/i.test(ua);
	isWindowPhone = /IEMobile/i.test(ua);
	isIOS = /iPhone|iPad|iPod/i.test(ua);
	isMobile = isAndroid || isBlackBerry || isWindowPhone || isIOS;
	if(isMobile){
		$(".right").insertAfter(".left");
	}
});
</script>
@endsection
