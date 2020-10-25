<!DOCTYPE html>

<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>管理后台登录</title>
    <link rel="stylesheet" href="{{asset('iassets/plugins/layui/css/layui.css')}}" media="all" />
    <link rel="stylesheet" href="{{asset('iassets/css/login.css')}}" />
</head>

<body class="beg-login-bg">
<div class="beg-login-box">
    <header>
        <h1>后台登录</h1>
    </header>
    <div class="beg-login-main">
        <form action="{{ route('admin.login.post') }}" class="layui-form" method="post">
            {!! csrf_field() !!}
            @if (count($errors) > 0)
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
            <div class="layui-form-item">
                <label class="beg-login-icon">
                    <i class="layui-icon">&#xe612;</i>
                </label>
                <input type="text" name="email" lay-verify="userName" autocomplete="off" placeholder="请输入登录邮箱" class="layui-input">
            </div>
            <div class="layui-form-item">
                <label class="beg-login-icon">
                    <i class="layui-icon">&#xe642;</i>
                </label>
                <input type="password" name="password" lay-verify="password" autocomplete="off" placeholder="请输入密码" class="layui-input">
            </div>
            <div class="layui-form-item">
                <div class="beg-pull-left beg-login-remember">
                    <label>记住帐号？</label>
                    <input type="checkbox" name="remember" value="1" lay-skin="switch" checked title="记住帐号">
                </div>
                <div class="beg-pull-right">
                    <button class="layui-btn layui-btn-primary" lay-submit lay-filter="login">
                        <i class="layui-icon">&#xe650;</i> 登录
                    </button>
                </div>
                <div class="beg-clear"></div>
            </div>
        </form>
    </div>
    <footer>
        <p>iadmin.dev 后台管理系统</p>
    </footer>
</div>
<script type="text/javascript" src="{{asset('iassets/plugins/layui/layui.js')}}"></script>
<script>
    layui.use(['layer', 'form'], function() {
        var layer = layui.layer, $ = layui.jquery, form = layui.form();
    });
</script>
</body>

</html>