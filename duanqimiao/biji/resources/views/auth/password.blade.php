@extends('auth.layout')
<div style="text-align: right">
    <ul class="list-group">
        <li class="list-group-item list-group-item-info"><a href="{{ url('auth/login') }}">登录</a></li>
    </ul>
</div>

@section('title')
    忘记密码？
@endsection
@section('content')
    @include('partials.errors')
    @include('partials.success')
    <form class="form-horizontal" role="form" method="POST" action="/password/email">

        {!! csrf_field() !!}
        <div class="form-group">
            <label class="col-md-4 control-label">电子邮箱</label>
            <div class="col-md-6">
                <input type="email" class="form-control" name="email" value="{{ old('email') }}" autofocus>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-9 col-md-offset-2">
                <button type="submit" id = "alert" class="btn btn-success " style="width:100%;height:35px">发送密码重置链接</button>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-9 col-md-offset-2">
            <ul class="list-group">
                <li class="list-group-item list-group-item-success">

                    请输入你的用户名或注册电子邮箱。

                    你将收到一封确认邮件，可以重新设置密码。
                </li>
            </ul>
            </div>
        </div>
    </form>
@endsection