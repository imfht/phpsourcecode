<?php
/*主要配置文件，任何环境都会加载这个配置文件*/
return array(
    'application' => array(
        "modules"  => 'Index',
        "resource" => 'Model,Redis',
        "library"  => 'Alpaca',

    ),
    'db'          => array(
        'driver'    => 'mysql',
        'host'      => '127.0.0.1',
        'database'  => 'db_alpaca',
        'username'  => 'root',
        'password'  => '123456',
        'charset'   => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix'    => '',
    ),
    'log'         => array(
        'levels'     => 'error',
        'categories' => '',
        'dir'        => APP_PATH.'/runtime/log/',  // 此目录可配置,在此目录下，每天一个文件夹
        'file'       => 'all.log'
    ),
);
