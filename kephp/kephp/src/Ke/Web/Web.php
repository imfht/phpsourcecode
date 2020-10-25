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


use Throwable;
use ReflectionClass;

use Ke\App;
use Ke\Uri;
use Ke\MimeType;
use Ke\OutputBuffer;
use Ke\Web\Route\Router;
use Ke\Web\Route\Result;
use Ke\Web\Render\Renderer;
use Ke\Web\Render\Error;

class Web
{

	/** @var Web */
	private static $web = null;

	private static $routes = [];

	///////// passed /////////

	/** @var OutputBuffer */
	public $ob = null;

	/** @var App */
	public $app = null;

	/** @var MimeType */
	public $mime = null;

	/** @var Http */
	public $http = null;

	/** @var Component */
	public $component = null;

	protected $defaultController = 'index';

	protected $defaultAction = 'index';

	protected $defaultFormat = 'html';

	protected $controllerNamespace = 'Controller';

	protected $controllerClassSuffix = 'Controller';

	/** @var Controller */
	protected $controller = null;

	protected $helpers = [];

	protected $autoDetectFormats = [];

	protected $statusCode = 200;

	protected $format = 'html';

	protected $headers = [];

	protected $params = [
		'head'       => '',
		'class'      => '',
		'controller' => '',
		'action'     => '',
		'tail'       => '',
		'format'     => '',
		'data'       => [],
	];

	private $tailSplit = false;

	/** @var UI */
	private $ui = null;

	private $asset = null;

	private $html = null;

	private $requireHelpers = ['string'];

	/** @var Router|null */
	private $router = null;

	/** @var Uri 网站基础的uri */
	private $baseUri = null;

	/** @var Uri 网站资源的uri */
	private $resourceUri = null;

	/** @var bool|Result */
	private $dispatch = false;

	/** @var Renderer */
	private $renderer = null;

	/** @var Error */
	private $errorRenderer = null;

	/** @var Context */
	private $context = null;

	private $isDebug = KE_APP_ENV === KE_DEV;


	/**
	 * @param Http $http
	 *
	 * @return $this
	 */
	final public static function getWeb(Http $http = null)
	{
		if (!isset(self::$web)) {
			self::$web = new static();
		}
		return self::$web;
	}

	public static function registerRoutes(array $routes)
	{
		if (!empty($routes)) {
			self::$routes += $routes;
			return true;
		}
		return false;
	}

	public static function updateRoute(string $name, array $route)
	{
		if (isset(self::$routes[$name])) {
			self::$routes[$name] = $route;
			return true;
		}
		return false;
	}

	public static function removeRoute(string $path)
	{
		if (isset(self::$routes[$path])) {
			unset(self::$routes[$path]);
			return true;
		}
		return false;
	}

	final public function __construct(Http $http = null)
	{
		// 绑定当前的默认的上下文环境实例
		if (!isset(self::$web))
			self::$web = $this;

		$this->app = App::getApp();
		if (!$this->app->isInit())
			$this->app->init();

		$this->ob = OutputBuffer::getInstance();
		if (KE_APP_MODE === KE_WEB)
			$this->ob->start('webStart');

		$this->mime = $this->app->getMime();
		$this->http = $http ?? Http::current();
		$this->component = (new Component())->setDirs([
			'appView'        => [$this->app->appNs('View'), 100, Component::VIEW],
			'appComponent'   => [$this->app->appNs('Component'), 100],
			'kephpComponent' => [$this->app->kephp('Ke/Component'), 1000],
		]);

		$this->prepare();
		$this->onConstruct();

	}

	final private function prepare()
	{
		// 加载helpers
		$helpers = $this->requireHelpers;
		if (!empty($this->helpers))
			array_push($helpers, ...$this->helpers);
		$this->loadHelper(...$helpers);
		// 初始化各种属性
		$this->setControllerNamespace($this->controllerNamespace);
		$this->setDefault($this->defaultController, $this->defaultAction);
		$this->setDefaultFormat($this->defaultFormat);
		if (KE_APP_MODE === KE_WEB) {
			register_shutdown_function(function () {
				$this->onExiting();
			});
			set_error_handler([$this, 'errorHandle']);
			set_exception_handler([$this, 'exceptionHandle']);
		}
	}

