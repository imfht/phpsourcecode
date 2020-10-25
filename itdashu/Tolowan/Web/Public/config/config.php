<?php
$settings = [
    'dbs' => [
        'db' => [
            "host" => "localhost",
            "username" => "root",
            "password" => "root",
            "dbname" => "tolowan",
            'adapter' => 'Mysql',
            "charset" => "utf8",
        ],
    ],
    'modelsCache' => [
        'adapters' => 'file',
        'frontendOptions' => [
            "lifetime" => 86400,
        ],
        'config' => [
            'cacheDir' => CACHE_DIR . 'data/'
        ]
    ],
    'viewCache' => [
        'adapters' => 'file',
        'frontendOptions' => [
            "lifetime" => 86400,
        ],
        'config' => [
            'cacheDir' => CACHE_DIR . 'view/'
        ]
    ],
    'flash' => [
        'error' => 'alert alert-danger',
        'success' => 'alert alert-success',
        'notice' => 'alert alert-info',
    ],
    'security' => [
        'adminIP' => [],
        'adminMac' => [],
        'adminCheckIP' => false,
        'adminCheckMac' => false,
    ],
    'cache' => [
        'type' => '',
        'config' => [],
        'time' => [
            'listHot' => 3000,
            'node' => 50000,
        ],
        'templateCache' => true,
    ],
    'translate' => [
        'switch' => false,
        'url' => 1,
    ],
    'cryptEncode' => 'sdfsdfeftrjhjhhgfdmjhjhj',
    'debug' => true,
    'adminPrefix' => 'admin',
    'multiLingual' => 2,
    'timezone' => 'Asia/Shanghai',
    'route' => true,
    'defalutLanguage' => 'cn',
    'translate' => false,
];
