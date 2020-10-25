<?php
/**
 * 基本类
 * 
 * @author ShuangYa
 * @package SYFramework
 * @category Base
 * @link https://www.sylibs.com/
 * @copyright Copyright (c) 2015-2019 ShuangYa
 * @license https://syframework.sylibs.com/license.html
 */
namespace Sy;

use ReflectionClass;
use Sy\Plugin;
use Sy\DI\Container;
use Sy\DI\EntryUtil;
use Sy\DB\DBInterface;
use Sy\Http\Router;
use Sy\Http\Dispatcher;
use Sy\Exception\Exception;
use Sy\Exception\StartException;
use Sy\Config\ConfigInterface;
use Sy\Config\Adapter\Arr;

if (!defined('SY_PATH')) {
	define('SY_PATH', __DIR__ . '/');
}

class App {
	protected static $environment = 'product';
	//CLI模式
	public static $isCli = FALSE;
	//应用相关设置
	public static $config;
	public static $sitePath;
	//应用namespace
	public static $cfgNamespace = NULL;
	/**
	 * 初始化：创建Application（通用）
	 * @access protected
	 * @param object $config设置
	 */
	protected static function createInit($config) {
		//PHP运行模式
		if (PHP_SAPI === 'cli') {
			self::$isCli = TRUE;
		}
		if (!defined('APP_PATH')) {
			throw new StartException('You must define APP_PATH');
		}
		if (defined('APP_ENV')) {
			self::$environment = APP_ENV;
		}
		if (is_string($config) && is_file($config)) {
			$config = Arr::fromIniFile($config);
		}
		if (is_array($config)) {
			$config = new Arr($config);
		}
		if (!is_object($config) || !($config instanceof ConfigInterface)) {
			throw new StartException('Config can not be recognised');
		}
		//基本信息
		self::$config = $config;
		//编码相关
		if (function_exists('mb_internal_encoding')) {
			mb_internal_encoding($config->get('charset'));
		}
		//设置一些基本参数
		$namespace = $config->get('namespace');
		if ('\\' !== $namespace[strlen($namespace) - 1]) {
			throw new StartException("A non-empty PSR-4 prefix must end with a namespace separator.");
		}
		self::$cfgNamespace = $namespace;
		Dispatcher::init();
		Router::init();
		//init alias
		EntryUtil::initAlias();
		//Configuration
		self::callConfiguration();
	}
	/**
	 * 初始化：创建WebApplication
	 * @access public
	 * @param object $config设置
	 */
	public static function create($config) {
		self::createInit($config);
		//网站目录
		$now = $_SERVER['PHP_SELF'];
		$dir = str_replace('\\', '/', dirname($now));
		$dir !== '/' && $dir = rtrim($dir, '/') . '/';
		self::$sitePath = $dir;
		//调试模式
		if (self::getEnv() === 'develop' && function_exists('xdebug_start_trace')) {
			xdebug_start_trace();
		}
		//开始路由分发
		Dispatcher::handleRequest();
		if (self::getEnv() === 'develop' && function_exists('xdebug_stop_trace')) {
			xdebug_stop_trace();
		}
	}
	/**
	 * 初始化：创建ConsoleApplication
	 * @access public
	 * @param object $config设置
	 */
	public static function createConsole($config = NULL) {
		self::createInit($config);
		//网站目录
		self::$sitePath = '/';
		if (!self::$isCli) {
			throw new StartException('Must run at CLI mode');
		}
		//调试模式
		if (self::getEnv() === 'develop' && function_exists('xdebug_start_trace')) {
			xdebug_start_trace();
		}
		//开始
		if (class_exists(self::$cfgNamespace . 'Console')) {
			$className = self::$cfgNamespace . 'Console';
			$className::run(Container::getInstance());
		}
		if (self::getEnv() === 'develop' && function_exists('xdebug_stop_trace')) {
			xdebug_stop_trace();
		}
	}
	/**
	 * Configuration
	 * 
	 * @access private
	 */
	private static function callConfiguration() {
		$container = Container::getInstance();
		$className = self::$cfgNamespace . 'Configuration';
		if ($container->has($className)) {
			$clazz = $container->get($className);
			$methods = (new ReflectionClass($clazz))->getMethods();
			foreach ($methods as $method) {
				if (strpos($method->name, 'set') !== 0) {
					continue;
				}
				$params = $method->getParameters();
				$init_params = [];
				foreach ($params as $param) {
					$type = $param->getType();
					if (class_exists('ReflectionNamedType') && $type instanceof \ReflectionNamedType) {
						$typeName = $type->getName();
					} else {
						$typeName = $type->__toString();
					}
					if ($type->isBuiltin()) {
						$value = null;
						settype($value, $typeName);
						$init_params[] = $value;
					} else {
						$init_params[] = $container->get($typeName);
					}
				}
				$method->invokeArgs($clazz, $init_params);
			}
		}
	}
	public static function setEnv($env) {
		self::$environment = $env;
	}
	public static function getEnv() {
		return self::$environment;
	}
}
