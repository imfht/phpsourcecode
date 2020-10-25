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
use Redis as RedisClient;

/**
 * Redis缓存
 * Class Redis
 * @package Tang\Cache\Stores
 */
class Redis extends Store
{
    /**
     * 写redis
     * @var \Redis
     */
    protected $writeRedis;
    /**
     * 读redis
     * @var \Redis
     */
    protected $readRedis;

    /**
     *
     * @param \Redis $writeRedis
     * @param \Redis $readRedis
     */
    public function __construct(RedisClient $writeRedis,RedisClient $readRedis)
	{
		$this->readRedis = $readRedis;
		$this->writeRedis = $writeRedis;
	}

    /**
     * (non-PHPdoc)
     * @see IStore::set()
     */
	public function set($key,$value,$expire = 0)
	{
		$value = is_numeric($value)?$value:json_encode($value);
		if($expire > 0)
		{
			$this->writeRedis->setex($key,$expire,$value);
		} else
		{
			$this->writeRedis->set($key,$value);
		}
	}

    /**
     * (non-PHPdoc)
     * @see IStore::clean()
     */
	public function clean()
	{
		$this->writeRedis->flushDB();
	}

    /**
     * (non-PHPdoc)
     * @see IStore::delete()
     */
	public function delete($key)
	{
		$this->writeRedis->delete($key);
	}

    /**
     * (non-PHPdoc)
     * @see Store::getHandler()
     */
	protected function getHandler($key)
	{
		$value = $this->readRedis->get($key);
		if(!is_null($value))
		{
			return is_numeric($value) ? $value:json_decode($value,true);
		}
	}
}
