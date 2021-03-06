<?php
// +----------------------------------------------------------------------
// | RXThinkCMF框架 [ RXThinkCMF ]
// +----------------------------------------------------------------------
// | 版权所有 2017~2020 南京RXThinkCMF研发中心
// +----------------------------------------------------------------------
// | 官方网站: http://www.rxthink.cn
// +----------------------------------------------------------------------
// | Author: 牧羊人 <1175401194@qq.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 缓存设置
// +----------------------------------------------------------------------
$cache_type = Env::get('cache.type', '');
if ($cache_type === 'redis') {
    $cache = [
        // 驱动方式
        'type' => 'redis',
        // 服务器地址
        'host' => Env::get('cache.host', '127.0.0.1'),
        // 服务器端口号
        'port' => Env::get('cache.port', '6379'),
        // 密码
        'password' => '',
        // 超时时间（单位：毫秒）
        'timeout' => 3600,
        // 缓存数据库库号
        'select' => 1,
        // 缓存前缀
        'prefix' => Env::get('cache.prefix', 'RX_'),
        // 缓存有效期 0表示永久缓存
        'expire' => 0,
    ];
} elseif ($cache_type === 'memcache') {
    $cache = [
        // 驱动方式
        'type' => 'Memcache',
        // 服务器地址
        'host' => Env::get('cache.host', '127.0.0.1'),
        // 服务器端口号
        'port' => Env::get('cache.port', '6379'),
        // 超时时间（单位：毫秒）
        'timeout' => 3600,
        // 缓存前缀
        'prefix' => Env::get('cache.prefix', 'RX_'),
        // 缓存有效期 0表示永久缓存
        'expire' => 0,
    ];
} else {
    $cache = [
        // 驱动方式
        'type' => 'File',
        // 缓存保存目录
        'path' => '',
        // 缓存前缀
        'prefix' => '',
        // 缓存有效期 0表示永久缓存
        'expire' => 0,
    ];
}

return $cache;
