<?php
// 事件定义文件

use app\listener\Log;

return [
    'bind'      => [
    ],

    'listen'    => [
        'AppInit'  => [],
        'HttpRun'  => [],
        'HttpEnd'  => [],
        'LogLevel' => [],
        'LogWrite' => [
            Log::class
        ],
    ],

    'subscribe' => [
    ],
];
