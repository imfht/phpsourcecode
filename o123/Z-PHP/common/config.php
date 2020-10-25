<?php
return [
    'DEBUG' => [
        'level' => 3, // 输出全部debug信息
        'type' => 'auto', // debug信息格式
    ],
    'ROUTER' => [
        'mod' => 0, // 0:queryString，1：pathInfo，2：路由
        'module' => false, // 不启用模块模式
        // 'restfull' => [], // restfull模式
    ],
    'VER' => ['1.0', '1.0'], // [0]:默认版本号:没有请求版本号或找不到请求版本号对应目录的情况下使用此版本号,[1]:强制指定版本号：无视请求版本号，一律使用此版本号
    'SESSION' => [
        'name' => 'SID', // session 名
        'auto' => true, // 自动开启 session
        'redis' => false,
        'host' => '',
        'port' => '',
        'pass' => '',
    ],
    'VIEW' => [
        'php_tag' => 'php',
        'import_tag' => 'import',
        'template_tag' => 'template',
        'ext' => '.html',
        'theme' => 'default',
        'prefix' => '<{',
        'suffix' => '}>',
        'compress' => false,
    ],
    'DB' => [
        'dsn' => 'mysql:host=127.0.0.1;dbname=zphp_test;port=3306',
        'db' => 'zphp_test',
        'user' => 'zphp_test',
        'pass' => 'zphp_test',
        'charset' => 'utf8',
        'prefix' => 'z_',
    ],
];
