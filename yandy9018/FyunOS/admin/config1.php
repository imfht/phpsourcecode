<?php
// 云数据库配置
define('DB_DRIVER', 'mysql');
define('DB_HOSTNAME', '127.0.0.1');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'root');
define('DB_DATABASE', 'anleye');
define('DB_PREFIX', 's');

define('HTTP_CLOUD', 'http://127.0.0.1');
define('DIR_CLOUD', 'D:\APMServ5.2.6\www\htdocs/');



// HTTP
define('HTTP_SERVER', HTTP_CLOUD .'/admin/');
define('HTTP_CATALOG', HTTP_CLOUD .'/');
define('HTTP_IMAGE', HTTP_CLOUD.'image/');

// HTTPS
define('HTTPS_SERVER', HTTP_CLOUD.'/admin/');
define('HTTPS_IMAGE', HTTP_CLOUD.'/image/');

// DIR
define('DIR_APPLICATION', DIR_CLOUD.'admin/');
define('DIR_SYSTEM', DIR_CLOUD.'system/');
define('DIR_DATABASE', DIR_CLOUD.'system/database/');
define('DIR_LANGUAGE', DIR_CLOUD.'admin/language/');
define('DIR_TEMPLATE', DIR_CLOUD.'admin/view/template/');
define('DIR_CONFIG', DIR_CLOUD.'system/config/');
define('DIR_IMAGE', DIR_CLOUD.'image/');
define('DIR_CACHE', DIR_CLOUD.'system/cache/');
define('DIR_DOWNLOAD', DIR_CLOUD.'download/');
define('DIR_LOGS', DIR_CLOUD.'system/logs/');
define('DIR_CATALOG', DIR_CLOUD.'catalog/');
?>