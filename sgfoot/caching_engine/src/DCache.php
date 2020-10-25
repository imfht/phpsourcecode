<?php

namespace SgIoc\Cache;
/**
 * memcached 快捷操作类
 * User: freelife2020@163.com
 * Date: 2018/4/15
 * Time: 15:26
 * RCache::put();
 * RCache::get();
 * RCache::forget();
 */
class DCache extends CacheManager
{
    /**
     * 默认引擎
     * @var string
     */
    public static $default_store = 'memcached';
}