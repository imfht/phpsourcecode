<?php

/**
 * 项目配置文件
 */

return array(
    'template' => array(
        'cpl' => 'templates_c',
        'tpl' => 'default',
        'sfx' => 'html'),
    'database' => array(
        'state' => 'off',
        'type' => 'mysql',
        'host' => '127.0.0.1',
        'user' => 'root',
        'pass' => 'root',
        'name' => 'domvc',
        'charset' => 'utf8',
        'pconnect' => true),
    'debug' => true,
    'session' => true,
    'message' => 'message.html',
    'timezone' => 'PRC',
    'router' => 1,
    'cache' => 'cache');
