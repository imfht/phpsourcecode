<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>whoneed_cms智能后台管理系统</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.6 -->
    <link rel="stylesheet" href="/adminlte/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/adminlte/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="/adminlte/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="/adminlte/css/AdminLTE.min.css">
    <!-- iCheck -->
    <!-- link rel="stylesheet" href="/adminlte/css/blue.css" -->

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="/adminlte/js/html5shiv.min.js"></script>
    <script src="/adminlte/js/respond.min.js"></script>
    <![endif]-->
</head>
<body class="hold-transition login-page" style="height: 300px;">
<div class="login-box">
    <div class="login-logo">
        <a href="#">智能后台管理系统</a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg"></p>

        <form action="/admin/site/login" method="post">
            <div class="form-group has-feedback">
                <input type="text" class="form-control" placeholder="请输入用户名" name=User>
                <span class="glyphicon form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" class="form-control" placeholder="请输入密码" name=Pass>
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback row">
                <div class="col-xs-8">
                    <input type="text" class="form-control" placeholder="请输入验证码" name=authCode>
                    <span class="glyphicon form-control-feedback"></span>
                </div>
                <div class="col-xs-4">
                    <img src="/static/plug-in/verifyCode/authimg.php" align='top' width="80px;" height="30px;">
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
                </div>
                <!-- /.col -->
            </div>
        </form>
    </div>
    <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<!-- jQuery 2.2.3 -->
<script src="/adminlte/js/jquery.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="/adminlte/js/bootstrap.min.js"></script>
<!-- iCheck -->
<script src="/adminlte/js/icheck.min.js"></script>
<script>
    $(function () {
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });
    });
</script>
</body>
</html>