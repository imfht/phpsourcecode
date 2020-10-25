<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * 初始化
 * @author 徐亚坤 hdyakun@sina.com
 */

$config = [];
$config['app'] = import(CONFIG_FOLDER . '.app', APP_PATH);
$aliases = $config['app']['aliases'];
foreach ($aliases as $class => $full_class) {
    class_alias($full_class, $class);
}

Madphp\Config::get('constants');
// 初始化请求
Madphp\Request::init();
// 初始化输出
Madphp\Response::init();
// BASE_URL
define('BASE_URL', $config['app']['base_url']);
// TIME_ZONE
date_default_timezone_set($config['app']['timezone']);

if (!is_cli()) {
    // whoops
    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
    $whoops->register();
}