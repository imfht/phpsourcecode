@extends('auth.layout')
@section('title')
    密码重置
@endsection
@section('content')
    @include('partials.errors')
    @include('partials.success')
    <form class="form-horizontal" role="form" method="POST"  action="/password/reset">
        {!! csrf_field() !!}
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="form-group">
            <label class="col-md-4 control-label">电子邮箱</label>
            <div class="col-md-6">
                <input type="email" class="form-control" name="email" value="{{ old('email') }}" autofocus>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label">新密码</label>
            <div class="col-md-6">
                <input type="password" class="form-control" name="password" >
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label">确认密码</label>
            <div class="col-md-6">
                <input type="password" class="form-control" name="password_confirmation" >
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-4 col-md-offset-2">
                <button type="submit" id = "alert" class="btn btn-success " style="width:290px;height:35px">重置密码</button>
            </div>
        </div>
    </form>
@endsection
