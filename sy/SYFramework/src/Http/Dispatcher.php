<?php
/**
 * 请求分发类
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
use Sy\Plugin;
use Sy\DI\Container;
use Sy\DI\EntryUtil;
use Sy\Exception\NotFoundException;

class Dispatcher {
	const ROUTE_VALID = 0;
	const ROUTE_ERR_MODULE = 1;
	const ROUTE_ERR_CONTROLLER = 2;
	const ROUTE_ERR_ACTION = 3;

	/** @var array $modules Avaliable modules */
	private static $modules;

	public static function init() {
		self::$modules = App::$config->get('modules');
		if (is_string(self::$modules)) {
			self::$modules = explode(',', self::$modules);
		}
	}
	/**
	 * 判断路由是否合法
	 * @codeCoverageIgnore
	 * @param string $module
	 * @param string $controller
	 * @param string $action
	 * @return int
	 */
	public static function isValid($module, $controller, $action) {
		if (!in_array($module, self::$modules, true)) {
			return self::ROUTE_ERR_MODULE;
		}
		$className = EntryUtil::controller($module, $controller);
		if (!Container::getInstance()->has($className)) {
			return self::ROUTE_ERR_CONTROLLER;
		}
		$clazz = Container::getInstance()->get($className);
		if (!method_exists($clazz, $action . 'Action')) {
			return self::ROUTE_ERR_ACTION;
		}
		return self::ROUTE_VALID;
	}
	/**
	 * Handle http request
	 * 
	 * @access public
	 */
	public static function handleRequest() {
		$request = new Request();
		Router::parse($request);
		self::dispatch($request);
	}
	/**
	 * 进行路由分发
	 * 
	 * @access public
	 * @param object $request
	 * @return mixed
	 */
	public static function dispatch(Request $request) {
		$module = ucfirst($request->module);
		$controller = ucfirst($request->controller);
		$action = ucfirst($request->action);
		if (!empty($request->extension)) {
			$response->mimeType($request->extension);
		}
		//触发beforeDispatch事件
		if (null === Plugin::trigger('beforeDispatch', [$request])) {
			try {
				$code = self::isValid($module, $controller, $action);
				if ($code === self::ROUTE_VALID) {
					//是否启用CSRF验证
					if (App::$config->get('csrf')) {
						Security::csrfSetCookie();
					}
					$className = EntryUtil::controller($module, $controller);
					$clazz = Container::getInstance()->get($className);
					$actionName = $action . 'Action';
					$result = $clazz->$actionName($request);
					if ($result !== null) {
						if (is_array($result) || is_object($result)) {
							$result = json_encode($result);
						}
						echo $result;
					} else {
						$clazz->end($request);
					}
					//触发afterDispatch事件
					Plugin::trigger('afterDispatch', [$request, $result]);
				} else {
					// Not found
					self::handleNotFound($request, $code);
				}
			} catch (\Throwable $e) {
				self::handleDispathException($request, $e);
			}
		}
		unset($request);
	}
	private static function handleNotFound($request, $code) {
		if (Plugin::trigger('dispatchFailed', [$request]) === null) {
			header(Vars::getStatus(404));
			$template = new Template();
			if (App::getEnv() === 'develop') {
				$template->assign('module', $request->module);
				$template->assign('controller', $request->controller);
				$template->assign('action', $request->action);
				$template->assign('code', $code);
				$template->assign('request', $request);
				echo $template->render(SY_PATH . 'Data/error_404_debug.php');
			} else {
				echo $template->render(SY_PATH . 'Data/error_404.php');
			}
		}
	}
	private static function handleDispathException($request, $exception) {
		//触发失败事件
		if (Plugin::trigger('dispatchFailed', [$request, $exception]) === null) {
			header(Vars::getStatus(500));
			$template = new Template();
			//如果用户没有自行处理，输出默认模板
			if (App::getEnv() === 'develop') {
				$template->assign('module', $request->module);
				$template->assign('controller', $request->controller);
				$template->assign('action', $request->action);
				$template->assign('exception', $exception);
				$template->assign('request', $request);
				echo $template->render(SY_PATH . 'Data/error_debug.php');
			} else {
				echo $template->render(SY_PATH . 'Data/error.php');
			}
		}
	}
}