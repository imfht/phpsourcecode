<?php

defined('APP_ROOT') or die('Illeigal access'); // 禁止非法访问

return [
    'app' => [
        'site_title' => 'Demo',
        'locale_dir' => APP_ROOT . '/locales',
        'route_key' => 's',
        'url_prefix' => '/index.php',
        'asset_url' => '/assets',
        'login_url' => '/sign/in/',
        'logout_url' => '/sign/out/',
    ],
    'hosts' => [
        'a.test.alicall.com' => '192.168.1.183',
    ],
    'pdo' => [
        'class' => '\\PDO',
        'default' => [
            'dsn' => 'mysql:host=127.0.0.1;port=3306;charset=utf8',
            'username' => 'dba',
            'password' => 'dba@#',
        ],
    ],
    'mysql' => [
        'class' => '\\Cute\\ORM\\Schema\\Mysql',
        'wordpress' => [
            '@pdo' => 'default',
            'dbname' => 'db_wordpress',
            'tblpre' => 'wp_',
        ],
    ],
    'hs' => [
        'class' => '\\Cute\\ORM\\HandlerSocket',
        'default' => ['host' => '127.0.0.1', 'port' => 9999],
    ],
    'redis' => [
        'class' => '\\Cute\\Memory\\RedisExt',
        'default' => ['host' => '127.0.0.1', 'port' => 6379],
    ],
    'tpl' => [
        'class' => '\\Cute\\View\\Templater',
        'default' => [
            'source_dir' => CUTE_ROOT . '/templates',
            //'compiled_dir' => CUTE_ROOT . '/runtime/tmpl',
        ],
    ],
    'logger' => [
        'class' => '\\Cute\\Log\\FileLogger',
        'default' => ['name' => 'php', 'directory' => CUTE_ROOT . '/runtime/logs'],
        'sql' => ['name' => 'sql', 'directory' => CUTE_ROOT . '/runtime/logs'],
        'curl' => ['name' => 'curl', 'directory' => CUTE_ROOT . '/runtime/logs'],
        'push' => ['name' => 'push', 'directory' => CUTE_ROOT . '/runtime/logs'],
        'error' => ['name' => 'error', 'directory' => CUTE_ROOT . '/runtime/logs', 'ERROR'],
    ],
    'client' => [
        'class' => '\\Cute\\Network\\cURL',
        'default' => ['base_url' => '', '@logger' => 'curl'],
    ],
    'push' => [
        'class' => '\\Cute\\Network\\APNs',
        'default' => ['base_url' => '', '@logger' => 'push'],
    ],
    'captcha' => [
        'class' => '\\Cute\\Form\\Captcha',
        'default' => ['phrase' => '', 'phrase_size' => 6],
    ],
];
