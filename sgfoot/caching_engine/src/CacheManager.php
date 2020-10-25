<?php

namespace SgIoc\Cache;
/**
 * 缓存服务管理
 * User: freelife2020@163.com
 * Date: 2018/3/16
 * Time: 12:50
 */
class CacheManager
{
    protected static $default_store = 'file';

    public static function __callStatic($name, $args)
    {
        $app = static::resolveFacadeInstance();
        return call_user_func_array(array($app, $name), $args);
    }

    /**
     * 获取实例
     * @param null $store
     * @return mixed
     */
    public static function link($store = null)
    {
        $store = is_null($store) ? static::$default_store : $store;
        $link  = self::store($store);
        return $link->getInstance();
    }

    /**
     * 选择不同引擎,默认file
     * @param $store
     * @return mixed
     */
    public static function store($store)
    {
        return static::resolveFacadeInstance($store);
    }

    /**
     * 实现化引擎
     * @param null $store
     * @return mixed
     */
    protected static function resolveFacadeInstance($store = null)
    {
        $store = $store == null ? static::$default_store : $store;
        return CacheContainer::make($store);
    }
}