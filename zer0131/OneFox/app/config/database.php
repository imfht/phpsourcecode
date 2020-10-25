<?php

/**
 * @author: ryan<zer0131@vip.qq.com>
 * @desc: 数据库配置文件
 */

$online = [
    'default' => [
        'host' => '127.0.0.1',
        'user' => 'root',
        'port' => 3306,
        'password' => '',
        'dbname' => 'test'
    ]
];

$dev = [
    'default' => [
        'host' => 'localhost',
        'user' => 'root',
        'port' => 3306,
        'password' => '',
        'dbname' => 'test'
    ],
    'test' => [
        'host' => 'localhost',
        'user' => 'root',
        'port' => 3306,
        'password' => '',
        'dbname' => 'test'
    ]
];

return DEBUG ? $dev : $online;