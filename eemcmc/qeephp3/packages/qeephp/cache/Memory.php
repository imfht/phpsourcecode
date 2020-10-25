<?php namespace qeephp\cache;

use qeephp\Config;

/**
 * Memory 提供当前内存缓存
 */
class Memory implements ICache
{
    /**
     * 缓存数据
     *
     * @var array
     */
    static private $_cache = array();

    function get($key)
    {
        return isset(self::$_cache[$key]) ? self::$_cache[$key] : false;
    }

    function get_multi(array $keys)
    {
        $result = array();
        foreach ($keys as $key)
        {
            $result[$key] = isset(self::$_cache[$key]) ? self::$_cache[$key] : false;
        }
        return $result;
    }

    function set($key, $value, $ttl = null)
    {
        self::$_cache[$key] = $value;
    }

    function set_multi(array $values, $ttl = null)
    {
        foreach ($values as $key => $value)
        {
            self::$_cache[$key] = $value;
        }
    }

    function del($key)
    {
        unset(self::$_cache[$key]);
    }

    function del_multi(array $keys)
    {
        foreach ($keys as $key)
        {
            unset(self::$_cache[$key]);
        }
    }

    /**
     * 返回用于特定存储域的缓存服务对象实例
     *
     * @param string $domain
     *
     * @return ICache
     */
    static function instance($domain)
    {
        static $instance;
        if (!$instance)
        {
            $instance = new Memory();
        }
        return $instance;
    }
}