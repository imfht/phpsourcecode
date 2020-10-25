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

/**
 * 这个接口并不约束任何接口，只是为了简化判断是否实现了缓存模型
 *
 * 在应用程序中，不需要实现这个接口，只要`use CacheModelTrait;`即可。
 *
 * 请参考[issues#trait_deep_search.php]
 *
 * @package Ke\Adm
 */
interface CacheModelImpl
{
}

/**
 * 将任意的类变为可缓存的模型
 *
 * 缓存存储的时候，直接将当前的对象实例（mixin CacheModelTrait）放入缓存中。
 *
 * todo: 需要加入KE_APP_HASH，和数据特征HASH，不然很容易数据混读，或者版本之间冲突。
 *
 * @package Ke\Adm
 */
trait CacheModelTrait
{

	// 下面的几个属性是很重要的属性，但是php目前版本，如果一个类继承自ArrayObject，并且混入了这个Trait
	// 当从缓存中读取的时候，private会失效，这是个很严重的bug。所以下来属性暂时保留用protected
	// 参考[issues#array_object.php]
	// 所以，在访问下列的属性的时候，请使用
	protected $cacheArgs = [];

	protected $cacheKey = '';

	protected $cacheHash = '';

	protected $cacheStatus = false;

	protected $cacheUpdateAt = 0;

	public static function isEnableCache(): bool
	{
		return static::getCacheSource() !== false;
	}

	public static function getCacheSource()
	{
		return null;
	}

	public static function getCacheAdapter()
	{
		if (!static::isEnableCache())
			throw new \Exception("The model cache is not enable!");
		return Cache::getAdapter(static::getCacheSource());
	}

	public static function getCacheDefaultTTL(): int
	{
		return 60 * 60 * 12;
	}

	public static function getCacheKeyPrefix(): string
	{
		return str_replace(KE_DS_CLS, '-', static::class);
	}

	public static function getCacheKeySuffix(): string
	{
		return '';
	}

	public static function getCacheKeySeparator(): string
	{
		return '.';
	}

	public static function makeCacheBaseKey(): string
	{
		$args = func_get_args();
		if (empty($args))
			return '';
		$spr = static::getCacheKeySeparator();
		return implode($spr, $args);
	}

	/**
	 * 将查询缓存的参数构建为一个缓存的key
	 *
	 * 默认生成：`prefix.implode('.', $args).suffix`
	 *
	 * @return string
	 */
	public static function makeCacheKey(): string
	{
		$args = [static::makeCacheBaseKey(...func_get_args())];
		$prefix = static::getCacheKeyPrefix();
		if (!empty($prefix))
			array_unshift($args, $prefix);
		$suffix = static::getCacheKeySuffix();
		if (!empty($suffix))
			$args[] = $suffix;
		$spr = static::getCacheKeySeparator();
		return implode($spr, $args);
	}

	/**
	 * 将查询缓存的参数构建为一个参数列表
	 *
	 * 默认行为是直接将这个传入的这个参数(数组形式)返回。
	 *
	 * <code>
	 * GoodsCategoryIndex::loadCache('e-book'); // $args 为 ['e-book'];
	 *
	 * public static function makeCacheArgs($categoryName = ''): array
	 * {
	 *     return ['name' => $categoryName];
	 * }
	 * </code>
	 *
	 * @return array
	 */
	public static function makeCacheArgs(): array
	{
		return func_get_args();
	}

	/**
	 * 构建一个Cache模型的实例对象
	 *
	 * 一般来说，不需要直接重载这个方法，而只需要重载onPrepareCache的方法，即可。
	 *
	 * 但是如Model，采用use trait的方式载入，因为本身Model的__construct已经绑定了一些特定的操作，则需要重载这个方法。
	 *
	 * @param string $key
	 * @param array  $args
	 * @return static
	 */
	public static function makeCacheInstance(string $key, array $args)
	{
		return new static();
	}

	public static function isCacheModelImpl($cache)
	{
		if (!is_object($cache))
			return false;
		if (!(($cache instanceof CacheModelImpl) || isset(class_uses($cache)[CacheModelTrait::class])))
			return false;
		return true;
	}

