<?php
namespace test;

use WxSDK\WxApp;
use WxSDK\core\module\MassKit;
define("__DIR_", dirname(dirname(__FILE__)));

include '../Loader.php'; // 自动加载类

$app=new WxApp();

// $previewMass = MassKit::createPreviewMpnews("rBfqMHvB3ajj7Vvo5UL_kc4RBD8MmpzdQ3KXzTivfqM","oUIjzjqtAVuPY2sIT96Ouy2V8Ejs");
// $ret = MassKit::sendPreview($accessToken, $previewMass);

$previewMass = MassKit::createPreviewText("<a href='http://www.51xlxy.com'>心灵相约</a>","oUIjzjqtAVuPY2sIT96Ouy2V8Ejs");
$ret = MassKit::sendPreview($app, $previewMass);


print_r(json_encode($ret,JSON_UNESCAPED_UNICODE));
