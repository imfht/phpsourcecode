<?php
/**
 * HTTP请求封装
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
use Yesf\Utils;
use Yesf\DI\Container;

class Request {
	/** @var callback $cookie_handler Cookie handler */
	private $cookie_handler;

	/** @var object $sw_request Swoole request object */
	private $sw_request;

	/** @var array $extra_infos extra infos */
	private $extra_infos = [];

	/** @var object $session Cached session */
	private $session = null;

	/** @var string $extension Request extension name */
	public $extension = null;

	/** @var mixed $status Request status, 404, or exception */
	public $status = null;

	/** @var string $module Module */
	public $module = null;
	/** @var string $controller Controoler */
	public $controller = null;
	/** @var string $action Action */
	public $action = null;

	/** @var array $param Request params */
	public $param = [];

	/** @var string $uri Parsed request uri */
	public $uri = '';

	/** @var array Same as swoole */
	public $get;
	public $post;
	public $server;
	public $header;
	public $cookie;
	public $files;

	/** @var array $hooked */
	protected static $hooked = [];
	/** @var array $hook */
	protected $hook;

	public function __construct($req) {
		$this->sw_request = $req;
		$this->get = &$req->get;
		$this->post = &$req->post;
		$this->server = &$req->server;
		$this->header = &$req->header;
		$this->cookie = &$req->cookie;
		$this->files = &$req->files;
		$this->hook = [];
	}
	/**
	 * Get original post body
	 * 
	 * @access public
	 * @return string
	 */
	public function rawContent() {
		return $this->sw_request->rawContent();
	}
	/**
	 * Get uploaded files
	 * 
	 * @access public
	 * @return array
	 */
	public function file() {
		static $res = null;
		if ($res === null) {
			$res = [];
			foreach ($this->files as $v) {
				$res[] = new File($v);
			}
		}
		return $res;
	}
	/**
	 * Get session
	 * 
	 * @access public
	 * @return object
	 */
	public function session() {
		if ($this->session === null) {
			$name = Yesf::app()->getConfig('session.name', Yesf::CONF_ENV, 'YESFSESSID');
			$type = Yesf::app()->getConfig('session.type', Yesf::CONF_ENV, 'cookie');
			$handler = Container::getInstance()->get(Dispatcher::class)->getSessionHandler();
			if ($type === 'cookie') {
				if (!isset($this->cookie[$name])) {
					do {
						$id = Session::generateId();
					} while ($handler->read($id) !== '');
					$saved = '';
					Utils::call($this->cookie_handler, [$name, $id, 0, '/']);
				} else {
					$id = $this->cookie[$name];
					$saved = $handler->read($id);
				}
			} else {
				if (!isset($this->get[$name])) {
					do {
						$id = Session::generateId();
					} while ($handler->read($id) !== '');
					$saved = '';
				} else {
					$id = $this->get[$name];
					$saved = $handler->read($id);
				}
			}
			$this->session = new Session($id, $saved);
		}
		return $this->session;
	}
	/**
	 * Set cookie handler, used by session
	 * 
	 * @access public
	 * @param callable $handler Cookie handler
	 */
	public function setCookieHandler($handler) {
		$this->cookie_handler = $handler;
	}
	/**
	 * Set hook
	 * 
	 * @access public
	 * @param string $name
	 * @param callable $handler
	 */
	public static function hook($name, $handler) {
		self::$hooked[$name] = $handler;
	}
	public function __get($name) {
		if (isset($this->extra_infos[$name])) {
			return $this->extra_infos[$name];
		}
		if (isset($this->hook[$name])) {
			return $this->hook[$name];
		}
		if (isset(self::$hooked[$name])) {
			$res = Utils::call(self::$hooked[$name], [$this]);
			$this->hook[$name] = $res;
			return $res;
		}
		return null;
	}
	public function __isset($name) {
		return isset($this->extra_infos[$name]);
	}
	public function __set($name, $value) {
		$this->extra_infos[$name] = $value;
	}
	public function __unset($name) {
		unset($this->extra_infos[$name]);
	}
	/**
	 * Finish request, release resources
	 * 
	 * @access public
	 */
	public function end() {
		$this->get = null;
		$this->post = null;
		$this->server = null;
		$this->header = null;
		$this->cookie = null;
		$this->files = null;
		$this->sw_request = null;
		if ($this->session !== null) {
			$handler = Container::getInstance()->get(Dispatcher::class)->getSessionHandler();
			$handler->write($this->session->id(), $this->session->encode());
			$this->session = null;
		}
	}
	public function __destruct() {
		$this->end();
	}
}
