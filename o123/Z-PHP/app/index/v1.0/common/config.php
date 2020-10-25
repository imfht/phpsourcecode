<?php
return [
    'DEBUG' => [
        'level' => 3, // 输出全部debug信息
        'type' => 'auto', // debug信息格式
    ],
    'ROUTER' => [
        'mod' => 0, // 0:queryString，1：pathInfo，2：路由
        'module' => false, // 不启用模块模式
        // 'restfull' => [], // restfull模式
    ],
];
