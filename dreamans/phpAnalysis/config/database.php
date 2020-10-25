<?php

/**
 * MySQL连接信息
 *
 * 字符集固定为utf8
 * 请自行创建数据库和所需数据表,程序不会自动创建
 * 程序所需DDL语句请见 install.sql 文件数据库部分
 */
return [

    'connection' => [
    
        'host' => '127.0.0.1',

        'port' => 3306,

        'user' => 'root',

        'pass' => 'root',

        'db' => 'xhprof',

        'tb_prefix' => 'pa_',
    ],
];

