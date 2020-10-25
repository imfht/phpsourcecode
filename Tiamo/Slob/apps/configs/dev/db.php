<?php
$db['master'] = array(
    'type'       => Swoole\Database::TYPE_MYSQLi,
    'host'       => "127.0.0.1",
    'port'       => 3306,
    'dbms'       => 'mysql',
    'engine'     => 'MyISAM',
    'user'       => "root",
    'passwd'     => "",
    'name'       => "ws",
    'charset'    => "utf8",
    'setname'    => true,
    'persistent' => false, //MySQL长连接
);

$db['status_center'] = array(
    'type'       => Swoole\Database::TYPE_MYSQLi,
    'host'       => "127.0.0.1",
    'port'       => 3306,
    'dbms'       => 'mysql',
    'engine'     => 'MyISAM',
    'user'       => "root",
    'passwd'     => "",
    'name'       => "status_center",
    'charset'    => "utf8",
    'setname'    => true,
    'persistent' => false, //MySQL长连接
);

return $db;