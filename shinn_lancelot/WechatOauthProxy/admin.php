<?php

    session_start();
    $user = isset($_SESSION['wop_admin_user']) ? $_SESSION['wop_admin_user'] : '';
    empty($user) && header('Location: ./login.php');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>后台管理</title>
    <link rel="shortcut icon" href="./asset/image/favicon.ico">
    <link rel="stylesheet" href="./asset/css/reset.css">
    <style>
        @media screen and (max-width: 640px) {
            .btn-size {
                height: 10vw;
                margin: 3vw auto 0;
                padding: 0 3vw;
                font-size: 4vw;
            }
            .user-size, .modify-password-size, .logout-size {
                height: 4vw;
                line-height: 4vw;
                font-size: 3vw;
                margin-left: 2vw;
                display: inline-block;
                vertical-align: top;
                overflow: hidden;
            }
            .logo {
                margin: 0 auto 10vw;
                width: 30vw;
                height: 30vw;
                background: url("./asset/image/oauth_proxy.png") no-repeat center / contain;
            }
        }

        @media screen and (min-width: 640px) {
            .btn-size {
                height: calc(640px * 0.1);
                margin: calc(640px * 0.03) auto 0;
                padding: 0 calc(640px * 0.03);
                font-size: calc(640px * 0.04);
            }
            .user-size, .modify-password-size, .logout-size {
                height: calc(640px * 0.04);
                line-height: calc(640px * 0.04);
                font-size: calc(640px * 0.03);
                margin-left: 2vw;
                display: inline-block;
                vertical-align: top;
                overflow: hidden;
            }
            .logo {
                margin: 0 auto calc(640px * 0.1);
                width: calc(640px * 0.3);
                height: calc(640px * 0.3);
                background: url("./asset/image/oauth_proxy.png") no-repeat center / contain;
            }
        }

        .wrapper {
            width: 100vw;
            height: 100vh;
        }

        .container {
            margin: 0 auto;
            width: 100%;
            max-width: 640px;
            height: 100vh;
            overflow: hidden;
            position: relative;
        }

        .user-box {
            width: 100%;
            padding: 0 2vw;
            box-sizing: border-box;
            text-align: right;
            position: absolute;
            top: 2vw;
            left: 0;
            z-index: 100;
            font-size: 0;
        }

        .user {
            color: #36a82e;
        }

        .modify-password-btn, .logout-btn {
            color: #ddd;
            text-decoration: underline;
            cursor: pointer;
            user-select: none;
        }

        .box {
            width: 100%;
            position: absolute;
            overflow: auto;
            top: 50%;
            left: 50%;
            z-index: 10;
            transform: translate(-50%, -50%);
            -webkit-transform: translate(-50%, -50%);
        }

        .box-content {
            margin: 0 2vw;
            max-height: 100vh;
        }

        .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            box-sizing: border-box;
            text-align: center;
            border: 0;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            color: #fff;
            background: #36a82e;
            cursor: pointer;
            border-radius: 3px;
            -moz-user-select: none;
            -o-user-select:none;
            -webkit-user-select:none;
            -ms-user-select:none;
            user-select: none;
        }

        .btn p {
            width: 100%;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        .disable {
            background: #ddd !important;
            cursor: not-allowed !important;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="container">
        <div class="user-box">
            <p class="user user-size"><?php echo $user; ?></p>
            <a href="./modifyPassword.php">
                <p class="modify-password-btn modify-password-size">修改密码</p>
            </a>
            <p class="logout-btn logout-size">退出登录</p>
        </div>
        <div class="box">
            <div class="box-content">
                <div class="logo"></div>
                <a href="./verify.php">
                    <div class="btn btn-size">
                        <p>添加微信公众号授权登录域名验证文件内容</p>
                    </div>
                </a>
                <a href="./safeDomainName.php">
                    <div class="btn btn-size">
                        <p>添加接口调用安全域名</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    var loginoutBtnObj = document.getElementsByClassName('logout-btn')[0],
        logoutState = 1,
        xhr = '';

    if (window.ActiveXObject) {
        xhr = new ActiveXObject("Microsoft.XMLHTTP");
    } else if (window.XMLHttpRequest) {
        xhr = new XMLHttpRequest();
    }

    var logout = function(e) {
        e.preventDefault();
        if (logoutState != 1) {
            return;
        }
        logoutState = 0;

        xhr.open('post', './common/logoutHandle.php', true);
        xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');
        xhr.send('');
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4) {
                responseObj = JSON.parse(xhr.response);
                alert(responseObj.message);
                if (responseObj.code == 1) {
                    setTimeout(function() {
                        window.location.href = './login.php';
                    }, 500);
                } else {
                    logoutState = 1;
                }
            } else {
                console.log(xhr.readyState);
            }
        }
    }

    loginoutBtnObj.addEventListener('click', function(e) {
        confirm('您确定要退出吗？') && logout(e);
    });
</script>
</body>
</html>