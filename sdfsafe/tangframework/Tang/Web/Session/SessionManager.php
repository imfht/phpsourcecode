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
namespace Tang\Web\Session;
use Tang\Cache\CacheManager;;
use Tang\Cache\Stores\Database;
use Tang\Cache\Stores\FileStore;
use Tang\Cache\Stores\Store;
use Tang\Exception\SystemException;
use Tang\Web\Session\Stores\Cache;
use Tang\Web\Session\Stores\FileCache;

class SessionManager extends CacheManager implements ISession
{
	public function createMemcachedDriver()
	{
		$store = parent::createMemcachedDriver();
		return $this->createCacheStore($store);
	}
	public function createRedisDriver()
	{
		return $this->createCacheStore(parent::createRedisDriver());
	}
	public function createIgbinaryDriver()
	{
		return $this->createFileCacheStore(parent::createIgbinaryDriver());
	}
	public function createJsonDriver()
	{
		return $this->createFileCacheStore(parent::createJsonDriver());
	}
	public function createSerializationDriver()
	{
		return $this->createFileCacheStore(parent::createSerializationDriver());
	}
	public function createDatabaseDriver()
	{
        return $this->createCacheStore(parent::createDatabaseDriver());
	}
	public function createApcDriver()
	{
		throw new SystemException('Drivers [%s] not supported!',array('apc'));
	}
	public function createFileCacheStore(FileStore $store)
	{
		return new FileCache($store,$this->config['expire']);
	}
	public function createCacheStore(Store $store)
	{
		return new Cache($store,$this->config['expire']);
	}
	public function get($name,$defaultValue='')
	{
		return isset($_SESSION[$name]) && $_SESSION[$name] ? $_SESSION[$name]:$defaultValue;
	}
	public function set($name,$value)
	{
		$_SESSION[$name] = $value;
	}
	public function delete($name)
	{
		if(isset($_SESSION[$name]))
		{
			unset($_SESSION[$name]);
		}
	}
	public function destroy()
	{
		unset($_SESSION);
		session_destroy();
	}
	protected function getIntreface()
	{
		return '\SessionHandlerInterface';
	}
}