	protected function onExiting()
	{
	}

	protected function onConstruct()
	{
	}

	/**
	 * PHP错误的处理的接管函数
	 */
	public function errorHandle($err, $msg, $file, $line, $context)
	{
		$this->renderError([
			'code'    => $err,
			'message' => $msg,
			'file'    => $file,
			'line'    => $line,
			'time'    => date('Y-m-d H:i:s'),
			'trace'   => $this->isDebug() ? debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 0) : [],
		]);
		exit();
	}

	/**
	 * @param Throwable $thrown
	 */
	public function exceptionHandle(Throwable $thrown)
	{
		$this->renderError($thrown);
	}

	/**
	 * @return Error
	 */
	public function getErrorRenderer()
	{
		if (!isset($this->errorRenderer))
			$this->errorRenderer = new Error();
		return $this->errorRenderer;
	}

	public function setErrorRenderer(Error $renderer)
	{
		$this->renderer = $renderer;
		return $this;
	}

	public function renderError($error)
	{
		/** @var Error $renderer */
		$renderer = $this->getErrorRenderer()->setError($error);
		// onError允许接管错误的处理
		if ($this->onError($error, $renderer) !== false)
			$renderer->render();
		return $this;
	}

	protected function onError($error, Renderer $renderer)
	{
	}

	public function loadHelper(...$helpers)
	{
		$this->app->getLoader()->loadHelper(...$helpers);
		return $this;
	}

	###################################################
	# controller, namespace, action, format
	###################################################

	public function setControllerNamespace(string $namespace)
	{
		$namespace = trim($namespace, KE_PATH_NOISE);
		if (empty($namespace))
			$namespace = 'Controller';
		if (!empty(KE_APP_NS))
			$namespace = add_namespace($namespace, KE_APP_NS);
		$this->controllerNamespace = $namespace;
		return $this;
	}

	public function getControllerNamespace(): string
	{
		return $this->controllerNamespace;
	}

	public function filterController(string $controller, bool $returnDefault = true): string
	{
		$controller = strtolower(str_replace(['-', '.', '/'], ['_', '_', '\\'], $controller));
		$namespace = $this->getControllerNamespace();
		if (($namespaceLength = strlen($namespace)) > 0) {
			if (stripos($controller, $namespace . '\\') === 0)
				$controller = substr($controller, $namespaceLength + 1);
			$controller = str_replace('\\', '/', $controller);
		}
		if (empty($controller) && $returnDefault)
			$controller = $this->getDefaultController();
		return $controller;
	}

	public function filterAction(string $action, bool $returnDefault = true): string
	{
		$action = strtolower(str_replace(['-', '.', '/', '\\'], '_', trim($action, '_# ')));
		if (empty($action) && $returnDefault)
			$action = $this->getDefaultAction();
		return $action;
	}

	public function setDefault(string $controller = null, string $action = null)
	{
		if (isset($controller)) {
			$controller = $this->filterController($controller, false);
			if (empty($controller))
				$controller = 'index';
			$this->defaultController = $controller;
			if (!$this->isDispatch())
				$this->params['controller'] = $this->defaultController;
		}
		if (isset($action)) {
			$action = $this->filterAction($action, false);
			if (empty($action))
				$action = 'index';
			$this->defaultAction = $action;
			if (!$this->isDispatch())
				$this->params['action'] = $this->defaultAction;
		}
		return $this;
	}

	public function getDefaultController(): string
	{
		return $this->defaultController;
	}

	public function getDefaultAction(): string
	{
		return $this->defaultAction;
	}

	public function setDefaultFormat(string $format)
	{
		$format = trim(strtolower($format), KE_PATH_NOISE . '.');
		if (empty($format))
			$format = 'html';
		$this->defaultFormat = $format;
		if (!$this->isDispatch())
			$this->params['format'] = $this->defaultFormat;
		return $this;
	}

	public function getDefaultFormat(): string
	{
		return $this->defaultFormat;
	}

	###################################################
	# router, dispatch, getParams
	###################################################

	public function getRouter()
	{
		if (!isset($this->router)) {
			$this->router = new Router();
			if (!empty(self::$routes))
				$this->router->routes += self::$routes;
			$this->router->loadFile($this->app->config('routes', 'php'));
		}
		return $this->router;
	}

	public function isDispatch()
	{
		return $this->dispatch !== false;
	}

	public function isMatch()
	{
		return $this->dispatch !== false && $this->dispatch->matched;
	}

	public function shouldCliServerHandle()
	{
		$path = $this->http->path;
		$empty = empty($path) || $path === '/';
		if (!$empty && real_file($this->app->path('public', $path)) !== false) {
			return false;
		}
		return true;
	}

	public function dispatch()
	{
		if ($this->dispatch !== false)
			return $this;

		if (PHP_SAPI === 'cli-server') {
			if ($this->shouldCliServerHandle() === false) {
				$this->dispatch = true;
				return false;
			}
		}

		/** @var Result $result */
		$result = $this->getRouter()->routing($this->http);

		if (!$result->matched)
			throw new \Error("Router not matched!");

		$params = $this->filterRouterResult($result);

		if (!empty($params))
			$this->params = array_merge($this->params, $params);
		$class = $this->getControllerClass();

		if (!empty($this->autoDetectFormats[$this->params['format']]))
			$this->format = $this->params['format'];

		// 种种原因，我们不能允许controller的不命中
		// 因为整个涉及到很多基础的变量的获取
		// todo 但未来版本，还是希望实现不需要class命中的模式

		// 做法1，严格检查controller的class是否存在
		if (!class_exists($class, true))
			throw new \Error("Controller {$class} not found!");
		if (!is_subclass_of($class, Controller::class))
			throw new \Error("{$class} is not a controller class!");
		$reflection = new ReflectionClass($class);
		if (!$reflection->isInstantiable())
			throw new \Error("Class {$class} is not instantiable!");

		// 到这里才表示分发正确了
		$this->dispatch = true;
		/** @var Controller $controller */
		$this->controller = new $class();
		$this->controller->setReflection($reflection);
		$this->controller->action($this->params['action']);


		// 做法2，即使class不存在，也可以继续往下执行
//		$controller = null;
//		if (class_exists($class, true) && is_subclass_of($class, Controller::class)) {
//
//			if ($reflection->isInstantiable()) {
//				/** @var Controller $controller */
//				$controller = new $class();
//				$controller->setReflection($reflection);
//				$controller->action($params['action']);
//			}
//		}
		return $this;
	}

	public function filterRouterResult(Result $result): array
	{

		$params = [];
		$params['mode'] = $result->mode;
		$params['mapping'] = $result->matchedMapping;
		// controller过滤
		if (!empty($result->class)) {
			$params['class'] = $result->class;
			// 暂定
			$params['controller'] = $params['controllerPath'] = $result->matchController;
		} else {
			$controller = $this->filterController($result->controller, true);
			if ($result->mode === Router::MODE_CONTROLLER) {
				$params['controllerPath'] = $result->matchController;
			} else {
				$params['controllerPath'] = $result->matchPath;
				$params['controllerPath'] .= (empty($result->matchPath) ? '' : '/') . $controller;
//				if (!empty($result->controller))
//					$params['controllerPath'] .= (empty($result->matchPath) ? '' : '/') . $result->controller;
			}

			$namespace = $this->filterController($result->namespace, false);
			if (!empty($namespace))
				$controller = add_namespace($controller, $namespace, true, '/');
			$params['controller'] = $controller;
		}
		// action过滤
		$action = $this->filterAction($result->action, true);
		$params['action'] = $action;
		$params['actionPath'] = $params['controllerPath'];
		$slash = empty($params['controllerPath']) ? '' : '/';
		if ($result->mode !== Router::MODE_TRADITION) {
			if (!empty($result->matches[1]))
				$params['actionPath'] .= $slash . $result->matches[1];
			else
				$params['actionPath'] .= $slash . $action;
		} else {
			if ($params['mapping']) {
				$params['actionPath'] = $result->matches[1];
			} else {
				$params['actionPath'] .= $slash . $action;
			}
		}
//		if (!empty($result->matches[1]))
//			 . '/' . $result->matches[1];
//		else
//			$params['actionPath'] = $params['controllerPath'] . '/' . $action;
		// format
		if (!empty($result->format))
			$params['format'] = $result->format;
		// tail
		if ($result->tail !== '') {
			$params['tail'] = $result->tail;
		}
		// data
		if (!empty($result->data)) {
			$params['data'] = array_merge($this->params['data'], $result->data);
//			$params += $result->data;
		}
		return $params;
	}

	public function params(string $field = null, $default = null)
	{
		if (!isset($field))
			return $this->params;
		return $this->params[$field] ?? $default;
	}

	public function tail(int $index = -1, $default = null)
	{
		if ($index < 0)
			return $this->params['tail'];
		if ($this->tailSplit === false) {
			$this->tailSplit = explode('/', $this->params['tail']);
		}
		return $this->tailSplit[$index] ?? $default;
	}

	public function makeControllerClass(string $controller)
	{
		$class = path2class($controller);
		if (!empty($this->controllerClassSuffix) && stripos($class, $this->controllerClassSuffix) === false) {
			$class .= $this->controllerClassSuffix;
		}
		$class = add_namespace($class, $this->controllerNamespace);
		return $class;
	}

	public function getController()
	{
		return $this->params['controller'];
	}

	public function controllerLink(string $path = null, $query = null)
	{
		$uri = $this->params['controllerPath'];
		$path = trim($path, KE_PATH_NOISE);
		if ($path !== '') {
//			if (empty($uri))
//				$uri = $this->defaultController;
			$uri .= (empty($uri) ? '' : '/') . $path;
		}
		return $this->uri($uri, $query);
	}

	public function actionLink(string $path = null, $query = null)
	{
		$uri = $this->params['actionPath'];
		$path = trim($path, KE_PATH_NOISE);
		if ($path !== '') {
//			if (empty($uri))
//				$uri = $this->defaultController;
			$uri .= (empty($uri) ? '' : '/') . $path;
		}
		return $this->uri($uri, $query);
	}

	public function getAction()
	{
		return $this->params['action'];
	}

	public function getFormat()
	{
		return $this->params['format'];
	}

	public function isFormat(string $format)
	{
		return $this->params['format'] === $format;
	}

	public function getControllerClass()
	{
		if (!empty($this->params['class']))
			return $this->params['class'];
		return $this->makeControllerClass($this->params['controller']);
	}

	public function getControllerObject()
	{
		return $this->controller;
	}

	public function getActionView()
	{
		if (empty($this->params['controller']))
			return "{$this->params['action']}";
		return "{$this->params['controller']}/{$this->params['action']}";
	}

	###################################################
	# render
	###################################################

	public function onRender(Renderer $renderer)
	{
	}

	public function isRender()
	{
		if (empty($this->renderer))
			return false;
		return $this->renderer->isRender();
	}

	public function registerRenderer(Renderer $renderer, $assign = null)
	{
		if (empty($this->renderer)) {
			$this->renderer = $renderer;
			$this->onRender($renderer);
			if (isset($assign))
				$this->assign($assign);
		}
		return $this;
	}

	public function getRenderer()
	{
		return $this->renderer;
	}

	public function assign($key, $value = null)
	{
		$this->getContext()->assign($key, $value);
		return $this;
	}

	public function getContext()
	{
		if (!isset($this->context)) {
			if (isset($this->ui))
				$this->context = $this->ui->getContext();
			else
				$this->context = new Context($this);
		}
		return $this->context;
	}

	public function setContext(Context $context)
	{
		if (isset($this->context))
			$context->assign($this->context);
		$this->context = $context;
		return $this;
	}

	/**
	 * 获取一个零件(View\Widget\Layout)的路径
	 *
	 * @param string      $path
	 * @param string|null $scope
	 *
	 * @return bool|string
	 */
	public function getComponentPath(string $path, string $scope = null)
	{
		$path = trim($path, KE_PATH_NOISE . '.');
		if (empty($path))
			return false;
		$scope = $scope ?? Component::WIDGET;
		if (($index = strpos($path, '/')) > 0) {
			$pre = substr($path, 0, $index);
			if ($this->component->hasScope($pre)) {
				$path = substr($path, $index + 1);
				$scope = $pre;
			}
		}
		return $this->component->seek($scope, $path);
	}

	public function setFormat(string $format)
	{
		$this->format = $format;
		return $this;
	}

	public function setStatusCode(int $code)
	{
		$this->statusCode = $code;
		return $this;
	}

	public function addHeaders(array $headers)
	{
		$this->headers += $headers;
		return $this;
	}

	public function setHeaders(array $headers)
	{
		$this->headers = array_merge($this->headers, $headers);
		return $this;
	}

	public function sendHeaders(array $headers = null)
	{
		if (headers_sent()) {
			header_remove();
		}
		if (!empty($headers))
			$this->addHeaders($headers);
		if ($this->statusCode > 200 && $this->statusCode < 600)
			http_response_code($this->statusCode);
		$contentType = $this->mime->makeContentType($this->format);
		if (!empty($contentType))
			header("Content-Type: {$contentType}", true);
		if (!empty($this->headers)) {
			foreach ($this->headers as $field => $header) {
				if (!empty($header) && is_string($header)) {
					header($header);
				}
			}
		}
		return $this;
	}

	###################################################
	# misc
	###################################################

	/**
	 * @param \Ke\Uri $uri
	 *
	 * @return $this
	 */
	public function setBaseUri(Uri $uri)
	{
		$this->baseUri = $uri;
		return $this;
	}

	/**
	 * @return \Ke\Uri
	 */
	public function getBaseUri()
	{
		if (!isset($this->baseUri)) {
			$this->baseUri = new Uri([
				'scheme' => KE_REQUEST_SCHEME,
				'host'   => KE_REQUEST_HOST,
				'uri'    => KE_HTTP_BASE,
			]);
		}
		return $this->baseUri;
	}

	/**
	 * @param      $uri
	 * @param null $query
	 *
	 * @return \Ke\Uri
	 */
	public function uri($uri, $query = null)
	{
		return $this->getBaseUri()->newUri($uri, $query);
	}

	public function setDebug(bool $debug)
	{
		$this->isDebug = $debug;
		return $this;
	}

	public function isDebug(): bool
	{
		return $this->isDebug;
	}

	public function getAsset()
	{
		if (!isset($this->asset) || !($this->asset instanceof Asset)) {
			$this->asset = Asset::getInstance();
		}
		return $this->asset;
	}

	public function setAsset(Asset $asset)
	{
		$this->asset = $asset;
		return $this;
	}

	public function asset($src, string $type = null, array $props = null)
	{
		$this->getAsset()->load($src, $type, $props);
		return $this;
	}

	public function getHtml()
	{
		if (!isset($this->html)) {
			if (isset($this->ui))
				$this->html = $this->ui->getHtml();
			else
				$this->html = Html::getInstance();
		}
		return $this->html;
	}

	public function setHtml(Html $html)
	{
		$this->html = $html;
		return $this;
	}

	public function setUI(UI $ui)
	{
		$this->ui = $ui;
		return $this;
	}

	public function getUI()
	{
		return $this->ui;
	}

	public function isAction(string $action)
	{
		return $action === $this->params['action'];
	}

	public function isController(string $controller)
	{
		return $controller === $this->params['controller'];
	}

	public function is(string $name)
	{
		if (empty($name))
			return false;
		$parse = $this->getRouter()->parseStr($name);
		if (isset($parse['controller']) && empty($parse['action']))
			return false;
		if (isset($parse['controller']) && !$this->isController($parse['controller']))
			return false;
		if (isset($parse['action']) && !$this->isAction($parse['action']))
			return false;
		return true;
	}

	public function in(string ...$names)
	{
		foreach ($names as $name)
			if ($this->is($name))
				return true;
		return false;
	}

//	// 暂时不要这个
//	public function useTheme(string $name, int $priority = 0)
//	{
//		if (!empty($this->theme))
//			$this->removeTheme($name);
//		$this->component->setDirs([
//			"{$name}-appView"       => [$this->component->dir('appView') . "/{$name}", $priority, Component::VIEW],
//			"{$name}-appComponent"  => [$this->component->dir('appComponent') . "/{$name}", $priority],
//			"{$name}-kephpComponent" => [$this->component->dir('kephpComponent') . "/{$name}", $priority + 900],
//		]);
//		return $this;
//	}
//
//	public function removeTheme(string $name)
//	{
//
//	}
}
