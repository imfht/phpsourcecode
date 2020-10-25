<?php

if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
    $res['code'] = 0;
    $res['message'] = '登录失败！';

    $user = isset($_POST['user']) ? strip_tags(trim($_POST['user'])) : '';
    $password = isset($_POST['password']) ? strip_tags(trim($_POST['password'])) : '';
    if (empty($user)) {
        $res['message'] = '用户名不能为空！';
        echo json_encode($res);
        exit();
    }
    if (empty($password)) {
        $res['message'] = '密码不能为空！';
        echo json_encode($res);
        exit();
    }

    // 密码盐
    $salt = md5('shinn_lancelot');

    $userArr = array();
    $file = './user.json';
    if (file_exists($file)) {
        $userArr = json_decode(file_get_contents($file), true);
    }

    $hasUser = false;
    if (count($userArr) > 0) {
        foreach ($userArr as $key=>$value) {
            if ($value['user'] == $user) {
                $hasUser = true;
                if ($value['password'] == md5($password . $salt)) {
                    session_start();
                    $_SESSION['wop_admin_user'] = $user;

                    $res['code'] = 1;
                    $res['message'] = '登录成功！';
                } else {
                    $res['message'] = '密码错误！';
                }
                break;
            }
        }
    }
    if (!$hasUser) {
        $res['message'] = '用户名不存在!';
    }

    echo json_encode($res);
}