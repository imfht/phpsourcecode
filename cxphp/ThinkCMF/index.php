<?php

/* 开启调试模式 */
define("APP_DEBUG", TRUE);

/* 项目名称，不可更改 */
define('APP_NAME', 'ThinkCMF');

/* 定义应该根目录 */
define('CMF_ROOT', getcwd());

/* 数据写入目录 */
define('CMF_DATA', CMF_ROOT . '/static/data');

/* 项目路径，不可更改 */
define('APP_PATH', CMF_ROOT . '/' . APP_NAME . '/');


/* 定义缓存存放路径 */
define("RUNTIME_PATH", CMF_DATA . '/.runtime/');

/* 检测系统是否需要安装 */
if (!file_exists("./static/data/install.lock")) {
	$_GET['m'] = 'install';
}

/* 版本号 */
define("CMF_VERSION", 'Extend 1.0');

//载入框架核心文件
require './ThinkPHP/ThinkPHP.php';
