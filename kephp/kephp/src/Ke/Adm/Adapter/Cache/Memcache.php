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

use Exception;
use Memcache as PhpMemcache;
use Ke\Adm\Adapter\CacheAdapter;

class Memcache implements CacheAdapter
{

	protected $source = null;

	protected $prefix = '';

	protected $server = '';

	protected $configuration = [
		'prefix'            => '',
		'colon'				=> self::DEFAULT_COLON,
		'host'              => '',
		'port'              => 11211,
		'compressThreshold' => 0,
		'compressRatio'     => 0,
	];

	/** @var PhpMemcache */
	private $memcache = null;

	public function __construct(string $source, array $config = null)
	{
		$this->source = $source;
		$this->configure($config);
	}

	public function configure(array $config)
	{
		$this->configuration = array_merge($this->configuration, $config);
		if (!extension_loaded('memcache') || !class_exists(PhpMemcache::class, false))
			throw new Exception('Missing memcache extension!');
		if (empty($this->configuration['host']))
			throw new Exception("Memcache host not specified in cache source \"{$this->source}\"!");
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
		if (!isset($this->instance)) {
			$this->memcache = new PhpMemcache();
			$this->memcache->addServer($this->configuration['host'], $this->configuration['port']);
			$status = @$this->memcache->getExtendedStats();
			// unset $status[$server] or $status[$server] === false
			if (empty($status[$this->server])) {
				throw new Exception('Memcache connect failed about cache source "{0}"', [$this->source]);
			}
//			if ($this->memcache->getServerStatus($this->config['host'], $this->config['port']) === 0) {
//				if ($this->config['pconnect'])
//					$conn = @$this->memcache->pconnect($this->config['host'], $this->config['port']);
//				else
//					$conn = @$this->memcache->connect($this->config['host'], $this->config['port']);
//				if ($conn === false)
//					throw new Exception('Memcache connect failure about cache source "{0}"', [$this->name]);
//			}
			if ($this->configuration['compressThreshold'] > 0 && $this->configuration['compressRatio'] > 0)
				$this->memcache->setCompressThreshold($this->configuration['compressThreshold'], $this->configuration['compressRatio']);
		}
		return $this;
	}

	public function exists($key)
	{
		if (!isset($this->memcache))
			$this->connect();
		$key = $this->prefix . $key;
		return $this->memcache->get($key) !== false;
	}

	public function set($key, $data, $expire = 0)
	{
		if (!isset($this->memcache))
			$this->connect();
		if ($data === false)
			$data = 0;
		$key = $this->prefix . $key;
		return $this->memcache->set($key, $data, null, $expire);
	}

	public function get($key)
	{
		if (!isset($this->memcache))
			$this->connect();
		$key = $this->prefix . $key;
		return $this->memcache->get($key);
	}

	public function delete($key)
	{
		if (!isset($this->memcache))
			$this->connect();
		$key = $this->prefix . $key;
		return $this->memcache->delete($key);
	}

	public function replace($key, $data, $expire = 0)
	{
		if (!isset($this->memcache))
			$this->connect();
		$key = $this->prefix . $key;
		return $this->memcache->replace($key, $data, $expire);
	}

	public function increment($key, $value = 1)
	{
		if (!isset($this->memcache))
			$this->connect();
		$key = $this->prefix . $key;
		return $this->memcache->increment($key, $value);
	}

	public function decrement($key, $value = 1)
	{
		if (!isset($this->memcache))
			$this->connect();
		$key = $this->prefix . $key;
		return $this->memcache->decrement($key, $value);
	}

	public function flush()
	{
		if (!isset($this->memcache))
			$this->connect();
		return $this->memcache->flush();
	}
}