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
    <title>修改密码</title>
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
                text-align: center;
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
            .home {
                width: 4vw;
                height: 4vw;
                background: url("./asset/image/home.png") no-repeat center / contain;
                float: left;
                display: inline-block;
                vertical-align: top;
                cursor: pointer;
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
                text-align: center;
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
            .home {
                width: calc(640px * 0.04);
                height: calc(640px * 0.04);
                background: url("./asset/image/home.png") no-repeat center / contain;
                float: left;
                display: inline-block;
                vertical-align: top;
                cursor: pointer;
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

        #old_password, #new_password, #again_new_password {
            background: transparent;
            border-bottom: 1px solid #eee;
        }

        #submit_btn {
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

        #submit_btn p {
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
            display: none;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="container">
        <div class="user-box">
            <a href="./admin.php"><div class="home" title="管理页"></div></a>
            <p class="user user-size"><?php echo $user; ?></p>
            <p class="modify-password-btn modify-password-size">修改密码</p>
            <p class="logout-btn logout-size">退出登录</p>
        </div>
        <div class="box">
            <form id="form">
                <div class="input-box">
                    <input type="password" class="field" name="old_password" id="old_password" value="" autocomplete="off" placeholder="请填写旧密码">
                    <i class="icon icon-clear" title="移除"></i>
                </div>
                <div class="input-box">
                    <input type="password" class="field" name="new_password" id="new_password" value="" autocomplete="off" placeholder="请填写新密码">
                    <i class="icon icon-clear" title="移除"></i>
                </div>
                <div class="input-box">
                    <input type="password" class="field" name="again_new_password" id="again_new_password" value="" autocomplete="off" placeholder="请再次填写新密码">
                    <i class="icon icon-clear" title="移除"></i>
                </div>
                <div class="field disable" id="submit_btn">
                    <p>提交</p>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    var submitBtnObj = document.getElementById('submit_btn'),
        oldPasswordObj = document.getElementById('old_password'),
        newPasswordObj = document.getElementById('new_password'),
        againNewPasswordObj = document.getElementById('again_new_password'),
        xhr = '',
        oldPassword = '',
        newPassword = '',
        againNewPassword = '',
        user = document.getElementsByClassName('user')[0].innerHTML,
        responseObj = '',
        submitState = 1,
        submitBtnClass = '',
        oldPasswordClass = '',
        newPasswordClass = '',
        againNewPasswordClass = '',
        clearIconObjs = document.getElementsByClassName('icon-clear'),
        formObj = document.getElementById('form'),
        loginoutBtnObj = document.getElementsByClassName('logout-btn')[0],
        logoutState = 1;

    if (window.ActiveXObject) {
        xhr = new ActiveXObject("Microsoft.XMLHTTP");
    } else if (window.XMLHttpRequest) {
        xhr = new XMLHttpRequest();
    }

    var submitFunc = function (e) {
        e.preventDefault();
        if (submitState != 1) {
            return;
        }
        submitState = 0;

        oldPassword = oldPasswordObj.value;
        if (oldPassword == '') {
            alert('请填写旧密码！');
            submitState = 1;
            return;
        }
        newPassword = newPasswordObj.value;
        if (newPassword == '') {
            alert('请填写新密码！');
            submitState = 1;
            return;
        }
        againNewPassword = againNewPasswordObj.value;
        if (againNewPassword == '') {
            alert('请再次填写新密码！');
            submitState = 1;
            return;
        }
        if (newPassword != againNewPassword) {
            alert('两次输入的新密码不一致！请重新填写！');
            submitState = 1;
            return
        }

        xhr.open('post', './common/modifyPasswordHandle.php', true);
        xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');
        xhr.send('user=' + user + '&old_password=' + oldPassword + '&new_password=' + newPassword + '&again_new_password=' + againNewPassword);
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4) {
                responseObj = JSON.parse(xhr.response);
                alert(responseObj.message);
                responseObj.code == 1 && setTimeout(function() {
                    window.location.href = './admin.php';
                }, 500);
                responseObj.code == -1 && setTimeout(function() {
                    window.location.href = './login.php';
                }, 500);
                if (responseObj.code != -1) {
                    submitState = 1;
                }
            } else {
                console.log(xhr.readyState);
            }
        }
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

    var clearFunc = function (e) {
        var inputId = this.previousElementSibling.getAttribute('id');
        if (inputId == 'old_password') {
            oldPasswordObj.value = '';
            oldPassword = '';
        }
        if (inputId == 'new_password') {
            newPasswordObj.value = '';
            newPassword = '';
        }
        if (inputId == 'again_new_password') {
            againNewPasswordObj.value = '';
            againNewPassword = '';
        }

        submitBtnClass = submitBtnObj.getAttribute('class');
        if (submitBtnClass == 'field') {
            submitBtnObj.setAttribute('class', 'field disable');
        }
        submitBtnObj.removeEventListener('click', submitFunc);
        this.style.display = 'none';
    }

    oldPasswordObj.addEventListener('input', function (e) {
        listenInput();
    });

    newPasswordObj.addEventListener('input', function (e) {
        listenInput();
    });

    againNewPasswordObj.addEventListener('input', function (e) {
        listenInput();
    });

    loginoutBtnObj.addEventListener('click', function(e) {
        confirm('您确定要退出吗？') && logout(e);
    });

    function listenInput() {
        submitBtnObj.removeEventListener('click', submitFunc);
        oldPassword = oldPasswordObj.value;
        newPassword = newPasswordObj.value;
        againNewPassword = againNewPasswordObj.value;
        submitBtnClass = submitBtnObj.getAttribute('class');
        if (oldPassword.length > 0 && newPassword.length > 0 && againNewPassword.length > 0) {
            if (submitBtnClass == 'field disable') {
                submitBtnObj.setAttribute('class', 'field');
            }
            submitBtnObj.addEventListener('click', submitFunc);
        } else {
            if (submitBtnClass == 'field') {
                submitBtnObj.setAttribute('class', 'field disable');
            }
            submitBtnObj.removeEventListener('click', submitFunc);
        }
        if (oldPassword.length > 0) {
            oldPasswordObj.nextElementSibling.style.display = 'block';
        } else {
            oldPasswordObj.nextElementSibling.style.display = 'none';
        }
        if (newPassword.length > 0) {
            newPasswordObj.nextElementSibling.style.display = 'block';
        } else {
            newPasswordObj.nextElementSibling.style.display = 'none';
        }
        if (againNewPassword.length > 0) {
            againNewPasswordObj.nextElementSibling.style.display = 'block';
        } else {
            againNewPasswordObj.nextElementSibling.style.display = 'none';
        }
    }

    oldPasswordObj.addEventListener('focusin', function (e) {
        oldPasswordClass = this.getAttribute('class');
        if (oldPasswordClass == 'field') {
            this.setAttribute('class', 'field line');
        }
    });

    newPasswordObj.addEventListener('focusin', function (e) {
        newPasswordClass = this.getAttribute('class');
        if (newPasswordClass == 'field') {
            this.setAttribute('class', 'field line');
        }
    });

    againNewPasswordObj.addEventListener('focusin', function (e) {
        againNewPasswordClass = this.getAttribute('class');
        if (againNewPasswordClass == 'field') {
            this.setAttribute('class', 'field line');
        }
    });

    oldPasswordObj.addEventListener('focusout', function (e) {
        oldPasswordClass = this.getAttribute('class');
        if (oldPasswordClass == 'field line') {
            this.setAttribute('class', 'field');
        }
    });

    newPasswordObj.addEventListener('focusout', function (e) {
        newPasswordClass = this.getAttribute('class');
        if (newPasswordClass == 'field line') {
            this.setAttribute('class', 'field');
        }
    });

    againNewPasswordObj.addEventListener('focusout', function (e) {
        againNewPasswordClass = this.getAttribute('class');
        if (againNewPasswordClass == 'field line') {
            this.setAttribute('class', 'field');
        }
    });

    for (var i = 0; i < clearIconObjs.length; i++) {
        clearIconObjs[i].addEventListener('click', clearFunc);
    }

    document.addEventListener('keydown', function (e) {
        oldPassword = oldPasswordObj.value;
        newPassword = newPasswordObj.value;
        againNewPassword = againNewPasswordObj.value;
        if (e.keyCode == 13) {
            if (oldPassword.length > 0 && newPassword.length > 0 && againNewPassword.length > 0) {
                submitFunc(e);
            } else {
                e.preventDefault();
            }
        }
    });
</script>
</body>
</html>