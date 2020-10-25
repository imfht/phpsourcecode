<?php
/**
 * Redis
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

class Redis implements CacheInterface {
	/** @var object $connection Connection */
	private $connection;

	public function __construct() {
		if (!class_exists('Redis', FALSE)) {
			throw new RequirementException('Class "Redis" is required');
		}
		if (!App::$config->has('redis')) {
			throw new Exception('No redis config found');
		}
		$config = App::$config->get('redis');
		$this->connection = new \Redis;
		$this->connection->connect($config['host'], $config['port']);
		if (!empty($config['password'])) {
			$this->connection->auth($config['password']);
		}
		if (isset($config['index'])) {
			$this->connection->select($config['index']);
		}
	}

	public function getConnection() {
		return $this->connection;
	}

	public function get($key, $default = null) {
		$result = $this->connection->get($key);
		return ($result === false || $result === null) ? $default : unserialize($result);
	}

	public function set($key, $value, $ttl = null) {
		if ($ttl !== null) {
			return $this->connection->setEx($key, $ttl, serialize($value));
		} else {
			return $this->connection->set($key, serialize($value));
		}
	}

	public function delete($key) {
		return $this->connection->del($key);
	}

	public function clear() {
		$this->connection->flushDb();
	}

	public function getMultiple($keys, $default = null) {
		$result = $this->connection->mGet($keys);
		foreach ($result as $k => $v) {
			if ($v === false || $v === null) {
				$result[$k] = $default;
			} else {
				$result[$k] = unserialize($v);
			}
		}
		return array_combine($keys, $result);
	}

	public function setMultiple($values, $ttl = null) {
		foreach ($values as $k => $v) {
			$values[$k] = serialize($v);
		}
		$this->connection->mSet($values);
		if ($ttl !== null) {
			foreach ($values as $k => $v) {
				$this->connection->expire($k, $ttl);
			}
		}
	}

	public function deleteMultiple($keys) {
		return $this->connection->del($keys);
	}

	public function has($key) {
		return $this->connection->exists($key);
	}
}