<?php
namespace test\card;

use WxSDK\WxApp;
use WxSDK\core\module\CardKit;

include '../Loader.php'; // 自动加载类
spl_autoload_register('Loader::autoload'); // 注册自动加载方法

$accessToken = new WxApp();

$ret = CardKit::createWhiteList($accessToken,null,array("qq978932979"));
print_r($ret);
?>