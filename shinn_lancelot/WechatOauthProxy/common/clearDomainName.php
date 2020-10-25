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

    $domainName = isset($_POST['domain_name']) ? strip_tags(trim($_POST['domain_name'])) : '';

    if (empty($domainName)) {
        echo json_encode($res);
        exit();
    }

    $domainNameArr = array();
    $file = './domainName.json';
    if (file_exists($file)) {
        $domainNameArr = json_decode(file_get_contents($file), true);
    }

    $hasDomainName = false;
    if (count($domainNameArr)) {
        foreach ($domainNameArr as $key=>$value) {
            if ($value == $domainName) {
                $hasDomainName = true;
                unset($domainNameArr[$key]);
                break;
            }
        }
    }

    if (!$hasDomainName) {
        $res['message'] = '域名不存在!';
        echo json_encode($res);
        exit();
    }

    file_put_contents($file, json_encode($domainNameArr));

    $res['code'] = 1;
    $res['message'] = '移除成功！';
    echo json_encode($res);
}