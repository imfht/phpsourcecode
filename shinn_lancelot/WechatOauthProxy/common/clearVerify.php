<?php

if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
    $res['code'] = 0;
    $res['message'] = '移除失败！';

    session_start();
    $user = isset($_SESSION['wop_admin_user']) ? $_SESSION['wop_admin_user'] : '';
    if (empty($user)) {
        $res['code'] = -1;
        $res['message'] = '登陆过期！';
        echo json_encode($res);
        exit();
    }

    $verify = isset($_POST['verify']) ? strip_tags(trim($_POST['verify'])) : '';

    if (empty($verify)) {
        echo json_encode($res);
        exit();
    }

    $file = $_SERVER['DOCUMENT_ROOT'] . '/' . $verify;
    if (!file_exists($file)) {
        $res['message'] = '验证文件不存在!';
        echo json_encode($res);
        exit();
    }

    unlink($file);

    $res['code'] = 1;
    $res['message'] = '移除成功！';
    echo json_encode($res);
}