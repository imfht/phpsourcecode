<?php
// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');
define('DIR_SECURE_FILENAME', 'index.html');
define('OPENWMSROOT',preg_replace("#[\\\\\/]install#", '', dirname(__FILE__)));
define('DIR_SECURE_CONTENT', 'deney Access!');
define('APP_DEBUG', true);  
// 引入ThinkPHP入口文件
require '../lib/run.php';
