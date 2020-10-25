<?php

/**
 * 生产环境配置文件，
 * 环境变量MOD_ENV = PRODUCTION时，加载这个配置文件，并且与main合并
 */
return array(
    'application' => array(
        "modules" => 'Index',
        "resource" => 'Model,Redis',
        "library" => 'Alpaca',
    ),
    'db'=>array(
        'driver'    => 'mysql',
        'host'      => '127.0.0.1',
        'database'  => 'db_alpaca',
        'username'  => 'root',
        'password'  => 'password',
        'charset'   => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix'    => '',
    ),
);