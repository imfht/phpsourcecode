<?php
/**
 * KePHP, Keep PHP easy!
 *
 * @license   https://opensource.org/licenses/MIT
 * @copyright Copyright 2015 - 2020 KePHP Authors All Rights Reserved
 * @link      http://kephp.com ( https://git.oschina.net/kephp/kephp-core )
 * @author    曾建凯 <janpoem@163.com>
 */

namespace Ke;


/**
 * 目录注册器
 *
 * @package Ke\Core
 */
class DirectoryRegistry
{

	const DEFAULT_SCOPE = '*';

	/**
	 * @var string 默认的范围，这个属性应该是一个字符串
	 */
	protected $defaultScope = self::DEFAULT_SCOPE;

	/**
	 * @var string 默认的拼接文件后缀名称
	 */
	protected $extension = 'php';

	protected $scopeAliases = [];

	/**
	 * @var array 范围重定向，如果指定了重定向的值
	 */
	protected $scopeRewrites = [];

	/**
	 * @var bool 是否严格验证目录，如果启用严格验证的话，设置的目录不存在时，将不会被添加
	 */
	protected $isStrictVerify = false;

	/**
	 * @var array 目录优先级的排序参考
	 */
	private $sort = [];

	/**
	 * @var array 已经注册的目录
	 */
	private $dirs = [];

	/**
	 * @var array 已经注册的范围
	 */
	private $scopes = [];

	private $scopeCaches = [];

	/**
	 * @var bool 目录是否已经排序的标识
	 */
	private $isSort = false;

	private $autoPriorityIndex = 0;

	private $dirCounter = 0;

	public function setExtension(string $ext)
	{
		$this->extension = strtolower(trim($ext, '. '));
		return $this;
	}

	public function getExtension()
	{
		return $this->extension;
	}

	/**
	 * 设置目录注册器使用严格验证的模式，即注册目录时，会检查目录是否存在且为一个有效的目录
	 *
	 * @param bool $isStrict
	 * @return $this
	 */
	public function setStrictVerify(bool $isStrict)
	{
		$this->isStrictVerify = $isStrict;
		return $this;
	}

	/**
	 * 是否使用严格验证模式
	 *
	 * @return bool
	 */
	public function isStrictVerify(): bool
	{
		return $this->isStrictVerify;
	}

	/**
	 * 生成自动的权重值
	 *
	 * @return int
	 */
	public function autoPriority(): int
	{
		return $this->autoPriorityIndex++;
	}

	/**
	 * 注册单个目录
	 *
	 * @param string|int|null $name      目录简称
	 * @param string          $dir       目录的路径，注意，注册目录时，并不会检查目录是否存在
	 * @param int             $priority  优先级
	 * @param \string[]       ...$scopes 注册的范围
	 * @return $this
	 */
	public function setDir($name = null, string $dir, int $priority = -1, string ...$scopes)
	{
		if (empty($name) || !is_string($name))
			$name = $this->dirCounter++;
		// 不让目录重复添加
		if (in_array($dir, $this->dirs))
			return $this;
		if ($this->isStrictVerify && ($dir = real_dir($dir)) === false)
			return $this;
		$this->dirs[$name] = $dir;
		$this->sort[$name] = $priority < 0 ? $this->autoPriority() : $priority;
		$this->isSort = count($this->dirs) <= 1;
		$this->setScopes($name, ...$scopes);
		return $this;
	}

	/**
	 * 注册多个目录
	 *
	 * <code>
	 * $dir = new DirRegistry();
	 * $dir->setDirs([
	 *     'default' => ['default_path', 0], // 'default' => 'path1'
	 *     'scope'   => ['scope_path1', 10, 'scope'],
	 *     'scopes'  => ['scope_path2', 20, 'scope', 'scope1', 'scope2'],
	 * ]);
	 * </code>
	 *
	 * @param array $dirs
	 * @return $this
	 */
	public function setDirs(array $dirs)
	{
		foreach ($dirs as $key => $value) {
			$this->setDir($key, ...(array)$value);
		}
		return $this;
	}

	public function deleteDir(string $dir)
	{
		if (!isset($this->dirs[$dir])) {
			$dir = array_search($dir, $this->dirs);
			if ($dir === false)
				return $this;
		}
		unset($this->dirs[$dir]);
		unset($this->sort[$dir]);
		return $this;
	}

	/**
	 *
	 *
	 * @param string $dir
	 * @return mixed
	 */
	public function indexOf(string $dir)
	{
		return array_search($dir, $this->dirs, true);
	}

