<?php
/**
 * Created by PhpStorm.
 * User: man0sions
 * Date: 16/8/25
 * Time: 下午6:05
 */

return [
    'charset'=>'utf-8', //字符集
    'logs'=>[
        'debug'=>BASE_DIR.'/runtime/logs/development.log',
        'warning'=>BASE_DIR.'/runtime/logs/production.log'
    ],
    'db' => [  //配置主从数据库
        'master' => [
            'host' => '192.168.10.10',
            'user' => 'mysqluser',
            'passwd' => 'mysqlpasswd',
            'dbname' => 'wxbdb',
        ],
        'slave' => [

            [
                'host' => '192.168.10.10',
                'user' => 'mysqluser',
                'passwd' => 'mysqlpasswd',
                'dbname' => 'wxbdb',
                'slave' => '1',
            ]
        ]


    ],
    'cache'=>[
        'driver'=>"\\Doctrine\\Common\\Cache\\PhpFileCache"
    ],
    'error' => [
        'code'=>404,
        'message'=>'page not found',
        'page' => BASE_DIR.'/views/error/error.php',
    ]
    ,
    'controller' => [
        'namespace' => "\\LuciferP\\TinyMvc\\controllers\\"
    ],
    'routers' => [
        BASE_DIR.'/src/routers/default.php',
        BASE_DIR.'/src/routers/users.php',
    ]

];