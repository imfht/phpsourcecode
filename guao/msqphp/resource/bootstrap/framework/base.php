<?php declare(strict_types = 1);

//开始时间,内存状况
define('PHP_START_TIME', microtime(true));
define('PHP_START_MEM' , memory_get_usage());

//根目录
$root_path = realpath(__DIR__.'/../../') . DIRECTORY_SEPARATOR;
//框架目录
$framework_path = $root_path . 'vendor/msqphp/framework' . DIRECTORY_SEPARATOR;

//加载环境类
require $framework_path . 'Environment.php';

//环境初始化
msqphp\Environment::setPath([
    'root'        => $root_path,
    'application' => $root_path . 'application',
    'bootstrap'   => $root_path . 'bootstrap',
    'config'      => $root_path . 'config/user',
    'library'     => $root_path . 'library/msqphp/framework',
    'public'      => $root_path . 'public',
    'resources'   => $root_path . 'resources',
    'storage'     => $root_path . 'storage',
    'test'        => $root_path . 'test',
    'framework'   => $framework_path
]);