<?php
// +-----------------------------------------------------------------------------------
// | TangFrameWork 致力于WEB快速解决方案
// +-----------------------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.tangframework.com All rights reserved.
// +-----------------------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +-----------------------------------------------------------------------------------
// | HomePage ( http://www.tangframework.com/ )
// +-----------------------------------------------------------------------------------
// | Author: wujibing<283109896@qq.com>
// +-----------------------------------------------------------------------------------
// | Version: 1.0
// +-----------------------------------------------------------------------------------
namespace Tang\Cache\Stores;
use Memcache;

/**
 * Memcached缓存
 * Class Memcached
 * @package Tang\Cache\Stores
 */
class Memcached extends Store
{
	/**
	 * @var Memcache
	 */
	protected $memcache;

    /**
     * 传入Memcache对象
     * @param Memcache $memcache
     */
    public function __construct(Memcache $memcache)
	{
		$this->memcache = $memcache;
	}

    /**
     * (non-PHPdoc)
     * @see IStore::set()
     */
	public function set($key,$value,$expire=0)
	{
		$this->memcache->add($key,$value,is_bool($value) || is_int($value) || is_float($value) ? false:MEMCACHE_COMPRESSED,$expire);
	}

    /**
     * (non-PHPdoc)
     * @see IStore::clean()
     */
	public function clean()
	{
		$this->memcache->flush();
	}

    /**
     * (non-PHPdoc)
     * @see IStore::delete()
     */
	public function delete($key)
	{
		$this->memcache->delete($key);
	}

    /**
     * (non-PHPdoc)
     * @see Store::getHandler()
     */
	protected function getHandler($key)
	{
		return $this->memcache->get($key);
	}
}