	public function dir(string $name): string
	{
		if (!isset($this->dirs[$name])) {
			$name = array_search($name, $this->dirs);
			if ($name === false)
				return false;
		}
		return $this->dirs[$name];
	}


	/**
	 * 设定路径范围
	 *
	 * @param string    $name
	 * @param \string[] ...$scopes
	 * @return $this
	 */
	public function setScopes(string $name, string ...$scopes)
	{
		if (!isset($this->dirs[$name]))
			return $this;
		if (empty($scopes))
			$scopes = $this->getDefaultScopes();
		foreach ($scopes as $scope) {
			$this->scopes[$scope][$name] = true;
			if (isset($this->scopeCaches[$scope]))
				unset($this->scopeCaches[$scope]);
		}
		return $this;
	}

	/**
	 * 定义范围的重定向
	 *
	 * @param array $rewrites
	 * @return $this
	 */
	public function setScopeRewrites(array $rewrites)
	{
		$this->scopeRewrites = array_merge($this->scopeRewrites, $rewrites);
		return $this;
	}

	public function addScopeRewrites(array $rewrites)
	{
		$this->scopeRewrites += $rewrites;
		return $this;
	}

	public function setScopeAliases(array $aliases)
	{
		$this->scopeAliases = array_merge($this->scopeRewrites, $aliases);
		return $this;
	}

	public function addScopeAliases(array $aliases)
	{
		$this->scopeAliases += $aliases;
		return $this;
	}

	public function getDefaultScope(): string
	{
		return $this->defaultScope;
	}

	/**
	 * 取得默认的范围值，这个方法用于在注册目录时，没指定范围目录的时候使用。
	 *
	 * @return array
	 */
	public function getDefaultScopes(): array
	{
		return [$this->defaultScope];
	}

	public function filterScope(string $scope = null): string
	{
		if (isset($this->scopeAliases[$scope]))
			$scope = $this->scopeAliases[$scope];
		if (empty($scope) || !isset($this->scopes[$scope]))
			$scope = $this->defaultScope;
		return $scope;
	}

	public function hasScope(string $scope): bool
	{
		if (isset($this->scopeAliases[$scope]))
			$scope = $this->scopeAliases[$scope];
		if (empty($scope))
			$scope = $this->defaultScope;
		return isset($this->scopes[$scope]);
	}

	/**
	 * 将当前目录按照优先级排序
	 *
	 * @return $this
	 */
	public function sort()
	{
		if (!$this->isSort) {
			$this->isSort = array_multisort($this->sort, $this->dirs);
		}
		return $this;
	}

	/**
	 * 取回指定范围的所有目录
	 *
	 * @param string|null $scope
	 * @return mixed
	 */
	public function getScopeDirs(string $scope = null): array
	{
		if (!$this->isSort)
			$this->sort();
		$scope = $this->filterScope($scope);
		if (empty($this->dirs) || empty($this->scopes[$scope]))
			return [];
		if (!isset($this->scopeCaches[$scope])) {
			$this->scopeCaches[$scope] = array_intersect_key($this->dirs, $this->scopes[$scope]);
		}
		return $this->scopeCaches[$scope];
	}

	/**
	 * 基于范围搜索相关的文件，返回这个文件的完整路径，如果搜索不到，则范围false
	 *
	 * @param string|null $scope
	 * @param string      $file
	 * @param bool        $isAll
	 * @param bool        $refresh
	 * @return array|bool|string
	 */
	public function seek(string $scope = null, string $file, bool $isAll = false, $refresh = false)
	{
		if (empty($file))
			return false;
		$scope = $this->filterScope($scope);
		if (!empty($this->extension))
			$file = ext($file, $this->extension);
		if (!KE_IS_WIN && strpos($file, KE_DS_CLS) !== false)
			$file = str_replace(KE_DS_CLS, KE_DS_UNIX, $file);
		$dir = DIRECTORY_SEPARATOR;
		if (!empty($this->scopeRewrites[$scope]))
			$dir .= $this->scopeRewrites[$scope] . DIRECTORY_SEPARATOR;
		$result = null;
		foreach ($this->getScopeDirs($scope) as $name => $base) {
			if (($base = real_dir($base, $refresh)) === false)
				continue;
			if (($path = real_file($base . $dir . $file, $refresh)) === false)
				continue;
			if (!$isAll)
				return $path;
			$result[] = $path;
		}
		return empty($result) ? false : $result;
	}
}