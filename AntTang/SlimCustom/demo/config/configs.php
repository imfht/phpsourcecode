<?php
/**
 * @package     configs.php
 * @author      Jing <tangjing3321@gmail.com>
 * @link        http://www.slimphp.net
 * @version     1.0
 * @copyright   Copyright (c) SlimCustom.
 * @date        2017年5月2日
 */

use \SlimCustom\Libs\App;

return [
    // set to false in production
    'displayErrorDetails' => true,
    
    // Allow the web server to send the content-length header
    'addContentLengthHeader' => false,
    
    // Renderer settings
    'renderer' => [
        'template_path' => App::publicPath() . '/views/'
    ],
    
    // Monolog settings
    'logger' => [
        'name' => App::name(),
        'path' => App::dataPath() . '/logs/' . App::name() . '_' . date('Ymd') . '.log',
        'level' => \Monolog\Logger::DEBUG
    ],
    
    // session
    'session' => [
        'driver' => 'cache',
        'lifetime' => 120,
        'expire_on_close' => false,
        'encrypt' => false,
        'files' => App::dataPath() . '/sessions/',
        'connection' => null,
        'table' => 'sessions',
        'lottery' => [
            2,
            100
        ],
        'cookie' => App::name() . '_session',
        'path' => '/',
        'domain' => 'hoge.cn',
        'secure' => false
    ],
    
    // 缓存
    'cache' => [
        'default' => 'file',
        'prefix' => App::name(),
        'stores' => [
            'file' => [
                'driver' => 'file',
                'path' => App::dataPath() . 'cache'
            ],
            'redis' => [
                'driver' => 'redis',
                'cluster' => false,
                'servers' => [
                    [
                        'host' => '127.0.0.1',
                        'port' => 6379,
                        'database' => 0,
                        'password' => ''
                    ]
                ]
            ]
        ]
    ],
    
    // 数据库
    'database' => [
        'default' => 'pdo',
        'prefix' => 'mxu_',
        // 'default' => 'mongodb',
        // 'prefix' => '',
        'connections' => [
            'pdo' => [
                'driver' => 'mysql',
                'host' => '127.0.0.1',
                'database' => 'mxu_message_collect',
                'username' => 'root',
                'password' => 'root',
                'charset' => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'strict' => false
            ],
            'mongodb' => [
                'driver' => 'mongodb',
                'host' => 'mongodb://127.0.0.1/',
                'database' => 'test',
                'uri_options' => [],
                'driver_options' => []
            ]
        ]
    ]
];
