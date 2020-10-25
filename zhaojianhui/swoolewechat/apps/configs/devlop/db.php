<?php
//TYPE_MYSQL:非永久连接；TYPE_MYSQLi:连接是永久连接;TYPE_PDO:pdo数据库链接方式；TYPE_CLMysql：swoole数据库连接
$db['master'] = [
    'type'       => Swoole\Database::TYPE_MYSQLi,
    'host'       => '172.17.0.2',
    'port'       => 3306,
    'dbms'       => 'mysql',
    'engine'     => 'InnoDb',
    'user'       => 'root',
    'passwd'     => '123456',
    'name'       => 'swooleWechat',
    'charset'    => 'utf8',
    'setname'    => true,
    'persistent' => false, //MySQL长连接
    'use_proxy'  => false,  //启动读写分离Proxy
    'slaves'     => [
        ['host' => '127.0.0.1', 'port' => '3307', 'weight' => 100],
        ['host' => '127.0.0.1', 'port' => '3308', 'weight' => 99],
        ['host' => '127.0.0.1', 'port' => '3309', 'weight' => 98],
    ],
];

$db['slave'] = [
    'type'       => Swoole\Database::TYPE_MYSQLi,
    'host'       => '172.17.0.2',
    'port'       => 3306,
    'dbms'       => 'mysql',
    'engine'     => 'InnoDb',
    'user'       => 'root',
    'passwd'     => 'root',
    'name'       => 'swooleWechat',
    'charset'    => 'utf8',
    'setname'    => true,
    'persistent' => false, //MySQL长连接
];

return $db;