<?php
/**
 * 缓存类
 * User: freelife2020@163.com
 * Date: 2018/3/16
 * Time: 17:03
 */

namespace SgIoc\Cache;

class Cache extends CacheManager
{
    /**
     * 默认引擎
     * @var string
     */
    public static $default_store = 'file';

    /**
     * 注册服务
     * @param array $config
     */
    public static function register($config)
    {
        if (isset($config['default'])) {
            self::$default_store = $config['default'];
        }
        self::registerFile($config);
        self::registerMemcache($config);
        self::registerMemcached($config);
        self::registerRedis($config);
    }

    /**
     * 注册文件缓存
     * @param $config
     * @throws \Exception
     */
    public static function registerFile($config)
    {
        $engine = 'file';
        if (!isset($config[$engine])) {
            throw new \Exception('The configuration item does not have a ' . $engine . ' node.');
        }
        $config = $config[$engine];
        CacheContainer::bind($engine, function () use ($config) {
            return new FileStore($config);
        });
    }

    /**
     * 注册memcache
     * @param $config
     * @throws \Exception
     */
    public static function registerMemcache($config)
    {
        $engine = 'memcache';
        if (!isset($config[$engine])) {
            throw new \Exception('The configuration item does not have a ' . $engine . ' node.');
        }
        $config = $config[$engine];
        CacheContainer::bind($engine, function () use ($config) {
            return new MemcacheStore(MemcacheConnector::getInstance($config), $config);
        });
    }

    /**
     * 注册memcached
     * @param $config
     * @throws \Exception
     */
    public static function registerMemcached($config)
    {
        $engine = 'memcached';
        if (!isset($config[$engine])) {
            throw new \Exception('The configuration item does not have a ' . $engine . ' node.');
        }
        $config = $config[$engine];
        CacheContainer::bind($engine, function () use ($config) {
            return new MemcachedStore(MemcachedConnector::getInstance($config), $config);
        });
    }

    /**
     * 注册redis
     * @param $config
     * @throws \Exception
     */
    public static function registerRedis($config)
    {
        $engine = 'redis';
        if (!isset($config[$engine])) {
            throw new \Exception('The configuration item does not have a ' . $engine . ' node.');
        }
        $config = $config[$engine];
        CacheContainer::bind($engine, function () use ($config) {
            return new RedisStore(RedisConnector::getInstance($config), $config);
        });
    }
}