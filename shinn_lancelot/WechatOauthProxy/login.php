<?php

    session_start();
    $user = isset($_SESSION['wop_admin_user']) ? $_SESSION['wop_admin_user'] : '';
    !empty($user) && header('Location: ./admin.php');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>后台管理登录</title>
    <link rel="shortcut icon" href="./asset/image/favicon.ico">
    <link rel="stylesheet" href="./asset/css/reset.css">
    <style>
        @media screen and (max-width: 640px) {
            .field {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 100%;
                height: 10vw;
                margin: 3vw auto 0;
                padding: 0 10vw;
                box-sizing: border-box;
                text-align: left;
                font-size: 4vw;
                border: 0;
                overflow: hidden;
                white-space: nowrap;
                text-overflow: ellipsis;
            }
            .icon-clear {
                background: url("./asset/image/clear.png") no-repeat center / contain;
                top: 2.5vw;
                right: 2.5vw;
                cursor: pointer;
                display: none;
            }
            .icon-user {
                background: url("./asset/image/user.png") no-repeat center / contain;
                top: 2.5vw;
                left: 2.5vw;
            }
            .icon-user-selected {
                background: url("./asset/image/user_selected.png") no-repeat center / contain !important;
                top: 2.5vw;
                left: 2.5vw;
            }
            .icon-password {
                background: url("./asset/image/password.png") no-repeat center / contain;
                top: 2.5vw;
                left: 2.5vw;
            }
            .icon-password-selected {
                background: url("./asset/image/password_selected.png") no-repeat center / contain;
                top: 2.5vw;
                left: 2.5vw;
            }
            .logo {
                margin: 0 auto 10vw;
                width: 30vw;
                height: 30vw;
                background: url("./asset/image/oauth_proxy.png") no-repeat center / contain;
            }
        }

        @media screen and (min-width: 640px) {
            .field {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 100%;
                height: calc(640px * 0.1);
                margin: calc(640px * 0.03) auto 0;
                padding: 0 calc(640px * 0.1);
                box-sizing: border-box;
                text-align: left;
                font-size: calc(640px * 0.04);
                border: 0;
                overflow: hidden;
                white-space: nowrap;
                text-overflow: ellipsis;
            }
            .icon-clear {
                background: url("./asset/image/clear.png") no-repeat center / contain;
                top: calc(640px * 0.025);
                right: calc(640px * 0.025);
                cursor: pointer;
                display: none;
            }
            .icon-user {
                background: url("./asset/image/user.png") no-repeat center / contain;
                top: calc(640px * 0.025);
                left: calc(640px * 0.025);
            }
            .icon-user-selected {
                background: url("./asset/image/user_selected.png") no-repeat center / contain !important;
                top: calc(640px * 0.025);
                left: calc(640px * 0.025);
            }
            .icon-password {
                background: url("./asset/image/password.png") no-repeat center / contain;
                top: calc(640px * 0.025);
                left: calc(640px * 0.025);
            }
            .icon-password-selected {
                background: url("./asset/image/password_selected.png") no-repeat center / contain;
                top: calc(640px * 0.025);
                left: calc(640px * 0.025);
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

        .box {
            width: 100%;
            height: auto;
            position: absolute;
            top: 50%;
            left: 50%;
            z-index: 10;
            transform: translate(-50%, -50%);
            -webkit-transform: translate(-50%, -50%);
        }

        #form {
            margin: 0 2vw;
        }

        #user, #password {
            background: transparent;
            border-bottom: 1px solid #eee;
        }

        #login_btn {
            text-align: center;
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

        #login_btn p {
            width: 100%;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        .input-box {
            position: relative;
        }

        .disable {
            background: #ddd !important;
            cursor: not-allowed !important;
        }

        .line {
            border-bottom: 1px solid #36a82e !important;
        }

        .icon {
            position: absolute;
            width: 5vw;
            height: 5vw;
            max-width: calc(640px * 0.05);
            max-height: calc(640px * 0.05);
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="container">
        <div class="box">
            <div class="logo"></div>
            <form id="form">
                <div class="input-box">
                    <i class="icon icon-user"></i>
                    <input type="text" class="field" name="user" id="user" value="" placeholder="用户名">
                    <i class="icon icon-clear" title="移除"></i>
                </div>
                <div class="input-box">
                    <i class="icon icon-password"></i>
                    <input type="password" class="field" name="password" id="password" value="" placeholder="密码">
                    <i class="icon icon-clear" title="移除"></i>
                </div>
                <div class="field disable" id="login_btn">
                    <p>登录</p>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    var loginBtnObj = document.getElementById('login_btn'),
        userObj = document.getElementById('user'),
        passwordObj = document.getElementById('password'),
        iconUserObj = document.getElementsByClassName('icon-user')[0],
        iconPasswordObj = document.getElementsByClassName('icon-password')[0],
        xhr = '',
        user = '',
        password = '',
        responseObj = '',
        loginState = 1,
        userClass = '',
        passwordClass = '',
        clearIconObjs = document.getElementsByClassName('icon-clear'),
        formObj = document.getElementById('form');

    if (window.ActiveXObject) {
        xhr = new ActiveXObject("Microsoft.XMLHTTP");
    } else if (window.XMLHttpRequest) {
        xhr = new XMLHttpRequest();
    }

    var loginFunc = function (e) {
        e.preventDefault();
        if (loginState != 1) {
            return;
        }
        loginState = 0;

        user = userObj.value;
        if (user == '') {
            alert('请输入用户名！');
            loginState = 1;
            return;
        }
        password = passwordObj.value;
        if (password == '') {
            alert('请输入密码！');
            loginState = 1;
            return;
        }

        xhr.open('post', './common/loginHandle.php', true);
        xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');
        xhr.send('user=' + user + '&password=' + password);
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4) {
                responseObj = JSON.parse(xhr.response);
                alert(responseObj.message);
                if (responseObj.code == 1) {
                    setTimeout(function() {
                        window.location.href = './admin.php';
                    }, 500);
                } else {
                    loginState = 1;
                }
            } else {
                console.log(xhr.readyState);
            }
        }
    }

    var clearFunc = function (e) {
        var inputId = this.previousElementSibling.getAttribute('id');
        if (inputId == 'user') {
            userObj.value = '';
            user = '';
            iconUserObj.setAttribute('class', 'icon icon-user');
        }
        if (inputId == 'password') {
            passwordObj.value = '';
            password = '';
            iconPasswordObj.setAttribute('class', 'icon icon-password');
        }

        loginBtnClass = loginBtnObj.getAttribute('class');
        if (loginBtnClass == 'field') {
            loginBtnObj.setAttribute('class', 'field disable');
        }
        loginBtnObj.removeEventListener('click', loginFunc);
        this.style.display = 'none';
    }

    userObj.addEventListener('input', function (e) {
        listenInput();
    });

    passwordObj.addEventListener('input', function (e) {
        listenInput();
    });

    function listenInput() {
        loginBtnObj.removeEventListener('click', loginFunc);
        user = userObj.value;
        password = passwordObj.value;
        loginBtnClass = loginBtnObj.getAttribute('class');
        if (user.length > 0 && password.length > 0) {
            if (loginBtnClass == 'field disable') {
                loginBtnObj.setAttribute('class', 'field');
            }
            loginBtnObj.addEventListener('click', loginFunc);
        } else {
            if (loginBtnClass == 'field') {
                loginBtnObj.setAttribute('class', 'field disable');
            }
            loginBtnObj.removeEventListener('click', loginFunc);
        }
        if (user.length > 0) {
            iconUserObj.setAttribute('class', 'icon icon-user icon-user-selected');
            userObj.nextElementSibling.style.display = 'block';
        } else {
            iconUserObj.setAttribute('class', 'icon icon-user');
            userObj.nextElementSibling.style.display = 'none';
        }
        if (password.length > 0) {
            iconPasswordObj.setAttribute('class', 'icon icon-password icon-password-selected');
            passwordObj.nextElementSibling.style.display = 'block';
        } else {
            iconPasswordObj.setAttribute('class', 'icon icon-password');
            passwordObj.nextElementSibling.style.display = 'none';
        }
    }

    userObj.addEventListener('focusin', function (e) {
        userClass = this.getAttribute('class');
        if (userClass == 'field') {
            this.setAttribute('class', 'field line');
        }
    });

    passwordObj.addEventListener('focusin', function (e) {
        passwordClass = this.getAttribute('class');
        if (passwordClass == 'field') {
            this.setAttribute('class', 'field line');
        }
    });

    userObj.addEventListener('focusout', function (e) {
        userClass = this.getAttribute('class');
        if (userClass == 'field line') {
            this.setAttribute('class', 'field');
        }
    });

    passwordObj.addEventListener('focusout', function (e) {
        passwordClass = this.getAttribute('class');
        if (passwordClass == 'field line') {
            this.setAttribute('class', 'field');
        }
    });

    for (var i = 0; i < clearIconObjs.length; i++) {
        clearIconObjs[i].addEventListener('click', clearFunc);
    }

    document.addEventListener('keydown', function (e) {
        user = userObj.value;
        password = passwordObj.value;
        if (e.keyCode == 13) {
            if (user.length > 0 && password.length > 0) {
                loginFunc(e);
            } else {
                e.preventDefault();
            }
        }
    });
</script>
</body>
</html>