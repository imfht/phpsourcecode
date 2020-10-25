<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin</title>
    <link rel="stylesheet" href="/assets/admin/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/admin/css/bootstrap-theme.min.css">
    <script src="/assets/admin/js/jquery.min.js"></script>
    <script src="/assets/admin/js/bootstrap.min.js"></script>

    <link rel="stylesheet" href="/assets/admin/css/common.css">
    <script src="/assets/admin/js/common.js"></script>
    <style>
        .login-box{
            position: absolute;
            top: 15%;
            left: 50%;
            width: 450px;
            margin-left: -225px;
        }
    </style>
</head>
<body>
<div class="layout-main login-box">
    <form class="form" action="/admin?c=Account&a=login" method="POST">
        <div class="form-group">
            <label>账号</label>
            <input class="form-control" type="text" name="username" required>
        </div>
        <div class="form-group">
            <label>密码</label>
            <input class="form-control" type="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-success">登录</button>
    </form>
</div>
</body>
</html>