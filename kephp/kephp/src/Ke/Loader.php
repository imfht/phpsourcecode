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
 * 应用程序加载器，包含了Class Loader和Helper Loader
 *
 * 本加载器并不包含Cli/Command加载器和Web/Component加载器。
 *
 * 可以理解为，全局基础的ClassLoader和FunctionLoader
 *
 * @package Ke
 */
class Loader extends DirectoryRegistry
{

	const HELPER = 'helper';

	const CLS = 'class';

	const LOADED = 1;

	const NOT_LOADED = 0;

	const UNDEFINED = -1;

	protected $defaultScope = self::CLS;

	/**
	 * @var array 已知的类和对应的存放路径
	 */
	protected $classPaths = [];

	/**
	 * @var bool class loader是否使用prepend模式，允许继承类重设该值
	 */
	protected $isPrepend = false;

	/**
	 * @var bool 类加载器是否已经启用
	 */
	private $isStart = false;

	private $loadClasses = [];

	private $loadHelpers = [];

	public function register(string $name, string $home, int $priority = 0, array $group = [])
	{
		if (empty($name))
			return $this;
		if (!empty($group)) {
			foreach ($group as $key => $value) {
				$scopes = [];
				if (is_array($value)) {
					$dir = array_shift($value);
					$scopes = $value;
				}
				$key = $name . (empty($key) ? '' : '_' . $key);
				$dir = $home . (empty($dir) ? '' : DS . $dir);
				$this->setDir($key, $dir, $priority, ...$scopes);
			}
		} else {
			$this->setDir($name, $home, $priority);
		}
		return $this;
	}

	public function __construct(array $options = null)
	{
		if (!empty($options))
			$this->setOptions($options);
	}

	public function setOptions(array $options)
	{
		if (isset($options['dirs']))
			$this->setDirs($options['dirs']);
		if (isset($options['classes']))
			$this->setClassPaths($options['classes']);
		if (isset($options['prepend']))
			$this->setPrepend($options['prepend']);
	}

	//////////////////////////////////////////////////////////////////////////////////
	// Class Loader相关
	//////////////////////////////////////////////////////////////////////////////////

	/**
	 * @param bool $isPrepend
	 * @return $this
	 */
	public function setPrepend(bool $isPrepend)
	{
		if (!$this->isStart) {
			$this->isPrepend = $isPrepend;
		}
		return $this;
	}

	public function start()
	{
		if (!$this->isStart) {
			$this->isStart = spl_autoload_register([$this, 'loadClass'], false, $this->isPrepend);
		}
		return $this;
	}

	public function stop()
	{
		if (!$this->isStart) {
			if (spl_autoload_unregister([$this, 'loadClass']))
				$this->isStart = false;
		}
		return $this;
	}

	/**
	 * 设定类路径，这里只能添加新的，而不能覆盖已经存在的Class路径
	 *
	 * @param array $paths
	 * @return $this
	 */
	public function setClassPaths(array $paths)
	{
		if (!empty($paths))
			$this->classPaths += $paths;
		return $this;
	}

	/**
	 * 通过加载文件的方式来添加类路径，支持传入数组和字符两种格式
	 *
	 * @param \string[] ...$files
	 * @return $this|Loader
	 */
	public function importClassPaths(string ...$files)
	{
		if (empty($files))
			return $this;
		$paths = import($files, null, KE_IMPORT_ARRAY);
		if (!empty($paths) && is_array($paths)) {
			return $this->setClassPaths($paths);
		}
		return $this;
	}

	/**
	 * 返回所有已定义的Class路径映射
	 *
	 * @return array
	 */
	public function getClassPaths(): array
	{
		return $this->classPaths;
	}

	/**
	 * @return array
	 */
	public function getLoadClasses()
	{
		return $this->loadClasses;
	}

	/**
	 * @return array
	 */
	public function getLoadHelpers()
	{
		return $this->loadHelpers;
	}

	/**
	 * @param string $class
	 * @return string
	 */
	public function getClassPath(string $class): string
	{
		$path = isset($this->classPaths[$class]) ? $this->classPaths[$class] : $this->seek(null, $class);
		return $path;
	}

	public function loadClass(string $class)
	{
		$class = trim($class, KE_PATH_NOISE);
		// !isset($this->classLoaded[$class]) || $this->classLoaded[$class] === false
		if (empty($this->loadClasses[$class])) {
			$path = $this->getClassPath($class);
			$status = self::NOT_LOADED;
			if ($path !== false && import($path) !== false)
				$status = self::LOADED;

			if ($status === self::LOADED) {
//				if (class_exists($class, false)) {
////					if (is_subclass_of($class, AutoLoadClassImpl::class))
////						call_user_func([$class, 'onLoadClass'], $class, $path);
//					// 这里预留一个空间，这里会加入一些接口的实现
//				}
//				elseif (trait_exists($class, false)) {
//
//				}
//				else {
//					// 如果类不存在，将其记录下来，因为PSR的规范，loadClass不应该抛出异常或错误
//					$status = self::UNDEFINED;
//				}
			}
			$this->loadClasses[$class] = $status;
		}
	}

	public function loadHelper(string ...$names)
	{
		foreach ($names as $name) {
			if (!empty($this->loadHelpers[$name]))
				continue;
			$paths = $this->seek(self::HELPER, $name, true);
			$this->loadHelpers[$name] = import($paths, null, KE_IMPORT_PATH);
		}
		return $this;
	}

//	public function loadHelper(string ...$helpers)
//	{
//		$paths = [];
//		foreach ($helpers as $helper) {
//			if (!empty($this->loadedHelpers[$helper]))
//				continue;
//			$this->loadedHelpers[$helper] = $paths[$helper] = $this->seek(self::HELPER, $helper, true);
//		}
//		if (!empty($paths))
//			import($paths);
//		return $this;
//	}


}