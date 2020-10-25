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
    <title>添加微信公众号授权登录域名验证文件内容</title>
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
            .logo {
                margin: 0 auto 10vw;
                width: 30vw;
                height: 30vw;
                background: url("./asset/image/oauth_proxy.png") no-repeat center / contain;
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
            .logo {
                margin: 0 auto calc(640px * 0.1);
                width: calc(640px * 0.3);
                height: calc(640px * 0.3);
                background: url("./asset/image/oauth_proxy.png") no-repeat center / contain;
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

        #txt {
            background: transparent;
            border-bottom: 1px solid #eee;
        }

        #submit_btn, #copy, .manage-verify {
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

        #submit_btn p, #copy p {
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
            <a href="./modifyPassword.php">
                <p class="modify-password-btn modify-password-size">修改密码</p>
            </a>
            <p class="logout-btn logout-size">退出登录</p>
        </div>
        <div class="box">
            <div class="logo"></div>
            <form id="form">
                <div class="input-box">
                    <input type="text" class="field" name="txt" id="txt" value="" autocomplete="off" placeholder="请填写微信授权回调域名验证文件内容">
                    <i class="icon icon-clear" title="移除"></i>
                </div>
                <div class="field disable" id="submit_btn">
                    <p>提交</p>
                </div>
                <a href="./manageVerify.php">
                    <div class="field manage-verify">
                        <p>管理微信授权回调域名验证文件</p>
                    </div>
                </a>
                <div class="field" id="copy">
                    <p>复制微信授权回调域名</p>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="./asset/js/clipboard.min.js"></script>
<script>
    var submitBtnObj = document.getElementById('submit_btn'),
        txtObj = document.getElementById('txt'),
        xhr = '',
        txt = '',
        responseObj = '',
        submitState = 1,
        submitBtnClass = '',
        txtClass = '',
        clearIconObj = document.getElementsByClassName('icon-clear')[0],
        redirectUrl = window.location.host,
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

        txt = txtObj.value;
        if (txt == '') {
            alert('请填写验证文件txt中的内容！');
            return;
        }

        xhr.open('post', './common/verifyHandle.php', true);
        xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');
        xhr.send('txt=' + txt);
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4) {
                responseObj = JSON.parse(xhr.response);
                alert(responseObj.message);
                responseObj.code == 1 && clearFunc();
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
        txtObj.value = '';
        txt = '';
        submitBtnClass = submitBtnObj.getAttribute('class');
        if (submitBtnClass == 'field') {
            submitBtnObj.setAttribute('class', 'field disable');
        }
        clearIconObj.style.display = 'none';
        submitBtnObj.removeEventListener('click', submitFunc);
    }

    loginoutBtnObj.addEventListener('click', function(e) {
        confirm('您确定要退出吗？') && logout(e);
    });

    txtObj.addEventListener('input', function (e) {
        submitBtnObj.removeEventListener('click', submitFunc);
        txt = txtObj.value;
        submitBtnClass = submitBtnObj.getAttribute('class');
        if (txt.length > 0) {
            if (submitBtnClass == 'field disable') {
                submitBtnObj.setAttribute('class', 'field');
            }
            clearIconObj.style.display = 'block';
            submitBtnObj.addEventListener('click', submitFunc);
        } else {
            if (submitBtnClass == 'field') {
                submitBtnObj.setAttribute('class', 'field disable');
            }
            clearIconObj.style.display = 'none';
            submitBtnObj.removeEventListener('click', submitFunc);
        }
    });

    txtObj.addEventListener('focusin', function (e) {
        txtClass = txtObj.getAttribute('class');
        if (txtClass == 'field') {
            txtObj.setAttribute('class', 'field line');
        }
    });

    txtObj.addEventListener('focusout', function (e) {
        txtClass = txtObj.getAttribute('class');
        if (txtClass == 'field line') {
            txtObj.setAttribute('class', 'field');
        }
    });
    
    clearIconObj.addEventListener('click', clearFunc);

    document.addEventListener('keydown', function (e) {
        txt = txtObj.value;
        if (e.keyCode == 13) {
            if (txt.length > 0) {
                submitFunc(e);
            } else {
                e.preventDefault();
            }
        }
    });

    var copy = new ClipboardJS('#copy', {
        text: function() {
            return redirectUrl;
        }
    });
    copy.on("success",function(e){
        alert('复制成功！');
        console.log(e.text);
        e.clearSelection();
    });
    copy.on("error",function(e){
        alert('复制失败！');
        console.log(e.action);
    });
</script>
</body>
</html>