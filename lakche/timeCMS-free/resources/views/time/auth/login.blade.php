@extends($theme.'.layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="container">
            <div class="col-sm-offset-3 col-sm-6">
                <form id="registerForm" class="loginForm" action="{{ asset('auth/login') }}" method="post">
                    {!! csrf_field() !!}
                    <div class="row text-center">
                        <h2>用户登录</h2>
                    </div>
                    <div class="form-group">
                        <label for="name">用户名</label>
                        <input type="text" id="name" class="form-control" name="name" tabindex="4" value="{{ old('name') ? old('name') : $user_name }}"
                               placeholder="请输入用户名">
                        @if($errors->first('name'))
                            <p class="bg-danger">{{ $errors->first('name') }}</p>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="password">密码</label>
                        <input type="password" id="password" class="form-control" name="password" tabindex="5"
                               placeholder="请输入密码">
                        @if($errors->first('password'))
                            <p class="bg-danger">{{ $errors->first('password') }}</p>
                        @endif
                    </div>
                    <div class="form-group">
                        <label>验证码</label>
                        <div class="form-inline">
                            <input type="text" id="captcha" class="form-control" name="captcha" tabindex="6"
                               placeholder="请输入验证码">
                            <label class="captcha">{!! captcha_img() !!} <span class="glyphicon glyphicon-refresh"></span>点击刷新</label>
                        </div>
                        @if($errors->first('captcha'))
                            <p class="bg-danger">{{ $errors->first('captcha') }}</p>
                        @endif
                    </div>
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" id="remember" value="" checked="checked" name="remember"> 记住我
                        </label>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-6">
                            <input type="submit" class="btn btn-primary btn-block" tabindex="7"
                                   value="登 &nbsp; &nbsp; 录">
                        </div>
                        <div class="col-xs-6">
                            <input type="button" class="btn btn-success btn-block" tabindex="8"
                                   onclick="javascript:window.location='{{ asset('/') }}';" value="返回首页">
                        </div>
                    </div>
                    <div class="checkbox pull-right">
                        <label>还没有帐号？</label>
                        <a href="{{ asset('reg') }}" class="registor_now">马上注册</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="{{ asset($theme.'/js/captcha.js') }}"></script>
@endsection
