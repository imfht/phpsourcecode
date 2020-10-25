<?php
/**
 * 文件
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

class File implements CacheInterface {
	/** @var string $path Cache file directory */
	private $path;

	public function __construct() {
		$this->path = App::$config->get('cache.path');
		if ($this->path === '@TMP') {
			$this->path = sys_get_temp_dir();
		}
		if (strpos($this->path, '@APP') === 0) {
			$this->path = APP_PATH . substr($this->path, 5);
		}
		if (!is_dir($this->path)) {
			mkdir($this->path, 0777, true);
		}
		$this->path = str_replace('\\', '/', $this->path);
		if (substr($this->path, -1) !== '/') {
			$this->path .= '/';
		}
	}

	private function getKey($key) {
		return md5($key);
	}

	private function getPath($key) {
		$name = $this->getKey($key);
		return $this->path . $name;
	}

	public function get($key, $default = null) {
		$fullpath = $this->getPath($key);
		if (!is_file($fullpath)) {
			return $default;
		}
		$res = file_get_contents($fullpath);
		if ($res === false) {
			return $default;
		}
		// The first 10 bytes is expire timestamp
		$expire = substr($res, 0, 10);
		if ($expire != 0 && $expire <= time()) {
			$this->delete($key);
			return $default;
		}
		return unserialize(substr($res, 10));
	}

	public function set($key, $value, $ttl = null) {
		if ($ttl === null) {
			$time = 0;
		} else {
			$time = time() + $ttl;
		}
		$path = $this->getPath($key);
		$content = str_pad($time, 10, '0', STR_PAD_LEFT) . serialize($value);
		file_put_contents($path, $content);
	}

	public function delete($key) {
		@unlink($this->getPath($key));
	}

	public function clear() {
		$dh = opendir($this->path);
		while ($f = readdir($dh)) {
			if (!is_file($this->path . $f)) {
				continue;
			}
			@unlink($this->path . $f);
		}
		closedir($dh);
	}

	public function getMultiple($keys, $default = null) {
		$result = [];
		foreach ($keys as $v) {
			$res = $this->get($v);
			if ($res === null) {
				$result[$v] = $default;
			} else {
				$result[$v] = $res;
			}
		}
		return $result;
	}

	public function setMultiple($values, $ttl = null) {
		foreach ($values as $k => $v) {
			$this->set($k, $v, $ttl);
		}
	}

	public function deleteMultiple($keys) {
		foreach ($keys as $v) {
			$this->delete($v);
		}
	}

	public function has($key) {
		return is_file($this->getPath($key));
	}
}