<?php

if(!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * 系统配置
 * @author 徐亚坤 hdyakun@sina.com
 */

return array(
    // 应用URL
    'base_url' => 'http://localhost/project/rocket/',
    // 时区设置
    'timezone' => 'Asia/Shanghai',

    'charset' => 'UTF-8',

    'aliases' => array(
        'Format' => '\Madphp\Support\Format',

        'DB' => '\Madphp\Db',
        'Cache' => '\Madphp\Cache',
        'View' => '\Madphp\View',
        'Route' => '\Madphp\Route',
        'Request' => '\Madphp\Request',
        'Response' => '\Madphp\Response',
        'Cookie' => '\Madphp\Cookie',
        'Input' => '\Madphp\Input',
        'Output' => '\Madphp\Output',

        'Config' => '\Madphp\Config',
        'Event' => '\Madphp\Event',
        'Log' => '\Madphp\Log',

        'Html' => '\Madphp\Html',
    ),
);