<?php

if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
    $res['code'] = 0;
    $res['message'] = '提交失败！';

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
                break;
            }
        }
    }

    if (!$hasDomainName) {
        array_unshift($domainNameArr, $domainName);
        file_put_contents($file, json_encode($domainNameArr));
    }

    $res['code'] = 1;
    $res['message'] = '提交成功！';
    echo json_encode($res);
}