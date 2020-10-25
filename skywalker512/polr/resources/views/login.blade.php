@extends('layouts.base')

@section('css')
<link rel='stylesheet' href='css/login.css' />
@endsection

@section('content')
<div class="center-text">
    <h1>登 录</h1><br/><br/>
    <div class="col-md-3"></div>
    <div class="col-md-6">
        <form action="login" method="POST">
            <input type="text" placeholder="用户名" name="username" class="form-control login-field" />
            <input type="password" placeholder="密 码" name="password" class="form-control login-field" />
            <input type="hidden" name='_token' value='{{csrf_token()}}' />
            <input type="submit" value="登 录" class="login-submit btn btn-success" />

            <p class='login-prompts'>
            @if (env('POLR_ALLOW_ACCT_CREATION') == true)
                <small>注册一个账号 <a href='{{route('signup')}}'>注 册</a></small>
            @endif

            @if (env('SETTING_PASSWORD_RECOV') == true)
                <small>忘记密码？ <a href='{{route('lost_password')}}'>重置</a></small>
            @endif
            </p>
        </form>
    </div>
    <div class="col-md-3"></div>
</div
@endsection
