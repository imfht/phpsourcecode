<?php
//初始化文件
define('DEBUG', 'on');
define('DS', DIRECTORY_SEPARATOR);
define('WEBPATH', __DIR__ . DS . '..');
//载入环境配置,(开发环境：devlop,测试环境：test,生产环境：product)
if (file_exists(WEBPATH . 'server/Env.php')){
    require_once WEBPATH . 'server/Env.php';
}
if (!defined('ENV')){
    define('ENV', 'devlop');
}
//使用composer扩展
require_once WEBPATH . '/vendor/autoload.php';
//载入swoole frameworkZ框架配置
require_once WEBPATH . '/vendor/matyhtf/swoole_framework/libs/lib_config.php';
