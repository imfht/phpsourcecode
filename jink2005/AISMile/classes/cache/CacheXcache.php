<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

/**
 * This class require Xcache extension
 *
 * @since 1.5.0
 */
class CacheXcacheCore extends Cache
{
	public function __construct()
	{
		$this->keys = xcache_get(self::KEYS_NAME);
		if (!is_array($this->keys))
			$this->keys = array();
	}

	/**
	 * @see Cache::_set()
	 */
	protected function _set($key, $value, $ttl = 0)
	{
		return xcache_set($key, $value, $ttl);
	}

	/**
	 * @see Cache::_get()
	 */
	protected function _get($key)
	{
		return xcache_isset($key) ? xcache_get($key) : false;
	}

	/**
	 * @see Cache::_exists()
	 */
	protected function _exists($key)
	{
		return xcache_isset($key);
	}

	/**
	 * @see Cache::_delete()
	 */
	protected function _delete($key)
	{
		return xcache_unset($key);
	}

	/**
	 * @see Cache::_writeKeys()
	 */
	protected function _writeKeys()
	{
		xcache_set(self::KEYS_NAME, $this->keys);
	}

	/**
	 * @see Cache::flush()
	 */
	public function flush()
	{
		$this->delete('*');
		return true;
	}
}
