<?php
/**
 * memcache 缓存
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since 2013-05 v1.0.0
 */

namespace herosphp\cache;

use herosphp\cache\interfaces\ICache;
use herosphp\exception\HeroException;

class MemoCache implements ICache {

    /**
     * Memcache 缓存实例
     * @var Memcache|null
     */
    private static $Mem = NULL;

    //所有缓存key的前缀
    const KEY_PREFIX = "CACHE_KET_PRIFIX_";

    //加载缓存配置
    public function initConfigs()
    {
        $this->configs = Loader::config('memo', 'cache');
    }


    /**
     * @var array 配置信息
     */
    private $configs = array();

    /**
     * 初始化缓存配置信息
     * @param array $configs 缓存配置信息
     * @throws HeroException
     */
    public function __construct() {

        if (empty($this->configs)) {
            $this->initConfigs();
        }

        if ( !extension_loaded("memcache") ) {
            E("please install memcache extension.");
        }
        $Mem = new \Memcache();
        foreach ( $this->configs['server'] as $value ) {
            call_user_func_array(array($Mem, 'addServer'), $value);
        }
        if ( !$Mem->getstats() ) {
            if ( APP_DEBUG ) {
                E("Unable to connect the Memcache server!");
            }
        }
        self::$Mem = $Mem;
    }

    /**
     * @see    ICache::get()
     * @param string $key
     * @return array|mixed|string
     */
	public function get( $key ) {
		return self::$Mem->get(self::KEY_PREFIX.$key);
	}

    /**
     * @see ICache::set()
     * @param string $key
     * @param string $content
     * @param null $expire
     * @return bool
     */
	public function set( $key, $content, $expire=0) {
		return self::$Mem->set(self::KEY_PREFIX.$key, $content, MEMCACHE_COMPRESSED, $expire);
	}

    /**
     * @see    ICache::delete()
     * @param string $key
     * @return bool
     */
	public function delete( $key ) {
		return self::$Mem->delete(self::KEY_PREFIX.$key, 0);
	}
}
