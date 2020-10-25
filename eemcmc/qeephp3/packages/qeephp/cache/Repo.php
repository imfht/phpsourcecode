<?php namespace qeephp\cache;

use qeephp\Config;

/**
 * 缓存仓库
 */
abstract class Repo
{
    private static $_cache_instances = array();

    /**
     * 为特定存储域选择匹配的缓存服务实例
     *
     * @param string $domain
     *
     * @return qeephp\cache\ICache
     */
    static function select_cache($domain='default')
    {
        if (!isset(self::$_cache_instances[$domain]))
        {
            $class = Config::get("cache.domains.{$domain}");
            if (empty($class)) throw CacheError::not_set_domain_config_error($domain);
            self::$_cache_instances[$domain] = call_user_func_array(array($class,'instance'),array($domain));
        }
        return self::$_cache_instances[$domain];
    }

}