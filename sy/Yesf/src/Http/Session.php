<?php
/**
 * Session支持类
 * 
 * @author ShuangYa
 * @package Yesf
 * @category Base
 * @link https://www.sylingd.com/
 * @copyright Copyright (c) 2017-2019 ShuangYa
 * @license https://yesf.sylibs.com/license.html
 */
namespace Yesf\Http;

use ArrayAccess;

class Session implements ArrayAccess {
	/** @var string $id Session ID */
	private $id;

	/** @var array $sess Session content */
	private $sess;

	/**
	 * Generate session id
	 * Notice this function didn't check is this id already use or not
	 * 
	 * @access public
	 * @return string
	 */
	public static function generateId() {
		return bin2hex(random_bytes(10));
	}

	/**
	 * Constructor
	 * 
	 * @access public
	 * @param string $id Session ID
	 * @param string $sess Saved session content
	 */
	public function __construct($id, $sess = null) {
		$this->id = $id;
		if (!empty($sess)) {
			$this->sess = unserialize($sess);
		} else {
			$this->sess = [];
		}
	}
	public function id() {
		return $this->id;
	}
	public function has($offset) {
		return isset($this->sess[$offset]);
	}

	public function get($offset) {
		return isset($this->sess[$offset]) ? $this->sess[$offset] : null;
	}
	
	public function set($offset, $value) {
		$this->sess[$offset] = $value;
	}
	
	public function delete($offset) {
		unset($this->sess[$offset]);
	}

	public function clear() {
		$this->sess = [];
	}
	
	public function encode() {
		return serialize($this->sess);
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