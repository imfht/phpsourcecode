<?php

/**
 * 基本类
 * 
 * @author ShuangYa
 * @package SYFramework
 * @category Base
 * @link http://www.sylingd.com/
 * @copyright Copyright (c) 2015-2016 ShuangYa
 * @license http://lab.sylingd.com/go.php?name=framework&type=license
 */

namespace sy;
use \sy\base\SYException;
use \sy\base\Router;
use \sy\lib\Plugin;

class BaseSY {
	//会从data下的相应文件读取
	public static $mimeTypes = NULL;
	public static $httpStatus = NULL;
	//调试模式
	public static $debug = TRUE;
	//CLI模式
	public static $isCli = FALSE;
	//应用相关设置
	public static $app;
	public static $appDir;
	public static $siteDir;
	public static $sitePath;
	public static $frameworkDir;
	//应用namespace
	public static $cfgAppNamespace = NULL;
	/**
	 * 初始化：创建Application（通用）
	 * @access protected
	 * @param object $config设置
	 */
	protected static function createApplicationInit($siteDir, $config) {
		//路径相关
		static::$siteDir = $siteDir . '/';
		static::$frameworkDir = __DIR__ . '/';
		//PHP运行模式
		if (PHP_SAPI === 'cli') {
			static::$isCli = TRUE;
		}
		if (is_string($config) && is_file($config)) {
			$config = new \sy\base\Config($config);
		}
		if (!is_object($config) || !($config instanceof \sy\base\Config)) {
			throw new SYException('Config can not be recognised', '10001');
		}
		//基本信息
		$config->replace('cookie.path', str_replace('@app/', $dir, $config->get('cookie.path')));
		static::$app = $config;
		//应用的绝对路径
		static::$appDir = $config->get('dir') . '/';
		if ($config->get('debug')) {
			static::$debug = $config->get('debug');
		}
		//编码相关
		if (function_exists('mb_internal_encoding')) {
			mb_internal_encoding($config->get('charset'));
		}
		//设置一些基本参数
		static::$cfgAppNamespace = $config->get('appNamespace');
		Router::$routerType = $config->get('router.type');
		Router::$defaultModule = $config->get('router.module');
	}
	/**
	 * 初始化：创建WebApplication
	 * @access public
	 * @param object $config设置
	 */
	public static function createApplication($siteDir, $config) {
		static::createApplicationInit($siteDir, $config);
		//网站目录
		$now = $_SERVER['PHP_SELF'];
		$dir = str_replace('\\', '/', dirname($now));
		$dir !== '/' && $dir = rtrim($dir, '/') . '/';
		static::$sitePath = $dir;
		//单元测试
		if (defined('SY_UNIT')) {
			return;
		}
		//是否启用CSRF验证
		if (static::$app->get('csrf')) {
			\sy\lib\Security::csrfSetCookie();
		}
		//调试模式
		if (static::$debug && function_exists('xdebug_start_trace')) {
			xdebug_start_trace();
		}
		//bootstrap
		if (is_file(static::$appDir . 'Bootstrap.php')) {
			require(static::$appDir . 'Bootstrap.php');
			$bootstrap = new \Bootstrap;
			call_user_func([$bootstrap, 'run']);
		}
		//开始路由分发
		$route = Plugin::trigger('routerStartup');
		if ($route === NULL) {
			$route = Router::getRoute();
		}
		$newRoute = Plugin::trigger('routerShutdown', [$route]);
		if (is_array($newRoute)) {
			Router::router($newRoute);
		} else {
			Router::router($route);
		}
		if (static::$debug && function_exists('xdebug_stop_trace')) {
			xdebug_stop_trace();
		}
	}
	/**
	 * 初始化：创建ConsoleApplication
	 * @access public
	 * @param object $config设置
	 */
	public static function createConsoleApplication($siteDir, $config = NULL) {
		static::createApplicationInit($siteDir, $config);
		//网站目录
		static::$sitePath = '/';
		if (!static::$isCli) {
			throw new SYException('Must run at CLI mode', '10005');
		}
		//仅支持参数方式运行
		$opt = getopt(static::$routeParam . ':');
		//以参数方式运行
		$run = $opt[static::$routeParam];
		if (!empty($run) && static::$app->has('console.' . $run)) {
			list($fileName, $callback) = static::$app->get('console.' . $run);
		} else {
			list($fileName, $callback) = static::$app->get('console.default');
		}
		require(static::$appDir . '/workers/' . $fileName);
		if (is_callable($callback)) {
			call_user_func($callback);
		}
	}
	/**
	 * 获取HTTP状态文字
	 * @access public
	 * @param string $status 状态码
	 */
	public static function getHttpStatus($status) {
		if (static::$httpStatus === NULL) {
			static::$httpStatus = require(static::$frameworkDir . 'data/httpStatus.php');
		}
		$version = ((isset($_SERVER['SERVER_PROTOCOL']) && $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.0') ? '1.0' : '1.1');
		if (isset(static::$httpStatus[$status])) {
			$statusText = static::$httpStatus[$status];
			return "HTTP/$version $status $statusText";
		} else {
			return "HTTP/$version $status";
		}
	}
	/**
	 * 自动加载类
	 * @access public
	 * @param string $className
	 */
	public static function autoload($className) {
		//判断是否为框架的class
		if (strpos($className, 'sy\\') === FALSE) {
			//是否为App自有class
			if (static::$app->has('class.' . $className)) {
				$fileName = str_replace('@app/', static::$appDir, static::$app->get('class.' . $className));
			} elseif (static::$cfgAppNamespace !== NULL && strpos($className, static::$cfgAppNamespace . '\\') === 0) {
				//namespace匹配
				if (strpos($className, static::$cfgAppNamespace . '\\model\\') === 0) {
					$fileName = static::$appDir . 'models/' . substr($className, strrpos($className, '\\') + 1) . '.php';
				} else {
					$fileName = substr($className, strlen(static::$cfgAppNamespace) + 1) . '.php';
					$fileName = static::$appDir . str_replace('\\', '/', $fileName);
				}
			} else {
				return;
			}
		} elseif (strpos($className, 'sy\\') === 0) {
			$fileName = substr($className, 3) . '.php';
			$fileName = static::$frameworkDir . str_replace('\\', '/', $fileName);
		} else {
			return;
		}
		if (is_file($fileName)) {
			require($fileName);
		}
	}
	/**
	 * 发送Content-type的header，也就是mimeType
	 * @access public
	 * @param string $type 可为文件扩展名，或者Content-type的值
	 */
	public static function setMimeType($type) {
		$mimeType = static::getMimeType($type);
		if ($mimeType === NULL) {
			$mimeType = $type;
		}
		$header = $mimeType . ';';
		if (in_array($type, ['js', 'json', 'atom', 'rss', 'xhtml'], TRUE) || substr($mimeType, 0, 5) === 'text/') {
			$header .= ' charset=' . static::$app->get('charset');
		}
		header('Content-type:' . $header);
	}
	/**
	 * 获取扩展名对应的mimeType
	 * @access public
	 * @param string $ext
	 * @return string
	 */
	public static function getMimeType($ext) {
		if (static::$mimeTypes === NULL) {
			static::$mimeTypes = require(static::$frameworkDir . 'data/mimeTypes.php');
		}
		$ext = strtolower($ext);
		return isset(static::$mimeTypes[$ext]) ? (static::$mimeTypes[$ext]) : null;
	}
}
