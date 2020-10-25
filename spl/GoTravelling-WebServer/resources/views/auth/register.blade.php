@extends('layout.master')

@section('styles')
    <link rel="stylesheet" href="{{asset('css/register-style.css')}}"/>
@stop

@section('topNav')
@stop

@section('content')
    <div class="main-container-fluid login-back-to-home-wrapper">
        <div class="back-to-home-wrapper">
            <a class="btn back-to-home" href="{{asset('/')}}">返回首页</a>
        </div>

    </div>

    <div class="main-container register-wrapper">

        <h2>GoTravelling</h2>

        <form class="register-form" method="post" role="form" action="{{ action('Auth\AuthController@postRegister')  }}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">

            <div>
                <label for="username">用户名</label>
                <input type="text" name="username" id="username" value="{{old('username')}}" required/> <div class="error-input">{{$errors->first('username')}}</div>
            </div>

            <div>
                <label for="password">密码</label>
                <input type="password" name="password" id="password" required/> <div class="error-input">{{$errors->first('password')}}</div>
            </div>

            <div>
                <label for="password_confirmation">确认密码</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required/> <div class="error-input">{{$errors->first('password_confirmation')}}</div>
            </div>


            <div class="form-button-wrapper">
                <button type="reset">重置表单</button>
                <button type="submit">立即注册</button>
            </div>

        </form>
    </div>


@endsection
