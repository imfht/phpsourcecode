<?php

use core\Config;

$config = [
    'database' => require ROOT . DS . 'config' . DS . 'db.php',
    'params' => require ROOT . DS . 'config' . DS . 'params.php',
    'meta' => require ROOT . DS . 'config' . DS . 'meta.php',
    'debug' => true,
    'router' => [
        'controller' => 'index',
        'action' => 'index'
    ],
    'modules' => require ROOT . DS . 'config' . DS . 'module.php',
    'showScriptName' => false,
    'common' => [
        'functions'
    ]
];

foreach ($config as $k => $v) {
    Config::set($k, $v);
}
