<?php

namespace Library\Cache;

/**
 * Class Redis
 * @package Library\Cache
 */
class Redis {

    /**
     * 实例化的Redis对象
     * @var Redis
     */
    private static $_instance = array();

    /**
     * 实例化的Memcache对象
     *
     * @var Redis
     * @return \Redis
     */
    public static function instance($name, $env='product')
    {
        $config = \Config\Redis::getConfig($name, $env);

        if (!isset($config)) {
            throw new \Exception("Redis Config not set");
        }

        if (!isset(self::$_instance[$name])) {
            if (extension_loaded('Redis')) {
                self::$_instance[$name] = new \Redis();
            } else {
                throw new \Exception("extension Redis is not installed");
            }

            self::$_instance[$name]->connect($config['host'], $config['port']);
        }

        return self::$_instance[$name];
    }

}