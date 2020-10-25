<?php
// 定义应用目录
defined('WWW') or define('WWW','localhost');
define('LIB',dirname(__FILE__).'/includes/common/');
define('Controllers',      dirname(LIB).'/controllers/frontend/'); // 控制器类
define('Models',      dirname(LIB).'/models/'); // 控制器类
define('DEBUG',false); // 调试
define('DEBUG_MODE',2); // 不缓存
include_once LIB.'FrontendCore.php';
?>