<?php
/**
 * 项目配置文件
 * Created by PhpStorm.
 * User: zsw
 * Date: 2018/3/26
 * Time: 11:24
 */

return [
    'database' => [
        'driver' => 'pdo_mysql',
        'user' => 'root',
        'password' => 'test',
        'host' => '127.0.0.1',
        'dbname' => 'test',
        'charset' => 'utf8',
        'driverOptions' => array(1002 => 'SET NAMES utf8')
    ],
];