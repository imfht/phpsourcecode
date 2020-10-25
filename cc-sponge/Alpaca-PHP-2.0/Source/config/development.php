<?php

/**
 * 开发环境配置文件，
 * 环境变量MOD_ENV = DEVELOPMENT，或者没有设置MOD_ENV时，加载这个配置文件，并且与main合并
 */
return array(
    'db2'=>array(
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