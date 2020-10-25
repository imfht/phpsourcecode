<?php

$config = [
    /* mysql */
    'mysql' => [
        'charset' => 'UTF8',
        'persistent' => false,
        'collation' => 'utf8_unicode_ci',
        'timeout' => 3000,
    ],
    /* mysql */

    /* database */
    'database' => [

        'blog' => [
            'dbname' => 'blog',
            'write' => [
                'username' => 'root',
                'password' => 'root',
                'servers' => [
                    '127.0.0.1:3306',
                ],
            ],
            'read' => [
                'username' => 'root',
                'password' => 'root',
                'servers' => [
                    '127.0.0.1:3306',
                ],
            ],
        ],

        'rbac' => [
            'dbname' => 'rbac',
            'write' => [
                'username' => 'root',
                'password' => 'root',
                'servers' => [
                    '127.0.0.1:3306',
                ],
            ],
            'read' => [
                'username' => 'root',
                'password' => 'root',
                'servers' => [
                    '127.0.0.1:3306',
                ],
            ],
        ],

    ],
    /* database */
];

return $config;