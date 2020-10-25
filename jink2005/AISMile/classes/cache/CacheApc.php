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
 * This class require PECL APC extension
 *
 * @since 1.5.0
 */
class CacheApcCore extends Cache
{
	public function __construct()
	{
		$this->keys = array();
		$cache_info = apc_cache_info('user');
		foreach ($cache_info['cache_list'] as $entry)
			$this->keys[$entry['info']] = $entry['ttl'];
	}

	/**
	 * @see Cache::_set()
	 */
	protected function _set($key, $value, $ttl = 0)
	{
		return apc_store($key, $value, $ttl);
	}

	/**
	 * @see Cache::_get()
	 */
	protected function _get($key)
	{
		return apc_fetch($key);
	}

	/**
	 * @see Cache::_exists()
	 */
	protected function _exists($key)
	{
		return isset($this->keys[$key]);
	}

	/**
	 * @see Cache::_delete()
	 */
	protected function _delete($key)
	{
		return apc_delete($key);
	}

	/**
	 * @see Cache::_writeKeys()
	 */
	protected function _writeKeys()
	{
	}

	/**
	 * @see Cache::flush()
	 */
	public function flush()
	{
		return apc_clear_cache();
	}
}
