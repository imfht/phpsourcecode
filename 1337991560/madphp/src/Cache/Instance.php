<?php

/**
 * Cache Instance
 * 获取缓存实例
 * @author 徐亚坤 hdyakun@sina.com
 */

namespace Madphp\Cache;

class Instance
{
    /**
     * 存储缓存实例的数组
     * @var array
     */
    public static $instances = array();

    /**
     * 获取缓存实例
     * @param string $storage
     * @param array $config
     * @return mixed
     * @throws \Exception
     */
    public static function get($storage = "auto", $config = array())
    {
        $storage = strtolower($storage);

        if (empty($config)) {
            $config = Util::$config;
        } else {
            $config = array_merge(Util::$config, $config);
        }
        if ($storage == "" || $storage == "auto") {
            $storage = self::getAutoClass($config);
        }

        $instance = md5(json_encode($config) . $storage);
        if (!isset(self::$instances[$instance])) {
            $class = __NAMESPACE__ . "\\Drivers\\" . ucfirst(strtolower($storage));
            if (class_exists($class)) {
                self::$instances[$instance] = new $class($config);
            } else {
                throw new \Exception("$class not found!");
            }
        }

        return self::$instances[$instance];
    }

    /**
     * 自动获取缓存类名
     * @param $config
     * @return string
     */
    public static function getAutoClass($config)
    {
        $driver = "";
        $path = Util::getPath(false, $config);

        if (is_writeable($path)) {
            $driver = "file";
        } elseif (extension_loaded('pdo_sqlite') && is_writeable($path)) {
            $driver = "sqlite";
        } elseif (extension_loaded('apc') && ini_get('apc.enabled') && strpos(PHP_SAPI, "CGI") === false) {
            $driver = "apc";
        } elseif (class_exists("\\Memcached")) {
            $driver = "memcached";
        } elseif (extension_loaded('wincache') && function_exists("wincache_ucache_set")) {
            $driver = "wincache";
        } elseif (extension_loaded('xcache') && function_exists("xcache_get")) {
            $driver = "xcache";
        } elseif (function_exists("\\memcache_connect")) {
            $driver = "memcache";
        } elseif (class_exists("\\Redis")) {
            $driver = "redis";
        } else {
            $driver = "file";
        }

        return $driver;
    }
}