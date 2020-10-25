<?php
/**
 * Request
 * 
 * @author ShuangYa
 * @package SYFramework
 * @category Library
 * @link https://www.sylibs.com/
 * @copyright Copyright (c) 2015-2019 ShuangYa
 * @license https://syframework.sylibs.com/license.html
 */
namespace Sy\Http;

use Sy\App;
use Sy\Exception\Exception;

class Request {
	/** @var object $session Cached session */
	private $session = null;

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
	
	/** @var array Same as php */
	public $get;
	public $post;
	public $server;
	public $cookie;
	public $files;

	/** @var string $extension Extension */
	public $extension;

	public function __construct() {
		$this->get = &$_GET;
		$this->post = &$_POST;
		$this->server = &$_SERVER;
		$this->cookie = &$_COOKIE;
		$this->files = &$_FILES;
	}
	/**
	 * Get original post body
	 * 
	 * @access public
	 * @return string
	 */
	public function rawContent() {
		return file_get_contents('php://input');
	}
	/**
	 * Get session
	 * 
	 * @access public
	 * @return object
	 */
	public function session() {
		if ($this->session === null) {
			$this->session = new Session();
		}
		return $this->session;
	}
}