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
namespace Tang\Cache;
use Tang\Cache\Stores\Apc;
use Tang\Cache\Stores\Database;
use Tang\Cache\Stores\Igbinary;
use Tang\Cache\Stores\Json;
use Tang\Cache\Stores\Memcached;
use Tang\Cache\Stores\Redis;
use Tang\Cache\Stores\Serialization;
use Tang\Database\NoSql\Memcached as MemcachedDb;
use Tang\Database\NoSql\Redis as RedisDb;
use Tang\Database\Sql\DB;
use Tang\Manager\Manager;
use Tang\Services\FileService;

/**
 * Class CacheManager
 * @package Tang\Cache
 */
class CacheManager extends Manager
{
    public function createMemcachedDriver()
	{

		return new Memcached(MemcachedDb::get($this->config['memcachedSource'])->getMemcache());
	}
	public function createRedisDriver()
	{
		$redis = RedisDb::get($this->config['redisSource']);
		return new Redis($redis->getWriteRedis(),$redis->getReadRedis());
	}
	public function createIgbinaryDriver()
	{
		return new Igbinary(FileService::getService(),$this->config['directory']);
	}
	public function createJsonDriver()
	{
		return new Json(FileService::getService(),$this->config['directory']);
	}
	public function createSerializationDriver()
	{
		return new Serialization(FileService::getService(),$this->config['directory']);
	}
	public function createDatabaseDriver()
	{
		$config = $this->config['database'];
		return new Database(DB::get($config['source'])->table($config['tableName']));
	}
	public function createApcDriver()
	{
		return new Apc();
	}

    /**
     * @param string $name
     * @return \Tang\Cache\Stores\IStore
     */
    public function driver($name = '')
    {
        return parent::driver($name);
    }
	protected function getIntreface()
	{
		return '\Tang\Cache\Stores\IStore';
	}
}