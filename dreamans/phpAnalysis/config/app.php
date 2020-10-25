<?php

return [

    'controller' => [
    
        'pre' => 'App\\Controllers\\',
    ],
    
    'router' => [
    
        'map' => [
            '/' => 'IndexController',
            '/request/list' => 'RequestListController',
            '/request/detail' => 'RequestDetailController',
        ],
        
        'callback' => function($app) { 
            return App\Libs\Routers\ControllerPathCbRouter::path($app);
        },
    ],

    'connection' => require __DIR__ . '/database.php',

    'view' => [
        'path' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'template',
    ],
];

