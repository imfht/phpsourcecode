<?php
use WxSDK\WxApp;
use WxSDK\core\module\smart\TranslateKit;
use WxSDK\core\utils\Tool;
include_once '../Loader.php';
$app = new WxApp();

$ret = TranslateKit::translate($app, "我爱你，中国");
$r = $ret->data['to_content'];
print_r($ret);
exit(Tool::unicodeDecode($r));

// exit(iconv('utf-8', 'gbk', $ret->data['to_content']));