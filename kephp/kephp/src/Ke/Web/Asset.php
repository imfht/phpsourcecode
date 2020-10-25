<?php
/**
 * KePHP, Keep PHP easy!
 *
 * @license   https://opensource.org/licenses/MIT
 * @copyright Copyright 2015 - 2020 KePHP Authors All Rights Reserved
 * @link      http://kephp.com ( https://git.oschina.net/kephp/kephp-core )
 * @author    曾建凯 <janpoem@163.com>
 */


namespace Ke\Web;


use Ke\Uri;

class Asset
{

	const DEFAULT_NAME = 'default';

	const IDX_SRC = 0;

	const IDX_TYPE = 1;

	const IDX_PROP = 2;

	const LIB = 'lib';

	private static $instances = [];

	protected $libraries = [];

	protected $loaded = [];

	protected $baseUri = null;

	/**
	 * @param string|null $name
	 * @return Asset
	 */
	public static function getInstance(string $name = null)
	{
		if (empty($name))
			$name = self::DEFAULT_NAME;
		if (!isset(self::$instances[$name])) {
			self::$instances[$name] = new static();
		}
		return self::$instances[$name];
	}

	public function loadFile($file)
	{
		$data = import($file);
		if (!empty($data) && is_array($data))
			$this->setLibraries($data);
		return $this;
	}

	public function setLibraries(array $libraries)
	{
		if (empty($this->libraries))
			$this->libraries = $libraries;
		else
			$this->libraries = array_merge_recursive($this->libraries, $libraries);
		return $this;
	}

	public function getLibraries(): array
	{
		return $this->libraries;
	}

	public function setLibrary(string $name, array $libraries, bool $isMerge = false)
	{
		if (!isset($this->libraries[$name]) || !$isMerge)
			$this->libraries[$name] = $libraries;
		else
			$this->libraries[$name] = array_merge_recursive($this->libraries[$name], $libraries);
		return $this;
	}

	public function getLibrary(string $name)
	{
		if (empty($this->libraries[$name]))
			return false;
		if (!is_array($this->libraries[$name])) {
			$name = $this->libraries[$name];
			if (empty($this->libraries[$name]))
				return false;
		}
		return $this->libraries[$name];
	}

	public function getLibraryLink(string $name, int $index = 0)
	{
		$lib = $this->getLibrary($name);
		if (empty($lib) || empty($lib[$index]))
			return false;
		list($src, $type, $query) = $lib[0];
		return $this->getBaseUri()->newUri(ext($src, $type), $query)->toUri(true);
	}

	public function load($src, string $type = null, array $props = null, $query = null)
	{
		if (is_array($src)) {
			foreach ($src as $item) {
				if (!is_array($item))
					$item = (array)$item;
				if (!isset($item[1]) && isset($type))
					$item[1] = $type;
				$this->load(...$item);
			}
			return $this;
		} elseif (is_string($src)) {
			$libs = $this->getLibrary($src);
			if (!empty($libs)) {
				$this->onLoadingLib($src);
				foreach ($libs as $settings) {
					$this->load(...(array)$settings);
				}
				return $this;
			} else {
				if (empty($type))
					return $this;
				if ($this->isLoad($src, $type))
					return $this;
				if ($this->onLoadingSrc($src, $type) !== false) {
					$this->addLoad($src, $type);
					$tag = $this->makeReferenceTag($src, $type, $props, $query);
					if (!empty($tag)) {
						print $tag . PHP_EOL;
					}
				}
			}
		}
		return $this;
	}

	public function makeLoadKey(string $src, string $type)
	{
		return "{$src}-{$type}";
	}

	public function isLoad(string $src, string $type = null)
	{
		return isset($this->loaded[$this->makeLoadKey($src, $type)]);
	}

	public function addLoad(string $src, string $type = null)
	{
		$this->loaded[$this->makeLoadKey($src, $type)] = true;
		return $this;
	}

	protected function onLoadingLib(string $name)
	{
	}

	protected function onLoadingSrc(string $src, string $type)
	{
	}

	public function makeUrl($url, $query = null)
	{
		return new Uri([
			'uri'   => $url,
			'query' => $query,
		]);
	}

	public function makeReferenceTag(string $src, string $type, array $props = null, $query = null)
	{
		if (!isset($props['buildUri']) || !empty($props['buildUri']))
			$src = $this->makeUrl(ext($src, $type), $query);
		$tag = '';
		$attr = '';
		if (!empty($props))
			$attr = $this->makeProps($props);
		switch ($type) {
			case 'js' :
				$tag = '<script type="text/javascript" src="%s"%s></script>';
				break;
			case 'css' :
				$tag = '<link rel="stylesheet" type="text/css" href="%s"%s/>';
				break;
		}
		return sprintf($tag, $src, $attr);
	}

	public function makeProps(array $props, string $prefix = '', array & $return = [])
	{
		foreach ($props as $key => $value) {
			if (empty($key) || !is_string($key))
				continue;
			if (!empty($prefix))
				$key = $prefix . '-' . $key;
			if (is_array($value) || is_object($value)) {
				$this->makeProps($value, $key, $return);
			} else {
				$return[] = $key . '="' . htmlentities($value) . '"';
			}
		}
		if (!empty($return))
			return ' ' . implode(' ', $return);
		return '';
	}

	public function getBaseUri()
	{
		if (!isset($this->baseUri)) {
			$web = Web::getWeb();
			if (KE_HTTP_REWRITE)
				$this->baseUri = $web->getBaseUri();
			else
				$this->baseUri = new Uri([
					'scheme' => KE_REQUEST_SCHEME,
					'host'   => KE_REQUEST_HOST,
					'uri'    => dirname(KE_HTTP_BASE),
				]);
		}
		return $this->baseUri;
	}
}