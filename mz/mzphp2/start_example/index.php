<?php
// chdir for this directory
chdir(__DIR__);
# 在 PHP 环境变量中定义当前环境
// cli env mode
if (!isset($_SERVER['ENV'])) {
    $_SERVER['ENV'] = is_file(__DIR__ . '/conf/conf.debug') ? 'debug' : 'online';
}
if (!isset($_SERVER['ENV'])) {
    $_SERVER['ENV'] = 'online';
}
$_SERVER['ENV'] = isset($_SERVER['ENV']) ? $_SERVER['ENV'] : 'online';
// 调试模式: 0:关闭; 1:调试模式; 
define('DEBUG', 1);
// 站点根目录
define('ROOT_PATH', dirname(__FILE__) . '/');
// 框架的物理路径
define('FRAMEWORK_PATH', ROOT_PATH . '../mzphp/');
// 根据环境加载不同的配置文件
$conf = include(ROOT_PATH . 'conf/conf.' . $_SERVER['ENV'] . '.php');
//定义运行环境
$conf['env'] = $_SERVER['ENV'];

// 扩展核心目录（该目录文件会一起打包入 runtime.php 文件）
if (isset($conf['core_path'])) {
    define('FRAMEWORK_EXTEND_PATH', $conf['core_path']);
}

// 临时目录
define('FRAMEWORK_TMP_PATH', $conf['tmp_path']);

// 日志目录
define('FRAMEWORK_LOG_PATH', $conf['log_path']);

// 包含核心框架文件，转交给框架进行处理。
include FRAMEWORK_PATH . 'mzphp.php';


core::run($conf);