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

use Ke\Uri;
use Ke\Utils\Status;
use ReflectionClass, ReflectionObject;
use Ke\Web\Render, Ke\Web\Render\Renderer;

class Controller
{

	/** @var ReflectionClass|ReflectionObject */
	private $_reflection = null;

	/** @var Web */
	protected $web = null;

	/** @var Http */
	protected $http = null;

	/** @var string */
	public $title = '';

	/** @var null */
	public $layout = null;

	final public function __construct()
	{
		$this->web = Web::getWeb();
		$this->http = $this->web->http;
		$this->onConstruct();
	}

	protected function onConstruct()
	{
	}

	/**
	 * @return ReflectionClass|ReflectionObject
	 */
	public function getReflection()
	{
		if (!isset($this->_reflection) || !($this->_reflection instanceof ReflectionClass))
			$this->_reflection = new ReflectionObject($this);
		return $this->_reflection;
	}

	public function setReflection(ReflectionClass $reflectionClass)
	{
		if (!isset($this->_reflection))
			$this->_reflection = $reflectionClass;
		return $this;
	}

	public function makeActions(string $action)
	{
		$actions = ['default' => $action];
		$method = $this->http->method;
		if ($method !== Http::GET) {
			$method = strtolower($method);
			$actions[$method] = "{$method}_{$action}";
		}
		// 暂时不要
//		$actions['render'] = "render_{$action}";
		return $actions;
	}

	protected function beforeAction(string $action)
	{

	}

	protected function onMissing(string $action)
	{
		throw new \Exception("Action {$action} not found!");
	}

	protected function getActionArgs(array $params): array
	{
		return $params['data'] ?? [];
	}

	public function action(string $action)
	{
		if (empty($action))
			throw new \Exception('Empty action name!');
		if ($this->web->isRender())
			return $this;
		$beforeReturn = $this->beforeAction($action);
		if ($beforeReturn === false || $this->web->isRender())
			return $this;
		$objectRef = $this->getReflection();
		$args = $this->getActionArgs($this->web->params());
		$methods = $this->makeActions($action);
		$actionReturn = null;
		$status = 0;
		foreach ($methods as $name => $method) {
			if ($objectRef->hasMethod($method)) {
				if ($name === 'default')
					$status = 1; // 方法存在
				$methodRef = $this->_reflection->getMethod($method);
				if ($name === 'default')
					$status = 2; //
				if (!$methodRef->isStatic() && is_callable([$this, $method]) && !$this->web->isRender()) {
					if ($name === 'default')
						$status = 3; //
					$return = call_user_func_array([$this, $method], $args);
					if ($return !== null && !($return instanceof Controller) && !($return instanceof Renderer))
						$actionReturn = $return;
				}
			}
			if ($status === 0 && $name === 'default') {
				$this->onMissing($method);
			}
		}

		$this->defaultReturn($actionReturn);

		return $this;
	}

	protected function defaultReturn($return = null)
	{
		if (!isset($return))
			$return = $this->web->getActionView();
		return $this->view($return);
	}

	protected function onRender(Renderer $renderer)
	{
	}

	final protected function rendering(Renderer $renderer)
	{
		if (!$this->web->isRender()) {
			$this->web->assign($this);
			$this->onRender($renderer);
		}
		return $renderer;
	}

	protected function view(string $view = null, string $layout = null)
	{
		if (!isset($layout))
			$layout = $this->layout;
		if (!$this->web->isRender())
			$this->rendering(new Render\View($view, $layout))->render();
		return $this;
	}

	protected function component(string $component, string $layout = null, array $vars = [])
	{
		if (!isset($layout))
			$layout = $this->layout;
		if (!$this->web->isRender()) {
			if (!empty($format))
				$this->web->setFormat($format);
			$text = $this->web->getContext()->loadComponent($component, $layout, $vars, true);
			$this->rendering(new Render\Text($text))->render();
		}
		return $this;
	}

	protected function text($text, $format = 'txt')
	{
		if (!$this->web->isRender()) {
			if (!empty($format))
				$this->web->setFormat($format);
			$this->rendering(new Render\Text($text))->render();
		}
		return $this;
	}

	protected function json($data)
	{
		$json = is_object($data) && is_callable([$data, 'toJSON']) ? $data->toJSON() : json_encode($data);
		return $this->text($json, 'json');
	}

	protected function status($status, $message = '', array $data = null)
	{
		if (!($status instanceof Status)) {
			$status = new Status($status, $message, $data);
		} else {
			if (!empty($message))
				$status->plusMessage($message);
			if (!empty($data))
				$status->setData($data);
		}
		return $this->json($status->export());
	}

	protected function redirect($uri = null, $query = null)
	{
		if (!$this->web->isRender()) {
			if (!($uri instanceof Uri))
				$uri = $this->web->uri($uri, $query);
			else if (!empty($query))
				$uri->mergeQuery($query);
			$this->rendering(new Render\Redirection($uri))->render();
		}
		return $this;
	}
}