<?php
$_SERVER['ENV'] = isset($_SERVER['ENV']) ? $_SERVER['ENV'] : 'debug';
// 调试模式: 0:关闭; 1:调试模式; 参数开启调试, URL中带上：zhangcheng_debug
// 线上请务必将此参数修改复杂不可猜出
define('DEBUG', ((isset($argc) && $argc) || strstr($_SERVER['REQUEST_URI'], '_debug')) ? 1:0);
// 站点根目录
define('ROOT_PATH', dirname(__FILE__).'/');
// 框架的物理路径
define('FRAMEWORK_PATH', ROOT_PATH.'mzphp/');

$conf = include(ROOT_PATH.'conf/conf.'.$_SERVER['ENV'].'.php');
//定义运行环境
$conf['env'] = $_SERVER['ENV'];

// 扩展核心目录（该目录文件会一起打包入 runtime.php 文件）
if(isset($conf['core_path'])){
    define('FRAMEWORK_EXTEND_PATH', $conf['core_path']);
}

// 临时目录
define('FRAMEWORK_TMP_PATH', $conf['tmp_path']);

// 日志目录
define('FRAMEWORK_LOG_PATH', $conf['log_path']);

// 包含核心框架文件，转交给框架进行处理。
include FRAMEWORK_PATH.'mzphp.php';

core::run($conf);

?>