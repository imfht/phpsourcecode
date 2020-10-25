@extends('auth.layout')
@section('script')
    <link href="{{ asset('/css/auth.css') }}" rel="stylesheet" type="text/css">
@endsection
@section('title')
    创建账户
@endsection
@section('content')
    @include('partials.errors')
    <form class="form-horizontal" role="form" method="POST" action="{{ url('/auth/register') }}">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="form-group">
            <label class="col-md-4 control-label">设置用户名</label>
            <div class="col-md-6">
                <input type="text" class="form-control" name="name" autofocus>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label">你的电子邮箱</label>
            <div class="col-md-6">
                <input type="email" class="form-control" name="email" value="{{ old('email') }}" >
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-4 control-label">设置密码</label>
            <div class="col-md-6">
                <input type="password" class="form-control" name="password">
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label">确认密码</label>
            <div class="col-md-6">
                <input type="password" class="form-control" name="password_confirmation">
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-6 col-md-offset-3">
                <a href="/auth/login"><button type="submit" class="btn btn-success " style="width:100%;height:35px" >创建账户</button></a>

            </div>
        </div>
        <div class="form-group" style="font-size: 1.2rem">

            <div class="col-md-6 col-md-offset-4">
                已经拥有账号？<a href="/auth/login" >登录</a>
            </div>
        </div>
    </form>
@endsection