	/**
	 * @param array ...$args
	 * @return static
	 */
	public static function &loadCache(...$args)
	{
		global $KE_CACHES;
		$key = static::makeCacheKey(...$args);
		$cache = $KE_CACHES[$key] ?? static::getCacheAdapter()->get($key);
		if ($cache !== false) {
			if (!static::isCacheModelImpl($cache) || !$cache->verifyCacheHash($cache->cacheHash ?? null)) {
				$cache = false;
			}
		}
		if ($cache === false) {
			$args = static::makeCacheArgs(...$args);
			$cache = static::makeCacheInstance($key, $args);
			// 如果一个class use trait，那么判断这个class实例，要用class_uses
			// 如果先一个class use trait，然后class1 extends class，那么就需要判断实例是不是指定类的实例，这也太绕了。
			if (!is_object($cache) ||
				!(($cache instanceof CacheModelImpl) ||
					isset(class_uses($cache)[CacheModelTrait::class])) // PHP你有没有觉得自己垢了？
			) {
				$cache = new static();
			}
			$cache->cacheKey = $key;
			$cache->cacheArgs = $args;
			$cache->onPrepareCache($key, $args);
			$cache->cacheHash = $cache->makeCacheHash();
			// 无效的对象，不保存，也不放入全局的变量中，直接返回这个对象
			if (!$cache->isValidCache()) {
				return $cache;
			}
			$cache->saveCache();
		}
		else {
			$cache->onLoadCache();
		}
		if (!isset($KE_CACHES[$key]))
			$KE_CACHES[$key] = $cache;
		return $KE_CACHES[$key];
	}

	protected function onPrepareCache(string $key, array $args)
	{
	}

	protected function onLoadCache() {}

	abstract public function isValidCache();

	abstract public function makeCacheHash(): string;

	public function verifyCacheHash(string $hash = null): bool
	{
		return $hash === $this->makeCacheHash();
	}

//	abstract public function getCacheHash(): string;

	public function isCached()
	{
		return $this->cacheStatus !== false;
	}

	public function getCacheKey(): string
	{
		return $this->cacheKey;
	}

	public function getCacheArgs(): array
	{
		return $this->cacheArgs;
	}

	public function getCacheHash(): string
	{
		return $this->cacheHash;
	}

	public function getCacheStatus()
	{
		return $this->cacheStatus;
	}

	public function getCacheArg($field, $default = null)
	{
		return $this->cacheArgs[$field] ?? $default;
	}

	public function getCacheTTL(): int
	{
		return static::getCacheDefaultTTL();
	}

	public function getExpireDate(): int
	{
		if ($this->isCached())
			return $this->cacheUpdateAt + $this->getCacheTTL();
		return -1;
	}

	public function getUpdateDate(): int
	{
		return $this->cacheUpdateAt;
	}

	protected function setCacheStatus($status, int $update = null)
	{
		if ($status !== false) {
			$this->cacheStatus = $status;
			$this->cacheUpdateAt = $update ?? time();
		} else {
			$this->cacheStatus = $status;
			$this->cacheUpdateAt = $update ?? 0;
		}
		return $this;
	}

	public function saveCache()
	{
		$lastStatus = $this->cacheStatus;
		$lastUpdate = $this->cacheUpdateAt;
		$event = 'onCreateCache';
		if (empty($this->status)) {
			$this->setCacheStatus(Model::ON_CREATE);
		} else {
			$event = 'onUpdateCache';
			$this->setCacheStatus(Model::ON_UPDATE);
		}
		if ($this->$event() !== false &&
			$this->onSaveCache() !== false &&
			static::getCacheAdapter()->set($this->getCacheKey(), $this, $this->getCacheTTL())
		) {
			return Model::SAVE_SUCCESS;
		}
		$this->setCacheStatus($lastStatus, $lastUpdate);
		return Model::SAVE_FAILURE;
	}

	protected function onCreateCache()
	{
	}

	protected function onUpdateCache()
	{
	}

	protected function onSaveCache()
	{
	}

	public function destroyCache()
	{
		if (!$this->isCached())
			return Model::SAVE_FAILURE;
		$lastStatus = $this->cacheStatus;
		$this->cacheStatus = Model::ON_DELETE;
		$key = $this->getCacheKey();
		if ($this->onDestroyCache() !== false && static::getCacheAdapter()->delete($key)) {
			global $KE_CACHES;
			if (isset($KE_CACHES[$key]))
				unset($KE_CACHES[$key]);
			$this->setCacheStatus(false);
			return Model::SAVE_SUCCESS;
		}
		$this->setCacheStatus($lastStatus);
		return Model::SAVE_FAILURE;
	}

	protected function onDestroyCache()
	{
	}
}