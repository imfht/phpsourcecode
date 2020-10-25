<?php
/**
 * KePHP, Keep PHP easy!
 *
 * @license   https://opensource.org/licenses/MIT
 * @copyright Copyright 2015 - 2020 KePHP Authors All Rights Reserved
 * @link      http://kephp.com ( https://git.oschina.net/kephp/kephp-core )
 * @author    曾建凯 <janpoem@163.com>
 */

namespace Ke\Adm;


use Exception;
use Ke\Adm\Adapter\CacheAdapter;

class Cache
{
	
	const DEFAULT_SOURCE = 'default';
	
	const MEMCACHE = 'memcache';
	
	const REDIS = 'redis';
	
	protected static $defaultName = 'main';
	
	private static $classes = [
//		self::MEMCACHE => Adapter\Cache\Memcache::class, // 这个 memcache 扩展实际上在 php7 已经无效了。
		self::REDIS    => Adapter\Cache\RedisCache::class,
	];
	
	private static $sources = [];
	
	private static $adapters = [];
	
	public static function getAdapterClasses()
	{
		return self::$classes;
	}
	
	public static function getAdapterClass(string $name)
	{
		return self::$classes[$name] ?? false;
	}
	
	public static function registerAdapter(string $name, string $className)
	{
		if (class_exists($className) && class_implements($className, CacheAdapter::class)) {
			self::$classes[$name] = $className;
			return true;
		}
		return false;
	}
	
	public static function define(array $configs)
	{
		foreach ($configs as $source => $config) {
			self::set($source, $config);
		}
	}
	
	public static function set(string $source = null, array $config)
	{
		if (empty($source))
			$source = self::DEFAULT_SOURCE;
		if (isset(self::$adapters[$source])) {
			self::$adapters[$source]->configure($config);
		} else {
			if (empty(self::$sources[$source]))
				self::$sources[$source] = $config;
			else
				self::$sources[$source] = array_merge(self::$sources[$source], $config);
		}
		return true;
	}
	
	public static function get(string $source = null)
	{
		if (empty($source))
			$source = self::DEFAULT_SOURCE;
		if (isset(self::$adapters[$source]))
			return self::$adapters[$source]->getConfiguration();
		else if (isset(self::$sources[$source]))
			return self::$sources[$source];
		return false;
	}
	
	/**
	 * @param null $source
	 *
	 * @return Adapter\CacheAdapter|Adapter\Cache\Memcache|Adapter\Cache\RedisCache
	 * @throws Exception
	 */
	public static function getAdapter($source = null)
	{
		if (empty($source))
			$source = self::DEFAULT_SOURCE;
		if (isset(self::$adapters[$source]))
			return self::$adapters[$source];
		if (!isset(self::$sources[$source]))
			throw new Exception("Cache[{$source}]: undefined cache config!");
		if (empty(self::$sources[$source]['adapter']))
			throw new Exception("Cache[{$source}]: undefined cache adapter!");
		$class = self::$sources[$source]['adapter'];
		if (isset(self::$classes[$class]))
			$class = self::$classes[$class];
		if (!is_subclass_of($class, Adapter\CacheAdapter::class))
			throw new Exception("Cache[{$source}]: invalid cache adapter!");
		
		/** @var Adapter\DbAdapter $adapter */
		self::$adapters[$source] = new $class($source, self::$sources[$source]);
		return self::$adapters[$source];
	}
}