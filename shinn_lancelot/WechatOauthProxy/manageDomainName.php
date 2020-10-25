<?php

    session_start();
    $user = isset($_SESSION['wop_admin_user']) ? $_SESSION['wop_admin_user'] : '';
    empty($user) && header('Location: ./login.php');

    $domainNameArr = array();
    $file = './common/domainName.json';
    if (file_exists($file)) {
        $domainNameArr = json_decode(file_get_contents($file), true);
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>添加接口调用安全域名</title>
    <link rel="shortcut icon" href="./asset/image/favicon.ico">
    <link rel="stylesheet" href="./asset/css/reset.css">
    <style>
        @media screen and (max-width: 640px) {
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
            .back {
                width: 4vw;
                height: 4vw;
                background: url("./asset/image/back.png") no-repeat center / contain;
                float: left;
                display: inline-block;
                vertical-align: top;
                cursor: pointer;
            }
            .list {
                margin: 8vw 2vw 0;
                max-height: calc(100vh - 6vw);
                overflow: auto;
            }
            .list > li:first-child {
                border-top: 1px solid #dddddd;
            }
            .list > li {
                width: 100%;
                height: 10vw;
                line-height: 10vw;
                font-size: 5vw;
                box-sizing: border-box;
                padding-right: 10vw;
                position: relative;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                color: #6b6b6b;
                border-bottom: 1px solid #dddddd;
            }
            .list .no-domain-name {
                text-align: center;
                padding: 0;
                font-size: 5vw;
            }
        }

        @media screen and (min-width: 640px) {
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
            .back {
                width: calc(640px * 0.04);
                height: calc(640px * 0.04);
                background: url("./asset/image/back.png") no-repeat center / contain;
                float: left;
                display: inline-block;
                vertical-align: top;
                cursor: pointer;
            }
            .list {
                margin: calc(640px * 0.08) 2vw 0;
                max-height: calc(100vh - 640px * 0.06);
                overflow: auto;
            }
            .list > li:first-child {
                border-top: 1px solid #dddddd;
            }
            .list > li {
                width: 100%;
                height: calc(640px * 0.1);
                line-height: calc(640px * 0.1);
                font-size: calc(640px * 0.05);
                box-sizing: border-box;
                padding-right: calc(640px * 0.1);
                position: relative;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                color: #6b6b6b;
                border-bottom: 1px solid #dddddd;
            }
            .list .no-domain-name {
                text-align: center;
                padding: 0;
                font-size: calc(640px * 0.05);
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
        <div class="user-box">
            <a href="./safeDomainName.php"><div class="back" title="返回"></div></a>
            <p class="user user-size"><?php echo $user; ?></p>
            <a href="./modifyPassword.php">
                <p class="modify-password-btn modify-password-size">修改密码</p>
            </a>
            <p class="logout-btn logout-size">退出登录</p>
        </div>
        <div class="box">
            <ul class="list">
                <?php
                    if (count($domainNameArr) > 0) {
                        foreach ($domainNameArr as $key=>$value) {
                            echo '<li>
                                 ' . $value .'
                                    <i class="icon icon-clear" data-value="' . $value . '" title="移除安全域名"></i>
                            </li>';
                        }
                    } else {
                        echo '<li class="no-domain-name">暂无安全域名</li>';
                    }
                ?>
            </ul>
        </div>
    </div>
</div>

<script>
    var xhr = '',
        responseObj = '',
        clearIconObjs = document.getElementsByClassName('icon-clear'),
        loginoutBtnObj = document.getElementsByClassName('logout-btn')[0],
        logoutState = 1,
        clearState = 1;

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
        confirm('你确定要退出吗？') && logout(e);
    });

    var clearFunc = function (e, obj) {
        e.preventDefault();
        if (clearState != 1) {
            return;
        }
        clearState = 0;
        var value = obj.getAttribute('data-value');
        xhr.open('post', './common/clearDomainName.php', true);
        xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');
        xhr.send('domain_name=' + value);
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4) {
                responseObj = JSON.parse(xhr.response);
                alert(responseObj.message);
                responseObj.code == 1 && obj.parentElement.parentElement.removeChild(obj.parentElement);
                responseObj.code == -1 && setTimeout(function() {
                    window.location.href = './login.php';
                }, 500);
                if (responseObj.code != -1) {
                    clearState = 1;
                }
            } else {
                console.log(xhr.readyState);
            }
        }
    }

    for (var i = 0; i < clearIconObjs.length; i++) {
        clearIconObjs[i].addEventListener('click', function(e) {
            confirm('你确定要移除吗？') && clearFunc(e, this);
        });
    }
</script>
</body>
</html>