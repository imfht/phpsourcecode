@extends('BackTheme::layout.master')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">邮件管理</h3>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">邮件配置</h3>
            </div>
            <div class="panel-body">
                {{ Form::open(array('method' => 'post')) }}
                @if($errors->all())
                <div class="alert alert-danger alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    @foreach($errors->all() as $error)
                    {{$error}}<br/>
                    @endforeach
                </div>
                @endif
                <div class="form-group">
                    <label for="host">主机地址</label>
                    <input type="text" name="host" class="form-control" maxlength="100" required="" value="{{Input::old('host')?Input::old('host'):$host}}">
                    <small class="help-block">例如：mail.simplahub.com</small>
                </div>
                <div class="form-group">
                    <label for="port">端口号</label>
                    <input type="text" name="port" class="form-control" maxlength="10" required="" value="{{Input::old('port')?Input::old('port'):$port}}">
                </div>
                <div class="form-group">
                    <label for="username">用户名</label>
                    <input type="text" name="username" class="form-control" maxlength="30" required="" value="{{Input::old('username')?Input::old('username'):$username}}">
                </div>
                <div class="form-group">
                    <label for="password">密码</label>
                    <input type="text" name="password" class="form-control" maxlength="20" required="" value="{{Input::old('password')?Input::old('password'):$password}}">
                </div>
                <div class="form-group">
                    <label for="from_address">发送地址</label>
                    <input type="email" name="from_address" class="form-control" maxlength="100" required="" value="{{Input::old('from_address')?Input::old('from_address'):$from_address}}">
                    <small class="help-block">邮件收到时会显示该地址发出</small>
                </div>
                <div class="form-group">
                    <label for="from_name">发送名字</label>
                    <input type="text" name="from_name" class="form-control" maxlength="30" required="" value="{{Input::old('from_name')?Input::old('from_name'):$from_name}}">
                    <small class="help-block">邮件收到时会显示该用户</small>
                </div>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="pretend" {{$pretend?'checked':''}}> pretend模式
                    </label>
                </div>
                <small class="help-block">当在 pretend 模式开启下，邮件将会被写到您的应用程序日志中取代实际寄出信件。</small>
                <input class="btn btn-primary" type="submit" value="保存"/>
                {{ Form::close() }}
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">测试邮件</h3>
            </div>

            <div class="panel-body">
                {{ Form::open(array('url'=>'/admin/setting/email/send_test','method' => 'post')) }}
                <div class="form-group">
                    <label for="email">输入邮件地址</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="邮件地址" required="">
                </div>
                <button type="submit" class="btn btn-primary">点击发送测试邮件</button>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
<!--/.row -->
@stop