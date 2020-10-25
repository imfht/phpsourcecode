<?php

define("IN_CART", true);
define("SITEPATH", dirname(__FILE__));
require_once SITEPATH . "/init.php";

$action = isset($_GET['action']) ? strtolower(trim($_GET["action"])) : "";
if (!in_array($action, array("seccode", "send", "district", "zip"))) {
    die();
}
switch ($action) {
    case 'seccode'://验证码
        Image::ImageVerify();
        break;
    case 'send': //发送
        $type = trim($_GET["type"]);
        if (!in_array($type, array("email", "sms")))
            exit();
        csetcookie("send" . $type, '1', 300);

        //判断发送条件
        $lockfile = DATADIR . "/lock/" . "send{$type}.lock";
        @$filemtime = filemtime($lockfile);
        if (time() - $filemtime < 5)
            exit(); // 5s内
        touch($lockfile);

        set_time_limit(0);
        $send = new Send($type);
        $send->dosend();
        break;
    case 'district':
        $pid = intval($_GET["pid"]);
        $districts = Dis::getDistrict($pid);
        exit($districts ? json_encode($districts) : "failure");
        break;
    case 'zip':
        $districtid = intval($_GET["districtid"]);
        $zip = Dis::getZip($districtid);
        exit($zip);
        break;
}



