<?php
/**
 * 请求分发类
 * 
 * @author ShuangYa
 * @package Yesf
 * @category Base
 * @link https://www.sylingd.com/
 * @copyright Copyright (c) 2017-2019 ShuangYa
 * @license https://yesf.sylibs.com/license.html
 */
namespace Yesf\Http;

use SessionHandlerInterface;
use Yesf\Yesf;
use Yesf\DI\Container;
use Yesf\DI\EntryUtil;
use Yesf\Exception\NotFoundException;
use Yesf\Http\Interceptor\BaseInterface;
use Yesf\Http\Interceptor\BeforeInterface;
use Yesf\Http\Interceptor\AfterInterface;
use Yesf\Http\Interceptor\DefaultInterceptor;

class Dispatcher {
	const ROUTE_VALID = 0;
	const ROUTE_ERR_MODULE = 1;
	const ROUTE_ERR_CONTROLLER = 2;
	const ROUTE_ERR_ACTION = 3;

	/** @var RouterInterface $router Router */
	private $router;

	/** @var SessionHandlerInterface $session_handler Session Handler */
	private $session_handler;

	/** @var array $modules Avaliable modules */
	private $modules;

	/** @var bool $static_enable Enable static handler */
	private $static_enable = false;
	/** @var string $static_prefix Static files url prefix */
	private $static_prefix = '';
	/** @var string $static_dir Static directory */
	private $static_dir = '';

	/** @var array $interceptor Interceptors */
	private $interceptor;

	public function __construct(RouterInterface $router, SessionHandlerInterface $session) {
		$this->router = $router;
		$this->session_handler = $session;
		$this->modules = Yesf::app()->getConfig('modules', Yesf::CONF_PROJECT);
		$this->interceptor = [
			'before' => [],
			'after' => []
		];
		$interceptor = Container::getInstance()->get(DefaultInterceptor::class);
		$this->addInterceptor('/**', $interceptor);

		$static = Yesf::app()->getConfig('static', Yesf::CONF_PROJECT);
		if ($static === true || (is_array($static) && $static['enable'])) {
			$this->static_enable = true;
			$this->static_prefix = isset($static['prefix']) ? $static['prefix'] : '/';
			$this->static_dir = isset($static['dir']) ? str_replace('@APP', APP_PATH, $static['dir']) : APP_PATH . '/Static';
			if (!is_dir($this->static_dir)) {
				throw new NotFoundException("Directory {$this->static_dir} not exists");
			}
			$this->static_dir = str_replace('\\', '/', $this->static_dir);
			if (substr($this->static_dir, -1) !== '/') {
				$this->static_dir .= '/';
			}
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
	public function isValid($module, $controller, $action) {
		if (!in_array($module, $this->modules, true)) {
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
	 * Set router
	 * 
	 * @access public
	 * @param RouterInterface $router Router
	 */
	public function setRouter(RouterInterface $router) {
		$this->router = $router;
	}
	/**
	 * Set session handler
	 * 
	 * @access public
	 * @param SessionHandlerInterface $handler Session handler
	 */
	public function setSessionHandler(SessionHandlerInterface $handler) {
		$this->session_handler = $handler;
		$handler->open('', '');
	}
	/**
	 * Get session handler
	 * 
	 * @access public
	 */
	public function getSessionHandler() {
		return $this->session_handler;
	}
	/**
	 * Add interceptor
	 * 
	 * @access public
	 * @param string $include
	 * @param object $interceptor
	 * @param array $exclude
	 */
	public function addInterceptor(string $include, BaseInterface $interceptor, $exclude = null) {
		$add = ['handler' => $interceptor];
		if ($include === '**' || $include === '/**') {
			$add['all'] = true;
		} elseif (strpos($include, '*') !== false) {
			$regex = str_replace('/', '\\/', $include);
			$regex = str_replace('\\/**', '(.*?)', $regex);
			$regex = str_replace('**', '(.*?)', $regex);
			$regex = preg_replace('/(?!\\.)\\*(?<=\\?)/', '([^\/]?)', $regex);
			$add['regex'] = '/^' . $regex . '$/';
		} else {
			$add['uri'] = $include;
		}
		if ($exclude !== null) {
			$add['exclude'] = $exclude;
		}
		if ($interceptor instanceof BeforeInterface) {
			array_unshift($this->interceptor['before'], $add);
		}
		if ($interceptor instanceof AfterInterface) {
			array_unshift($this->interceptor['after'], $add);
		}
	}
	/**
	 * Execute interceptor
	 * 
	 * @access private
	 * @param string $type
	 * @param object $request
	 * @param object $response
	 * @return bool
	 */
	private function executeInterceptor($type, Request $request, Response $response) {
		foreach ($this->interceptor[$type] as $it) {
			if (isset($it['all'])) {
				if ($it['handler']->{$type}($request, $response) !== null) {
					return true;
				}
			} elseif (isset($it['uri'])) {
				if ($request->uri === $it['uri']) {
					if ($it['handler']->{$type}($request, $response) !== null) {
						return true;
					}
				}
			} else {
				if (preg_match($it['regex'], $request->uri)) {
					if ($it['handler']->{$type}($request, $response) !== null) {
						return true;
					}
				}
			}
		}
		return null;
	}
	/**
	 * Handle http request
	 * 
	 * @access public
	 * @param Request $req Request
	 * @param Response $res Response
	 */
	public function handleRequest(Request $request, Response $response) {
		if ($this->static_enable) {
			$request->uri = $request->server['request_uri'];
			$uri = $request->server['request_uri'];
			if (strpos($uri, $this->static_prefix) === 0) {
				$uri = substr($uri, strlen($this->static_prefix));
			}
			$path = realpath($this->static_dir . $uri);
			if ($path !== false && strpos($path, $this->static_dir) === 0) {
				if ($this->executeInterceptor('before', $request, $response) !== null) {
					$request->end();
					$response->end();
					unset($request, $response);
					return;
				}
				$response->mimeType(pathinfo($path, PATHINFO_EXTENSION));
				$response->sendfile($path);
				$this->executeInterceptor('after', $request, $response);
				$request->end();
				$response->end();
				unset($request, $response);
				return;
			}
		}
		$this->router->parse($request);
		if ($this->executeInterceptor('before', $request, $response) !== null) {
			$request->end();
			$response->end();
			unset($request, $response);
			return;
		}
		$this->dispatch($request, $response);
	}
	/**
	 * 进行路由分发
	 * 
	 * @access public
	 * @param array $routeInfo 路由信息
	 * @param object $request 请求内容
	 * @param object $response 响应对象
	 * @return mixed
	 */
	public function dispatch(Request $request, Response $response) {
		$module = ucfirst($request->module);
		$controller = ucfirst($request->controller);
		$action = ucfirst($request->action);
		$response->setTemplate($controller . '/' . $action);
		$response->setTemplatePath(APP_PATH . 'Module/' . $module . '/View/');
		if (!empty($request->extension)) {
			$response->mimeType($request->extension);
		}
		try {
			$code = self::isValid($module, $controller, $action);
			if ($code === self::ROUTE_VALID) {
				$className = EntryUtil::controller($module, $controller);
				$clazz = Container::getInstance()->get($className);
				$actionName = $action . 'Action';
				$result = $clazz->$actionName($request, $response);
				if ($result !== null) {
					$response->result = $result;
				}
			} else {
				// Not found
				$request->status = $code;
			}
		} catch (\Throwable $e) {
			$request->status = $e;
		}
		$this->executeInterceptor('after', $request, $response);
		$request->end();
		$response->end();
		unset($request, $response);
	}
}