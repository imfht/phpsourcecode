<?php

/**
 * =============================================================================
 *  [YTF] (C)2015-2099 Yuantuan Inc.
 *  This content is released under the Apache License, Version 2.0 (the "License");
 *  Licensed    http://www.apache.org/licenses/LICENSE-2.0
 *  Link        http://www.ytframework.cn
 * =============================================================================
 *  @author     Tangqian<tanufo@126.com> 
 *  @version    $Id: index.php 93 2016-04-22 08:23:41Z lixiaohui $
 *  @created    2015-10-10
 *  单入口
 * =============================================================================                   
 */
//当前框架版本号及版本时间
define('YTF_VERSION', '1.0');
define('YTF_VERSION_DATE', '2016.05.27');
//框架默认utf8
header("Content-type: text/html; charset=utf-8");
//时区
date_default_timezone_set('Asia/Shanghai');
//常用的常量处理
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(dirname(__FILE__)));
define('HTTP_HOST', (isset($_SERVER['HTTP_HOST']) ? htmlspecialchars($_SERVER['HTTP_HOST']) : ''));
//是否开启SESSION //自动启动
define('SESSION_AUTO_START', true);
//autoload
require_once ROOT . DS . 'core' . DS . 'Autoloader.class.php';
spl_autoload_register('\\core\\Autoloader::init');

//SSL判断
if (!core\Tool::isSSL()) {
    define('HTTPS_OPEN', 'http://');
} else {
    define('HTTPS_OPEN', 'https://');
}
define('SITE_URL', HTTPS_OPEN . HTTP_HOST);

//全局配置文件

require_once ROOT . DS . 'config' . DS . 'config.php';

error_reporting(0);
//引入全局文件
foreach ($config['common'] as $v) {
    if (is_file($file_common = ROOT . DS . 'common' . DS . $v . '.php')) {
        require_once $file_common;
    }
}

//debug
register_shutdown_function('\\core\\Debug::systemErrorHandler');
set_error_handler('\\core\\Debug::errorHandler');

//run
core\App::run($_SERVER['REQUEST_URI']);


