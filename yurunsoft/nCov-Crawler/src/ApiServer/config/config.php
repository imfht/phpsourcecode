<?php

use Imi\Log\LogLevel;
return [
    'configs'    =>    [
    ],
    // bean扫描目录
    'beanScan'    =>    [
        'ImiApp\ApiServer\Controller',
        'ImiApp\Service',
        'ImiApp\Module\Api\Controller',
    ],
    'beans'    =>    [
        'HttpDispatcher'    =>    [
            'middlewares'    =>    [
                'OptionsMiddleware',
                \Imi\Server\Http\Middleware\RouteMiddleware::class,
            ],
        ],
    ],
];