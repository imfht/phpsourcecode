<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo (session('_systemname')); ?></title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="/uap/Public/adminlte//bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/uap/Public/adminlte//dist/css/AdminLTE.min.css">
    <link rel="stylesheet" href="/uap/Public/adminlte//plugins/iCheck/square/blue.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <a href="#"><b><?php echo (session('_systemname')); ?></a>
    </div>
    <div class="login-box-body">
        <p style="display: none" class="login-box-msg">登陆</p>

        <form id="form">
            <div class="form-group has-feedback">
                <input type="text" name="name" class="form-control" placeholder="用户名">
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" name="passwd" class="form-control" placeholder="密码">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <div class="row">
                <div class="col-xs-8">
                </div>
                <div class="col-xs-4">
                    <a onclick="checkLogin()" href="javascript:void(0);"
                       class="btn btn-primary btn-block btn-flat">登陆</a>
                </div>
            </div>
        </form>


    </div>
</div>
<script src="/uap/Public/adminlte//plugins/jQuery/jQuery-2.1.4.min.js"></script>
<script src="/uap/Public/adminlte//bootstrap/js/bootstrap.min.js"></script>
<script src="/uap/Public/adminlte//plugins/iCheck/icheck.min.js"></script>
<script>
    $(function () {
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });
    });

    function checkLogin() {
        var callback = '<?php echo ($_GET['callback']); ?>';
        var data = $('#form').serialize();
        $.post('/uap/home/login/checklogin', data, function (rs) {
            if (rs == "") {
                if (callback == '') {
                    window.location.href = '/uap/home/index';
                } else {
                    window.location.href = "http://" + callback;
                }
            } else {
                alert("用户名密码不匹配");
            }
        })
    }
</script>
</body>
</html>