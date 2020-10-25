<?php
/**
 * 路由解析类
 * 
 * @author ShuangYa
 * @package Yesf
 * @category Base
 * @link https://www.sylingd.com/
 * @copyright Copyright (c) 2017-2019 ShuangYa
 * @license https://yesf.sylibs.com/license.html
 */
namespace Yesf\Http;

use Yesf\Yesf;

class Router implements RouterInterface {
	/** @var string $prefix URL prefix */
	private $prefix = '';
	/** @var string $module Default module */
	private $module;
	/** @var array $routes All route rules */
	private $routes;
	/** @var bool $enable_map Enable map parser */
	private $enable_map;
	/** @var bool $enable_extension Enable extension parser */
	private $enable_extension;
	public function __construct() {
		$this->routes = [];
		$this->enable_map = Yesf::app()->getConfig('router.map', Yesf::CONF_PROJECT, true);
		$this->enable_extension = Yesf::app()->getConfig('router.extension', Yesf::CONF_PROJECT, false);
		$this->module = Yesf::app()->getConfig('module', Yesf::CONF_PROJECT);
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
	public function add($type, $rule, $action, $options = null) {
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
		if (!isset($this->routes[$type])) {
			$this->routes[$type] = [];
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
		$this->routes[$type][] = $add;
	}
	public function any($rule, $action, $options = null) {
		$this->add(__FUNCTION__, $rule, $action, $options);
	}
	public function get($rule, $action, $options = null) {
		$this->add(__FUNCTION__, $rule, $action, $options);
	}
	public function post($rule, $action, $options = null) {
		$this->add(__FUNCTION__, $rule, $action, $options);
	}
	public function put($rule, $action, $options = null) {
		$this->add(__FUNCTION__, $rule, $action, $options);
	}
	public function delete($rule, $action, $options = null) {
		$this->add(__FUNCTION__, $rule, $action, $options);
	}
	public function head($rule, $action, $options = null) {
		$this->add(__FUNCTION__, $rule, $action, $options);
	}
	public function options($rule, $action, $options = null) {
		$this->add(__FUNCTION__, $rule, $action, $options);
	}
	public function connect($rule, $action, $options = null) {
		$this->add(__FUNCTION__, $rule, $action, $options);
	}
	/**
	 * Set url prefix
	 * 
	 * @access public
	 * @param string $prefix
	 */
	public function setPrefix($prefix = '') {
		$this->prefix = $prefix;
	}
	/**
	 * Parse request in map way
	 * 
	 * @access private
	 * @param Request $request
	 * @return bool
	 */
	private function parseMap(Request $request) {
		//解析
		$res = explode('/', trim($request->uri, '/'), 3);
		if (count($res) === 3) {
			$request->module = $res[0];
			$request->controller = $res[1];
			$request->action = $res[2];
		} else {
			$request->module = $this->module;
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
	private function parseBy($rules, Request $request) {
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
			$request->module = isset($dispatch['module']) ? $dispatch['module'] : $this->module;
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
	public function enableMap() {
		$this->enable_map = true;
	}
	/**
	 * Disable map parser
	 * 
	 * @access public
	 */
	public function disableMap() {
		$this->enable_map = false;
	}
	/**
	 * Parse a request
	 * 
	 * @access public
	 * @param Request $request
	 */
	public function parse(Request $request) {
		$len = strlen($this->prefix);
		//路由解析
		$uri = $request->server['request_uri'];
		if (strpos($uri, '?') !== false) {
			$uri = substr($uri, 0, strpos($uri, '?'));
		}
		//去除开头的prefix
		if ($len > 0 && strpos($uri, $this->prefix) === 0) {
			$uri = substr($uri, $len);
		}
		$request->uri = $uri;
		$res = false;
		$method = strtolower($request->server['request_method']);
		if (isset($this->routes[$method])) {
			$res = $this->parseBy($this->routes[$method], $request);
		}
		if ($res === false && isset($this->routes['any'])) {
			$res = $this->parseBy($this->routes['any'], $request);
		}
		if ($res === false) {
			if ($this->enable_map) {
				//为空则读取默认设置
				if (empty($uri) || $uri === '/') {
					$request->module = $this->module;
					$request->controller = 'index';
					$request->action = 'index';
				} else {
					if ($this->enable_extension) {
						$hasPoint = strrpos($uri, '.');
						if ($hasPoint !== false) {
							$request->extension = substr($uri, $hasPoint + 1);
							$uri = substr($uri, 0, $hasPoint);
							$request->uri = $uri;
						}
					}
					$this->parseMap($request);
				}
			}
		}
	}
}