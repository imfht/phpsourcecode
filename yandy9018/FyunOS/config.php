<?php
// 云数据库配置
define('DB_DRIVER', 'mysql');
define('DB_HOSTNAME', '127.0.0.1');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'diancan');
define('DB_PREFIX', 'si_');

define('HTTP_CLOUD', 'http://192.168.0.100');
define('DIR_CLOUD', 'C:\wamp\www/');


// HTTP
define('HTTP_SERVER', HTTP_CLOUD ."/");
define('HTTP_IMAGE', HTTP_CLOUD.'image/');
define('HTTP_ADMIN', HTTP_CLOUD.'/admin/');

// HTTPS
define('HTTPS_SERVER', HTTP_CLOUD .'/');
define('HTTPS_IMAGE', HTTP_CLOUD.'image/');

// DIR
define('DIR_APPLICATION', DIR_CLOUD.'catalog/');
define('DIR_SYSTEM', DIR_CLOUD.'system/');
define('DIR_DATABASE', DIR_CLOUD.'system/database/');
define('DIR_LANGUAGE', DIR_CLOUD.'catalog/language/');
define('DIR_TEMPLATE', DIR_CLOUD.'catalog/view/theme/');
define('DIR_CONFIG', DIR_CLOUD.'system/config/');
define('DIR_IMAGE', DIR_CLOUD.'image/');
define('DIR_CACHE', DIR_CLOUD.'system/cache/');
define('DIR_DOWNLOAD', DIR_CLOUD.'download/');
define('DIR_LOGS', DIR_CLOUD.'system/logs/');


?>