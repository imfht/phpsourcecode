<?php
return [
    'default'     => 'mysql',

    'connections' => [

        /*
        |--------------------------------------------------------------------------
        | Database
        |--------------------------------------------------------------------------
        |
        | Available Drivers: "mysql"
        |
         */

        'mysql' => [
            'driver'    => 'mysql',
            'host'      => '127.0.0.1',
            'port'      => '3306',
            'database'  => 'taskflow',
            'username'  => 'taskflow',
            'password'  => 'qzd1989',
            'charset'   => 'utf8',
            'collation' => 'utf8_general_ci',
            'prefix'    => 'taskflow_',
        ],

    ],

];
