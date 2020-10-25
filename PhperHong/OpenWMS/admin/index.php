<?php
// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

define('BUILD_DIR_SECURE',true);
define('BIND_MODULE', 'admin'); 
define('DIR_SECURE_FILENAME', 'index.html');
define('DIR_SECURE_CONTENT', 'deney Access!');
define('RUNTIME_ALLINONE', 0);
define('APP_PATH', '../src/');
define('APP_DEBUG', true);  
define('PUBLIC_CONF_PATH','../src/conf/');
define('STATIC_PATH', './');
define('DB_BACKUP', '../backup/');
// 引入ThinkPHP入口文件
require '../lib/run.php';



