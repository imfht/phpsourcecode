<?php

/*
 * Copyright (C) xiuno.com
 */

// 调试模式: 1 打开，0 关闭
define('DEBUG', 0);
define('BBS_PATH', str_replace('\\', '/', substr(__FILE__, 0, -15)));
define('FRAMEWORK_PATH', BBS_PATH.'xiunophp/');

// 加载应用的配置文件
$bbsconf = include BBS_PATH.'conf/conf.php';
$adminconf = include BBS_PATH.'admin/conf/conf.php';
$conf = array_merge($bbsconf, $adminconf);

// 临时目录
define('FRAMEWORK_TMP_PATH', $conf['tmp_path']);

// 日志目录
define('FRAMEWORK_LOG_PATH', $conf['log_path']);
		
include FRAMEWORK_PATH.'core.php';

core::init($conf);
core::ob_start();
core::run($conf);
// 完毕

?>