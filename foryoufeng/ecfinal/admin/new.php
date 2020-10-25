<?php
// 定义应用目录
define('ROOT_PATH',dirname(dirname(__FILE__)).'/');
define('LIB',dirname(__FILE__).'/common/');
define('Controllers',      dirname(LIB).'/controllers/'); // 控制器类
define('Models',      dirname(LIB).'/models/'); // 控制器类
define('DEBUG',false); // 调试
//define('DEBUG',true); // 调试
include_once LIB.'AdminCore.php';
?>