<?php

use WxSDK\core\module\AutoReply;
use test\WxResponse;
use WxSDK\WxApp;

include_once '../Loader.php';; // 自动加载类

$api = new AutoReply(new WxResponse(), new WxApp());
$api->start();
