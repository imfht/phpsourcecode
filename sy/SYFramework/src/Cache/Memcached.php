<?php
/**
 * Memcached支持类
 * 
 * @see Psr\SimpleCache\CacheInterface
 * @author ShuangYa
 * @package SYFramework
 * @category Cache
 * @link https://www.sylibs.com/
 * @copyright Copyright (c) 2015-2019 ShuangYa
 * @license https://syframework.sylibs.com/license.html
 */
namespace Sy\Cache;

use Psr\SimpleCache\CacheInterface;
use Sy\App;
use Sy\Exception\Exception;
use Sy\Exception\RequirementException;

class Memcached implements CacheInterface {
	/** @var object $connection Connection */
	private $connection;

	public function __construct() {
		if (!class_exists('Memcached', FALSE)) {
			throw new RequirementException('Class "Memcached" is required');
		}
		if (!App::$config->has('memcached')) {
			throw new Exception('No memcached config found');
		}
		$config = App::$config->get('memcached');
		$this->connection = new \Memcached;
		if (isset($config['server']) && is_array($config['server'])) {
			$this->connection->addServers($config['server']);
		} else {
			$this->connection->addServer($config['host'], $config['port']);
		}
	}

	public function getConnection() {
		return $this->connection;
	}

	public function get($key, $default = null) {
		$result = $this->connection->get($key);
		if (!is_string($result) || $this->connection->getResultCode() === \Memcached::RES_NOTSTORED) {
			return $default;
		}
		return unserialize($result);
	}

	public function set($key, $value, $ttl = 0) {
		return $this->connection->set($key, serialize($value), $ttl);
	}

	public function delete($key) {
		return $this->connection->delete($key);
	}

	public function clear() {
		$this->connection->flush();
	}

	public function getMultiple($keys, $default = null) {
		$result = $this->connection->getMulti($keys);
		foreach ($keys as $key) {
			if (isset($result[$key])) {
				$result[$key] = unserialize($result[$key]);
			} else {
				$result[$key] = $default;
			}
		}
		return $result;
	}

	public function setMultiple($values, $ttl = 0) {
		foreach ($values as $k => $v) {
			$values[$k] = serialize($v);
		}
		$this->connection->setMulti($values, $ttl);
	}

	public function deleteMultiple($keys) {
		return $this->connection->deleteMulti($keys);
	}

	public function has($key) {
		return $this->get($key) !== null;
	}
}