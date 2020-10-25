<?php
/**
 * 路由解析类
 * 
 * @author ShuangYa
 * @package SYFramework
 * @category Base
 * @link https://www.sylingd.com/
 * @copyright Copyright (c) 2015-2019 ShuangYa
 * @license https://syframework.sylibs.com/license.html
 */
namespace Sy\Http;

use Sy\App;

class Router {
	/** @var string $prefix URL prefix */
	private static $prefix = '';
	/** @var string $module Default module */
	private static $module;
	/** @var array $routes All route rules */
	private static $routes;
	/** @var bool $enable_map Enable map parser */
	private static $enable_map;
	/** @var bool $enable_extension Enable extension parser */
	private static $enable_extension;
	public static function init() {
		self::$routes = [];
		self::$enable_map = App::$config->get('router.map', true);
		self::$enable_extension = App::$config->get('router.extension', false);
		self::$module = App::$config->get('module', 'index');
	}
	/**
	 * Load from string
	 * 
	 * @access public
	 * @param string $str
	 */
	public static function from($str) {
		self::$routes = unserialize($str);
	}
	/**
	 * Dump to string
	 * 
	 * @access public
	 * @return string
	 */
	public static function dump() {
		return serialize(self::$routes);
	}
	/**
	 * Add rule
	 * 
	 * @access public
	 * @param string $type Request method
	 * @param string $rule Match rule
	 * @param mixed $action Dispath info
	 * @param array $options Param options
	 */
	public static function add($type, $rule, $action, $options = null) {
		$add = [];
		if (strpos($rule, '{') !== false) {
			$param = [];
			$regex = str_replace('/', '\\/', $rule);
			$regex = preg_replace_callback('/\{([a-zA-Z0-9_]+)\}/', function($matches) use (&$param, &$options) {
				$paramName = $matches[1];
				$param[] = $paramName;
				if (is_array($options) && isset($options[$paramName])) {
					return $options[$paramName];
				} else {
					return '([^\/]+)';
				}
			}, $regex);
			$regex = '/^' . $regex . '$/';
			$add['regex'] = $regex;
			$add['param'] = $param;
		} else {
			$add['uri'] = $rule;
		}
		if (!isset(self::$routes[$type])) {
			self::$routes[$type] = [];
		}
		if (is_string($action)) {
			$res = explode('.', $action, 3);
			if (count($res) === 3) {
				$add['dispatch'] = [
					'module' => $res[0],
					'controller' => $res[1],
					'action' => $res[2]
				];
			} else {
				$add['dispatch'] = [
					'controller' => $res[0],
					'action' => $res[1]
				];
			}
		} else {
			$add['dispatch'] = $action;
		}
		self::$routes[$type][] = $add;
	}
	public static function any($rule, $action, $options = null) {
		self::add(__FUNCTION__, $rule, $action, $options);
	}
	public static function get($rule, $action, $options = null) {
		self::add(__FUNCTION__, $rule, $action, $options);
	}
	public static function post($rule, $action, $options = null) {
		self::add(__FUNCTION__, $rule, $action, $options);
	}
	public static function put($rule, $action, $options = null) {
		self::add(__FUNCTION__, $rule, $action, $options);
	}
	public static function delete($rule, $action, $options = null) {
		self::add(__FUNCTION__, $rule, $action, $options);
	}
	public static function head($rule, $action, $options = null) {
		self::add(__FUNCTION__, $rule, $action, $options);
	}
	public static function options($rule, $action, $options = null) {
		self::add(__FUNCTION__, $rule, $action, $options);
	}
	public static function connect($rule, $action, $options = null) {
		self::add(__FUNCTION__, $rule, $action, $options);
	}
	/**
	 * Set url prefix
	 * 
	 * @access public
	 * @param string $prefix
	 */
	public static function setPrefix($prefix = '') {
		self::$prefix = $prefix;
	}
	/**
	 * Parse request in map way
	 * 
	 * @access private
	 * @param string $uri
	 * @return array
	 */
	private static function parseMap(Request $request) {
		//解析
		$res = explode('/', trim($request->uri, '/'), 3);
		if (count($res) === 3) {
			$request->module = $res[0];
			$request->controller = $res[1];
			$request->action = $res[2];
		} else {
			$request->module = self::$module;
			$request->controller = $res[0];
			$request->action = $res[1];
		}
		return true;
	}
	/**
	 * Parse request with given rules
	 * 
	 * @access private
	 * @param array $rules
	 * @param Request $request
	 * @return bool
	 */
	private static function parseBy($rules, Request $request) {
		foreach ($rules as $rewrite) {
			if (isset($rewrite['uri'])) {
				if ($request->uri === $rewrite['uri']) {
					$dispatch = $rewrite['dispatch'];
					break;
				}
			} else {
				if (preg_match($rewrite['regex'], $request->uri, $matches)) {
					$param = [];
					unset($matches[0]);
					//参数
					foreach ($rewrite['param'] as $k => $v) {
						$param[$v] = $matches[$k + 1];
					}
					$dispatch = $rewrite['dispatch'];
					if ($dispatch instanceof \Closure) {
						$dispatch = $dispatch($param);
						if ($dispatch === null) {
							unset($dispatch);
							continue;
						}
					}
					break;
				}
			}
		}
		if (isset($dispatch)) {
			$request->module = isset($dispatch['module']) ? $dispatch['module'] : self::$module;
			$request->controller = $dispatch['controller'];
			$request->action = $dispatch['action'];
			if (isset($param)) {
				$request->param = $param;
			}
			return true;
		}
		return false;
	}
	/**
	 * Enable map parser
	 * 
	 * @access public
	 */
	public static function enableMap() {
		self::$enable_map = true;
	}
	/**
	 * Disable map parser
	 * 
	 * @access public
	 */
	public static function disableMap() {
		self::$enable_map = false;
	}
	/**
	 * Parse a request
	 * 
	 * @access public
	 * @param Request $request
	 */
	public static function parse(Request $request) {
		$len = strlen(self::$prefix);
		//路由解析
		$uri = $request->server['REQUEST_URI'];
		if (strpos($uri, '?') !== false) {
			$uri = substr($uri, 0, strpos($uri, '?'));
		}
		//去除开头的prefix
		if ($len > 0 && strpos($uri, self::$prefix) === 0) {
			$uri = substr($uri, $len);
		}
		$request->uri = $uri;
		$res = false;
		$method = strtolower($request->server['REQUEST_METHOD']);
		if (isset(self::$routes[$method])) {
			$res = self::parseBy(self::$routes[$method], $request);
		}
		if ($res === false && isset(self::$routes['any'])) {
			$res = self::parseBy(self::$routes['any'], $request);
		}
		if ($res === false) {
			if (self::$enable_map) {
				//为空则读取默认设置
				if (empty($uri) || $uri === '/') {
					$request->module = self::$module;
					$request->controller = 'index';
					$request->action = 'index';
				} else {
					if (self::$enable_extension) {
						$hasPoint = strrpos($uri, '.');
						if ($hasPoint !== false) {
							$request->extension = substr($uri, $hasPoint + 1);
							$uri = substr($uri, 0, $hasPoint);
							$request->uri = $uri;
						}
					}
					self::parseMap($request);
				}
			}
		}
	}
}