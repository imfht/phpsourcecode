<?php
namespace test;

use WxSDK\core\module\KeFuKit;
use WxSDK\WxApp;

include_once('com.php');
$app=new WxApp();

// $kf = new KeFu("test@51xlxy.com","test","ceshi");
// $ret = KeFuKit::addkf($accessToken, $kf);

$ret = KeFuKit::getKfList($app);

print_r(json_encode($ret));
