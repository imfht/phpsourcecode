@extends($theme.'.layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="container">
            <div class="col-sm-offset-3 col-sm-6">
                <form id="registerForm" class="loginForm" action="{{ asset('auth/register') }}" method="post">
                    {!! csrf_field() !!}
                    <div class="row text-center">
                        <h2>用户注册</h2>
                    </div>
                    <div class="form-group">
                        <label for="name">用户名</label>
                        <input type="text" id="name" class="form-control" name="name" tabindex="4"
                               value="{{ old('name') }}"
                               placeholder="用户名长度为1-20个字符">

                        @if($errors->first('name'))
                            <p class="bg-danger">{{ $errors->first('name') }}</p>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="password">密码</label>
                        <input type="password" id="password" class="form-control" name="password" tabindex="5"
                               placeholder="密码长度为6-20个字符">

                        @if($errors->first('password'))
                            <p class="bg-danger">{{ $errors->first('password') }}</p>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="password">重复密码</label>
                        <input type="password" id="password_confirmation" class="form-control" name="password_confirmation"
                               tabindex="6"
                               placeholder="请重复密码">
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
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary btn-block" value="注 &nbsp; &nbsp; 册">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="{{ asset($theme.'/js/captcha.js') }}"></script>
@endsection
