<?php
$cache['session'] = [
    'type'      => 'FileCache',
    'cache_dir' => WEBPATH . '/cache/filecache/',
];
$cache['master'] = [
    'type'          => 'Memcache',
    'use_memcached' => true, //使用memcached扩展
    'compress'      => true, //启用压缩
    'servers'       => [
        [
            'host'       => '172.17.0.2',
            'port'       => 11211,
            'weight'     => 100,
            'persistent' => true,
        ],
        /*array(
            'host' => '127.0.0.1',
            'port' => 11211,
            'weight' => 100,
            'persistent' => true,
        ),*/
    ],
];
return $cache;