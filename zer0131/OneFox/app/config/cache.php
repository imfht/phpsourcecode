<?php

/**
 * @author: ryan<zer0131@vip.qq.com>
 * @desc: 缓存配置
 */

$common = [
    'type' => 'redis', //file, memcache, memcached, redis四种缓存方式
    'file' => [
        'path' => APP_PATH . DS . 'cache',
        'expire' => 0,
        'prefix' => 'onefox_',//文件前缀
    ],
    'memcache' => [
        'expire' => 0,
        'servers' => [
            [
                'host' => '127.0.0.1',
                'port' => 11211,
                'persistent' => false,
                'weight' => 10
            ],
        ]
    ],
    'redis' => [
        'expire' => 0,
        'server' => [
            'host' => '127.0.0.1',
            'port' => 6379
        ]
    ]
];

$online = [];

$dev = [];

return DEBUG ? array_merge($common, $dev) : array_merge($common, $online);
