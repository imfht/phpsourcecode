<?php

if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
    $res['code'] = 0;
    $res['message'] = '修改失败！';

    session_start();
    $user = isset($_SESSION['wop_admin_user']) ? $_SESSION['wop_admin_user'] : '';
    if (empty($user)) {
        $res['code'] = -1;
        $res['message'] = '登陆过期！';
        echo json_encode($res);
        exit();
    }

    $user = isset($_POST['user']) ? $_POST['user'] : '';
    $oldPassword = isset($_POST['old_password']) ? strip_tags(trim($_POST['old_password'])) : '';
    $newPassword = isset($_POST['new_password']) ? strip_tags(trim($_POST['new_password'])) : '';
    $againNewPassword = isset($_POST['again_new_password']) ? strip_tags(trim($_POST['again_new_password'])) : '';
    if (empty($oldPassword)) {
        $res['message'] = '旧密码不能为空！';
        echo json_encode($res);
        exit();
    }
    if (empty($newPassword)) {
        $res['message'] = '新密码不能为空！';
        echo json_encode($res);
        exit();
    }
    if (empty($againNewPassword)) {
        $res['message'] = '确认新密码不能为空！';
        echo json_encode($res);
        exit();
    }
    if ($newPassword != $againNewPassword) {
        $res['message'] = '两次输入的新密码不一致！请重新填写！';
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
                if ($value['password'] == md5($oldPassword . $salt)) {
                    // 更新为新密码
                    $userArr[$key]['password'] = md5($newPassword . $salt);
                    // 写入文件
                    file_put_contents($file, json_encode($userArr));

                    $res['code'] = 1;
                    $res['message'] = '修改成功！';
                } else {
                    $res['message'] = '旧密码错误！';
                }
                break;
            }
        }
    }

    if (!$hasUser) {
        $res['message'] = '该用户不存在！';
    }

    echo json_encode($res);
}