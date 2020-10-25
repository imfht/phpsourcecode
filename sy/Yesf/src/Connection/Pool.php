<?php
/**
 * 连接池管理类
 * 
 * @author ShuangYa
 * @package Yesf
 * @category Library
 * @link https://www.sylingd.com/
 * @copyright Copyright (c) 2017-2019 ShuangYa
 * @license https://yesf.sylibs.com/license.html
 */
namespace Yesf\Connection;

use Yesf\Yesf;
use Yesf\Swoole;
use Yesf\Exception\ConnectionException;
use Yesf\Exception\InvalidClassException;

class Pool {
	protected static $connection_default;
	protected static $connection_task;
	protected static $driver = [];
	protected static $created_driver = [];
	protected static $adapter = [];
	protected static $created_adapter = [];
	/**
	 * init阶段，读取基本配置
	 * 
	 * @access public
	 */
	public static function init() {
		$c = Yesf::app()->getConfig('connection.default', Yesf::CONF_ENV, [
			'min' => 1,
			'max' => 3,
		]);
		self::$connection_default = [
			'min' => isset($c['min']) ? intval($c['min']) : 1,
			'max' => isset($c['max']) ? intval($c['max']) : 1,
		];
		$task = Yesf::app()->getConfig('connection.task', Yesf::CONF_ENV, [
			'min' => 0,
			'max' => 1,
		]);
		self::$connection_task = [
			'min' => isset($task['min']) ? intval($task['min']) : 0,
			'max' => isset($task['max']) ? intval($task['max']) : 1,
		];
		// 注册默认Driver和Adapter
		self::setDriver('mysql', \Yesf\Connection\Driver\Mysql::class);
		self::setDriver('redis', \Yesf\Connection\Driver\Redis::class);
		self::setDriver('tcp', \Yesf\Connection\Driver\TcpClient::class);
		self::setAdapter('mysql', \Yesf\RD\Adapter\Mysql::class);
		self::setAdapter('redis', \Yesf\Cache\Adapter\Redis::class);
	}
	/**
	 * Get min connection count
	 * 
	 * @access public
	 * @return int
	 */
	public static function getMin() {
		return Swoole::$isTaskWorker ? self::$connection_task['min'] : self::$connection_default['min'];
	}
	/**
	 * Get max connection count
	 * 
	 * @access public
	 * @return int
	 */
	public static function getMax() {
		return Swoole::$isTaskWorker ? self::$connection_task['max'] : self::$connection_default['max'];
	}
	/**
	 * Get a connection
	 * 
	 * @access public
	 * @param mixed $config
	 * @return object
	 */
	public static function get($config = null) {
		if (!isset($config['driver'])) {
			throw new ConnectionException("Unknown driver");
		}
		if (!isset($config['host']) || !isset($config['port'])) {
			throw new ConnectionException("Host and Port is required");
		}
		$type = $config['driver'];
		$hash = md5($type . ':' . $config['host'] . ':' . $config['port']);
		if (!isset(self::$created_driver[$hash])) {
			if (isset(self::$driver[$type])) {
				$className = self::$driver[$type];
			} else {
				$className = __NAMESPACE__ . '\\Driver\\' . ucfirst($type);
			}
			self::$created_driver[$hash] = new $className($config);
		}
		return self::$created_driver[$hash];
	}
	/**
	 * Close connections
	 * 
	 * @access public
	 */
	public static function close($config = null) {
		if ($config === null) {
			// Close all
			foreach (self::$created_driver as $k => $v) {
				$hash = spl_object_hash($v);
				if (isset(self::$created_adapter[$hash])) {
					unset(self::$created_adapter[$hash]);
				}
				unset(self::$created_driver[$k]);
			}
		} else {
			if (is_string($config)) {
				$config = Yesf::app()->getConfig('connection.' . $config);
			}
			if (!isset($config['driver'])) {
				throw new ConnectionException("Unknown driver");
			}
			if (!isset($config['host']) || !isset($config['port'])) {
				throw new ConnectionException("Host and Port is required");
			}
			$hash = md5($config['driver'] . ':' . $config['host'] . ':' . $config['port']);
			if (isset(self::$created_driver[$hash])) {
				$adapter_hash = spl_object_hash(self::$created_driver[$hash]);
				if (isset(self::$created_adapter[$adapter_hash])) {
					unset(self::$created_adapter[$adapter_hash]);
				}
				unset(self::$created_driver[$hash]);
			}
		}
	}
	/**
	 * Get a connection with Adapter
	 * If $config is string, this function will get config from Yesf::app()->getConfig
	 * If $config is array, this function will use it directly
	 * 
	 * @access public
	 * @param mixed $config
	 * @return object
	 */
	public static function getAdapter($config = null) {
		if (is_string($config)) {
			$config = Yesf::app()->getConfig('connection.' . $config);
		}
		if (!isset($config['adapter'])) {
			throw new ConnectionException("Unknown adapter");
		}
		$type = $config['adapter'];
		// Get connection
		$connection = Pool::get($config);
		$hash = spl_object_hash($connection);
		if (!isset(self::$created_adapter[$hash])) {
			if (isset(self::$adapter[$type])) {
				$className = self::$adapter[$type];
			} else {
				throw new ConnectionException("Unknown adapter");
			}
			$instance = new $className($connection);
			self::$created_adapter[$hash] = $instance;
			return $instance;
		} else {
			return self::$created_adapter[$hash];
		}
	}
	/**
	 * 注册自定义Driver
	 * 
	 * @access public
	 * @param string $type
	 * @param string $className 类名
	 */
	public static function setDriver($type, $className) {
		if (!is_subclass_of($className, PoolInterface::class)) {
			throw new InvalidClassException("Class $className not implement PoolInterface");
		}
		self::$driver[$type] = $className;
	}
	/**
	 * 注册自定义Adapter
	 * 
	 * @access public
	 * @param string $type
	 * @param string $className 类名
	 */
	public static function setAdapter($type, $className) {
		self::$adapter[$type] = $className;
	}
}