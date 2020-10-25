<?php
/**
 * Session
 * 
 * @author ShuangYa
 * @package SYFramework
 * @category Library
 * @link https://www.sylibs.com/
 * @copyright Copyright (c) 2015-2019 ShuangYa
 * @license https://syframework.sylibs.com/license.html
 */
namespace Sy\Http;

use ArrayAccess;

class Session implements ArrayAccess {
	/** @var string $id Session ID */
	private $id;

	/**
	 * Constructor
	 * 
	 * @access public
	 * @param string $id Session ID
	 * @param string $sess Saved session content
	 */
	public function __construct() {
		session_start();
	}
	public function id() {
		return session_id();
	}
	public function has($offset) {
		return isset($_SESSION[$offset]);
	}

	public function get($offset) {
		return isset($_SESSION[$offset]) ? $_SESSION[$offset] : null;
	}
	
	public function set($offset, $value) {
		$_SESSION[$offset] = $value;
	}
	
	public function delete($offset) {
		unset($_SESSION[$offset]);
	}

	public function clear() {
		$_SESSION = [];
	}

	/**
	 * ArrayAccess
	 */
	public function offsetExists($offset) {
		return $this->has($offset);
	}

	public function offsetGet($offset) {
		return $this->get($offset);
	}

	public function offsetSet($offset, $value) {
		$this->set($offset, $value);
	}

	public function offsetUnset($offset) {
		$this->delete($offset);
	}
}