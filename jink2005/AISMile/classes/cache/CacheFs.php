<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

class CacheFsCore extends Cache
{
	/**
	 * @var int Number of subfolders to dispatch cached filenames
	 */
	protected $depth;

	protected function __construct()
	{
		$this->depth = Db::getInstance()->getValue('SELECT value FROM '._DB_PREFIX_.'configuration WHERE name= \'PS_CACHEFS_DIRECTORY_DEPTH\'', false);

		$keys_filename = $this->getFilename(self::KEYS_NAME);
		if (file_exists($keys_filename))
			$this->keys = unserialize(file_get_contents($keys_filename));
	}

	/**
	 * @see Cache::_set()
	 */
	protected function _set($key, $value, $ttl = 0)
	{
		return (@file_put_contents($this->getFilename($key), serialize($value)));
	}

	/**
	 * @see Cache::_get()
	 */
	protected function _get($key)
	{
		if ($this->keys[$key] > 0 && $this->keys[$key] < time())
		{
			$this->delete($key);
			return false;
		}

		$filename = $this->getFilename($key);
		if (!file_exists($filename))
		{
			unset($this->keys[$key]);
			$this->_writeKeys();
			return false;
		}
		$file = file_get_contents($filename);
		return unserialize($file);
	}

	/**
	 * @see Cache::_exists()
	 */
	protected function _exists($key)
	{
		if ($this->keys[$key] > 0 && $this->keys[$key] < time())
		{
			$this->delete($key);
			return false;
		}

		return isset($this->keys[$key]) && file_exists($this->getFilename($key));
	}

	/**
	 * @see Cache::_delete()
	 */
	protected function _delete($key)
	{
		$filename = $this->getFilename($key);
		if (!file_exists($filename))
			return true;
		return unlink($filename);
	}

	/**
	 * @see Cache::_writeKeys()
	 */
	protected function _writeKeys()
	{
		@file_put_contents($this->getFilename(self::KEYS_NAME), serialize($this->keys));
	}

	/**
	 * @see Cache::flush()
	 */
	public function flush()
	{
		$this->delete('*');
		return true;
	}

	/**
	 * Delete cache directory
	 */
	public static function deleteCacheDirectory()
	{
		Tools::deleteDirectory(_PS_CACHEFS_DIRECTORY_, false);
	}

	/**
	 * Create cache directory
	 *
	 * @param int $level_depth
	 * @param string $directory
	 */
	public static function createCacheDirectories($level_depth, $directory = false)
	{
		if (!$directory)
			$directory = _PS_CACHEFS_DIRECTORY_;
		$chars = '0123456789abcdef';
		for ($i = 0, $length = strlen($chars); $i < $length; $i++)
		{
			$new_dir = $directory.$chars[$i].'/';
			if (mkdir($new_dir))
				if (chmod($new_dir, 0777))
					if ($level_depth - 1 > 0)
						CacheFs::createCacheDirectories($level_depth - 1, $new_dir);
		}
	}

	/**
	 * Transform a key into its absolute path
	 *
	 * @param string $key
	 * @return string
	 */
	protected function getFilename($key)
	{
		$path = _PS_CACHEFS_DIRECTORY_;
		for ($i = 0; $i < $this->depth; $i++)
			$path .= $key[$i].'/';

		return $path.$key;
	}
}
