<?php
namespace SgIoc\Cache;
/**
 * memcache快捷操作类
 * User: freelife2020@163.com
 * Date: 2018/3/29
 * Time: 15:26
 * MCache::put();
 * MCache::get();
 * MCache::forget();
 */
class MCache extends CacheManager
{
    /**
     * 默认引擎
     * @var string
     */
    public static $default_store = 'memcache';
}