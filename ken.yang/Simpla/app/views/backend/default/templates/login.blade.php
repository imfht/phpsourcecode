<!--后台展示信息-->
@extends('BackTheme::layout.master_single')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="login-panel panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">后台登录</h3>
                </div>
                <div class="panel-body">
                    {{Form::open(array('method'=>'post'))}}
                    <fieldset>
                        @if($message)
                        <div class="alert alert-danger alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            {{ $message }}
                        </div>
                        @endif
                        <div class="form-group">
                            <input class="form-control" placeholder="用户名/邮箱" name="username_or_email" type="text" required="" autofocus  value='{{Input::old('username_or_email')}}'>
                        </div>
                        <div class="form-group">
                            <input class="form-control" placeholder="密码" name="password" type="password" value="" required="" value='{{Input::old('password')}}'>
                        </div>
                        <div class="form-group">
                            <img src="{{Captcha::getImage('4','100','60')}}" class="img-responsive pull-right">
                            <input class="form-control" placeholder="请输入验证码" name="user-captcha" type="text" value="" required="" style="width: 200px">
                        </div>
                        <div class="checkbox">
                            <label>
                                <input name="remember" type="checkbox" value="1">记住我
                            </label>
                        </div>
                        <!-- Change this to a button or input when using this as a form -->
                        <input type="submit" class="btn btn-lg btn-success btn-block" value="登录"/>
                    </fieldset>
                    {{Form::close()}}
                </div>
            </div>
        </div>
    </div>
</div>


@stop