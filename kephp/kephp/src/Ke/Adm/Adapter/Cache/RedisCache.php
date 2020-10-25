<?php
/**
 * KePHP, Keep PHP easy!
 *
 * @license   https://opensource.org/licenses/MIT
 * @copyright Copyright 2015 - 2020 KePHP Authors All Rights Reserved
 * @link      http://kephp.com ( https://git.oschina.net/kephp/kephp-core )
 * @author    曾建凯 <janpoem@163.com>
 */

namespace Ke\Adm\Adapter\Cache;


use Redis;
use Exception;
use Ke\Adm\Adapter\CacheAdapter;

class RedisCache implements CacheAdapter
{

	protected $source = null;

	protected $prefix = '';

	protected $server = '';

	protected $db = null;

	protected $configuration = [
		'prefix'      => '',
		'colon'       => self::DEFAULT_COLON,
		'host'        => '127.0.0.1',
		'port'        => 6379,
		'pconnect'    => true,
		'db'          => null,
		'transaction' => true,
		'password'    => '',
		//		Redis::SERIALIZER_NONE,
		//		Redis::SERIALIZER_IGBINARY
		'serializer'  => Redis::SERIALIZER_PHP,
		// 'server' => '127.0.0.1:11211'
		// 'server' => ['127.0.0.1', 11211]
	];

	/** @var Redis */
	private $redis = null;

	public function __construct(string $source, array $config = null)
	{
		$this->source = $source;
		$this->configure($config);
	}

	public function configure(array $config)
	{
		$this->configuration = array_merge($this->configuration, $config);
		if (!extension_loaded('redis') || !class_exists(Redis::class, false))
			throw new Exception('Missing redis extension!');
		if (empty($this->configuration['host']))
			throw new Exception("Redis host not specified in cache source \"{$this->source}\"!");
		$this->server = "{$this->configuration['host']}:{$this->configuration['port']}";
		if (!empty($this->configuration['prefix']) && is_string($this->configuration['prefix'])) {
			if (empty($this->configuration['colon']))
				$this->configuration['colon'] = self::DEFAULT_COLON;
			$this->prefix = rtrim($this->configuration['prefix'], '\\/.:_-#') . $this->configuration['colon'];
		}
		return $this;
	}

	public function getConfiguration()
	{
		return $this->configuration;
	}

	protected function connect()
	{
		if (!isset($this->redis)) {
			$this->redis = new Redis();
			if ($this->configuration['pconnect']) {
				$isConn = $this->redis->pconnect($this->configuration['host'], $this->configuration['port']);
			} else {
				$isConn = $this->redis->connect($this->configuration['host'], $this->configuration['port']);
			}
			// 这个是连接不到redis-server
			if ($isConn === false)
				throw new Exception("Cannot connect to redis-server {$this->source}!");
			if (!empty($this->configuration['password'])) {
				$isConn = $this->redis->auth($this->configuration['password']);
				if (!$isConn)
					throw new Exception('Redis-server auth fail!');
			}
			if (!empty($this->prefix))
				$this->redis->setOption(Redis::OPT_PREFIX, $this->prefix);
			if (!empty($this->configuration['serializer']))
				$this->redis->setOption(Redis::OPT_SERIALIZER, $this->configuration['serializer']);
			if (isset($this->configuration['db']) &&
				is_numeric($this->configuration['db']) &&
				$this->configuration['db'] >= 0
			) {
				$db = (int)$this->configuration['db'];
				if ($this->redis->select($db)) {
					$this->db = $db;
				}
			}
		}
		return $this;
	}

	public function exists($key)
	{
		if (!isset($this->redis))
			$this->connect();
		return $this->redis->exists($key);
	}

	public function set($key, $data, $expire = 0)
	{
		if (!isset($this->redis))
			$this->connect();
		if (!empty($this->configuration['transaction'])) {
			$return = $this->redis->multi()->set($key, $data, $expire);
			$this->redis->exec();
			return $return;
		} else {
			return $this->redis->set($key, $data, $expire);
		}
	}

	public function get($key)
	{
		if (!isset($this->redis))
			$this->connect();
		return $this->redis->get($key);
	}

	public function delete($key)
	{
		if (!isset($this->redis))
			$this->connect();
		if (!empty($this->configuration['transaction'])) {
			$return = $this->redis->multi()->del($key);
			$this->redis->exec();
			return $return;
		} else {
			return $this->redis->del($key);
		}
	}

	public function replace($key, $data, $expire = 0)
	{
		if (!isset($this->redis))
			$this->connect();
		return $this->redis->set($key, $data, $expire);
	}

	public function increment($key, $value = 1)
	{
		if (!isset($this->redis))
			$this->connect();
		return $this->redis->incrBy($key, $value);
	}

	public function decrement($key, $value = 1)
	{
		if (!isset($this->redis))
			$this->connect();
		return $this->redis->decrBy($key, $value);
	}

	public function flush()
	{
		if (!isset($this->redis))
			$this->connect();
		if (isset($this->db))
			$this->redis->flushDB();
		else
			$this->redis->flushAll();
	}
}