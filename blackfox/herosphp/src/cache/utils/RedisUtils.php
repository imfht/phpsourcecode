<?php

namespace herosphp\cache\utils;

/**
 * redis操作工具类
 * ----------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since 2013-05 v1.0.0
 */

use herosphp\core\Loader;

class RedisUtils {

    //redis实例
    private static $redis = null;

    private function __construct(){}

    /**
     * @return bool|null|\Redis
     */
    public static function getInstance() {

        if ( is_null(self::$redis) ) {
            $configs = Loader::config('redis', 'cache');
            if ( !$configs ) return false;
            self::$redis = new \Redis();
            self::$redis->connect($configs['host'], $configs['port']);
        }
        return self::$redis;
    }

} 