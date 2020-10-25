<?php

/**
 * 路由解析类
 * 
 * @author ShuangYa
 * @package SYFramework
 * @category Library
 * @link http://www.sylingd.com/
 * @copyright Copyright (c) 2017 ShuangYa
 * @license http://lab.sylingd.com/go.php?name=framework&type=license
 */

namespace sy\base;
use \Sy;
use \sy\lib\Plugin;

class Router {
	//路由参数名称
	public static $routeParam = 'r';
	public static $routeParamM = 'm';
	public static $routeParamC = 'c';
	public static $routeParamA = 'a';
	//其他参数
	public static $routerType = '';
	public static $defaultModule = '';
	/**
	 * 解析路由
	 * @access public
	 * @return array
	 */
	public static function getRoute() {
		//根据路由参数类型进行处理
		switch (self::$routerType) {
			case 'map':
				//根据REQUEST_URI解析
				$uri = ltrim($_SERVER['REQUEST_URI'], '/');
				if (strpos('?', $uri) !== FALSE) {
					$uri = substr($uri, 0, strpos($uri, '?'));
				}
				$uri = explode('/', $uri, 3);
				if (count($uri) === 3) {
					$result = [
						'module' => $uri[0],
						'controller' => $uri[1],
						'action' => $uri[2]
					];
				} else {
					$result = [
						'controller' => $uri[0],
						'action' => $uri[1]
					];
				}
				//移除扩展名
				if (($dot = strrpos($result['action'], '.')) !== FALSE) {
					$result['action'] = substr($result['action'], 0, $dot);
				}
				break;
			case 'simple':
				//直接从参数获取路由信息
				$result = [
					'controller' => $_GET[self::$routeParamC],
					'action' => $_GET[self::$routeParamA]
				];
				if (isset($_GET[self::$routeParamM])) {
					$result['module'] = $_GET[self::$routeParamM];
				}
				break;
			case 'supervar':
			default:
				$uri = $_GET[self::$routeParam];
				$uri = explode('/', $uri, 3);
				if (count($uri) === 3) {
					$result = [
						'module' => $uri[0],
						'controller' => $uri[1],
						'action' => $uri[2]
					];
				} else {
					$result = [
						'controller' => $uri[0],
						'action' => $uri[1]
					];
				}
				break;
		}
		//进行其他判断
		if (!isset($result['module'])) {
			$result['module'] = self::$defaultModule;
		}
		if (empty($result['controller'])) {
			$result['controller'] = 'index';
		}
		if (empty($result['action'])) {
			$result['action'] = 'index';
		}
		return $result;
	}
	/**
	 * 简单Router
	 * @access public
	 */
	public static function router($route) {
		if (!self::isValidModule($route['module'])) {
			header(Sy::getHttpStatus('404'));
			Plugin::trigger('error404');
			exit;
		}
		//获取操作类
		$controller = self::getController($route['module'], $route['controller']);
		if (NULL === $controller) {
			header(Sy::getHttpStatus('404'));
			Plugin::trigger('error404');
			exit;
		}
		//执行动作
		$actionName = 'action' . ucfirst($route['action']);
		if (!method_exists($controller, $actionName)) {
			header(Sy::getHttpStatus('404'));
			Plugin::trigger('error404');
			exit;
		}
		call_user_func([$controller, $actionName]);
	}
	/**
	 * 创建URL
	 * @access public
	 * @param mixed $param URL参数
	 * @param string $ext 自定义扩展名
	 * @return string
	 */
	public static function createUrl($param = '', $ext = NULL) {
		$param = (array)$param;
		$route = $param[0];
		$anchor = isset($param['#']) ? '#' . $param['#'] : '';
		unset($param['#']);
		//Plugin
		$PluginResult = Plugin::trigger('createUrl', [$route, $anchor, $param, $ext]);
		if (is_string($PluginResult)) {
			return $PluginResult;
		}
		//基本URL
		if (empty($route)) {
			return Sy::$sitePath;
		}
		$url = Sy::$sitePath;
		unset($param[0]);
		//是否启用了Rewrite
		if (Sy::$app->get('rewrite.enable')) {
			if (($rule = Sy::$app->get('rewrite.rule.' . str_replace('/', '_', $route))) !== NULL) {
				$url = str_replace('@root/', Sy::$sitePath, $rule);
				foreach ($param as $k => $v) {
					$k_tpl = '{{' . $k . '}}';
					if (strpos($url, $k_tpl) === FALSE) {
						continue;
					}
					$url = str_replace($k_tpl, $v, $url);
					//去掉此参数，防止后面http_build_query重复
					unset($param[$k]);
				}
			} else {
				$url .= $route;
				if ($ext !== NULL || Sy::$app->has('rewrite.ext')) {
					$url .= '.' . ($ext === NULL ? Sy::$app->get('rewrite.ext') : $ext);
				}
			}
		} else {
			switch (self::$routerType) {
				case 'simple':
					//直接从参数获取路由信息
					$route = explode('/', $route, 3);
					if (count($route) === 3) {
						$param[self::$routeParamM] = $route[0];
						$param[self::$routeParamC] = $route[1];
						$param[self::$routeParamA] = $route[2];
					} else {
						$param[self::$routeParamC] = $route[0];
						$param[self::$routeParamA] = $route[1];
					}
					break;
				case 'supervar':
				default:
					$param[self::$routeParam] = $route;
					break;
			}
		}
		if (count($param) > 0) {
			if (strpos($url, '?') === FALSE) {
				$url .= '?';
			} else {
				$url .= '&';
			}
			$url .= http_build_query($param);
		}
		$url .= $anchor;
		return $url;
	}
	/**
	 * 判断是否为一个合法的module
	 * @access public
	 * @param string $module
	 * @return boolean
	 */
	public static function isValidModule($module) {
		static $modules = NULL;
		if ($modules === NULL) {
			$modules = Sy::$app->get('router.modules');
			if (is_string($modules)) {
				$modules = explode(',', $modules);
			}
		}
		return in_array($module, $modules, TRUE);
	}
	/**
	 * 获取Controller操作类
	 * @access public
	 * @param string $controllerName
	 * @return object
	 */
	public static function getController($module, $controllerName) {
		if (strpos('/', $controllerName) !== FALSE) {
			return NULL;
		}
		//初始化Controller
		$fullPath = Sy::$appDir . 'modules/' . $module . '/controllers/' . $controllerName . '.php';
		$className = '\\' . Sy::$app->get('appNamespace') . '\\controller\\' . ucfirst($controllerName);
		if (!is_file($fullPath)) {
			return NULL;
		}
		if (!class_exists($className, FALSE)) {
			require($fullPath);
		}
		$controller = new $className;
		$controller->_sy_module = $module;
		return $controller;
	}
}