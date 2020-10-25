<?php
// 云数据库配置
define('DB_DRIVER', 'mysql');
define('DB_HOSTNAME', '127.0.0.1');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'MyPassword');
define('DB_DATABASE', SNAME);
define('DB_PREFIX', 'si_');

define('HTTP_CLOUD', 'http://'.SNAME.'.fuwupu.com');
define('DIR_CLOUD', '/alidata/www/default/');




// HTTP
define('HTTP_SERVER', HTTP_CLOUD .'/weixin/');
define('HTTP_CATALOG', HTTP_CLOUD .'/');
define('HTTP_IMAGE', HTTP_CLOUD.'image/');

// HTTPS
define('HTTPS_SERVER', HTTP_CLOUD.'/weixin/');
define('HTTPS_IMAGE', HTTP_CLOUD.'image/');

// DIR
define('DIR_APPLICATION', DIR_CLOUD.'weixin/');
define('DIR_SYSTEM', DIR_CLOUD.'system/');
define('DIR_DATABASE', DIR_CLOUD.'system/database/');
define('DIR_LANGUAGE', DIR_CLOUD.'weixin/language/');
define('DIR_TEMPLATE', DIR_CLOUD.'weixin/view/template/');
define('DIR_CONFIG', DIR_CLOUD.'system/config/');
define('DIR_IMAGE', DIR_CLOUD.'image/');
define('DIR_CACHE', DIR_CLOUD.'system/cache/');
define('DIR_DOWNLOAD', DIR_CLOUD.'download/');
define('DIR_LOGS', DIR_CLOUD.'system/logs/');
define('DIR_CATALOG', DIR_CLOUD.'catalog/');
?>