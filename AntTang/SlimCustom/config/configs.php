<?php
/**
 * @package     configs.php
 * @author      Jing <tangjing3321@gmail.com>
 * @link        http://www.slimphp.net
 * @version     1.0
 * @copyright   Copyright (c) SlimCustom.
 * @date        2017年4月5日
 */

namespace SlimCustom\configs;

use SlimCustom\Libs\App;

return [
    // set to false in production
    'displayErrorDetails' => true,
    
    // Allow the web server to send the content-length header
    'addContentLengthHeader' => false,
    
    //response format
    'response' => 'json',
    
    // alias
    'alias' =>[
        \Slim\PDO\Database::class => 'database',
        \Monolog\Logger::class => 'logger',
        \Slim\Router::class => 'router',
        \Slim\Views\PhpRenderer::class => 'renderer',
        \Slim\Http\Request::class => 'request',
        \SlimCustom\Libs\Http\Response::class => 'response',
        \Slim\Handlers\Strategies\RequestResponse::class => 'foundHandler',
        \Slim\Handlers\PhpError::class => 'phpErrorHandler',
        \Slim\Handlers\Error::class => 'errorHandler',
        \Slim\Handlers\NotFound::class => 'notFoundHandler',
        \Slim\CallableResolver::class => 'callableResolver',
        \SlimCustom\Libs\Cache\Cache::class => 'cache',
    ],
    
    // Renderer settings
    'renderer' => [
        'template_path' => App::$instance->publicPath() . '/views/'
    ],
    
    // Monolog settings
    'logger' => [
        'name' => App::$instance->name(),
        'path' => App::$instance->dataPath() . '/logs/' . App::$instance->name() . '_' . date('Ymd') . '.log',
        'level' => \Monolog\Logger::DEBUG
    ],
    
    // session
    'session' => [
        'driver' => 'file',
        
        'name' => 'PHP_SESSION',
        
        'lifetime' => 120,
        
        'save_path' => App::$instance->dataPath() . '/sessions/',
        
        'cookie' => App::$instance->name() . '_session',
        
        'path' => '/',
        
        'domain' => 'hoge.cn',
        
        'secure' => false,
        
        'httponly' => false
    ],
    
    // 缓存
    'cache' => [
        'default' => 'file',
        'prefix' => App::$instance->name(),
        'stores' => [
            'apc' => [
                'driver' => 'apc'
            ],
            
            'array' => [
                'driver' => 'array'
            ],
            
            'database' => [
                'driver' => 'database',
                'table' => 'cache',
                'connection' => null
            ],
            
            'file' => [
                'driver' => 'file',
                'path' => App::$instance->dataPath() . '/caches/'
            ],
            
            'memcached' => [
                'driver' => 'memcached',
                'servers' => [
                    [
                        'host' => '127.0.0.1',
                        'port' => 11211,
                        'weight' => 100
                    ]
                ]
            ],
            
            'redis' => [
                'driver' => 'redis',
                'cluster' => false,
                'servers' => [
                    [
                        'host' => '127.0.0.1',
                        'port' => 6379,
                        'database' => 0,
                        'password' => '',
                        'timeout' => 5,
                        'persistent' => false
                    ]
                ]
            ]
        ]
    ],
    
    // 数据库
    'database' => [
        'orm' => 'PDO',
        'default' => 'mysql',
        'prefix' => '',
        'connections' => [
            'mysql' => [
                'driver' => 'mysql',
                'host' => '127.0.0.1',
                'database' => 'mxu_message_collect',
                'username' => 'root',
                'password' => 'root',
                'charset' => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'strict' => false
            ]
        ]
    ]
];
