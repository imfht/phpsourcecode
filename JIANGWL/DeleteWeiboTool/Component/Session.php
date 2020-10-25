<?php

/**
 * Created by PhpStorm.
 * Author: William
 * Date: 2016/9/12
 * Time: 21:10
 */
class Session
{
	private $prefix;

	public function __construct($prefix = '')
	{
		$this->prefix = $prefix;
		session_start();
	}

	public function get($name = '')
	{
		return $_SESSION[$this->prefix . $name];
	}

	public function set($name = '', $val = '')
	{
		$_SESSION[$this->prefix . $name] = $val;
	}

	public function del($name = '')
	{
		unset($_SESSION[$this->prefix . $name]);
	}

	public function delAll()
	{
		foreach ($_SESSION as $key => $session) {
			if (preg_match('/^' . $this->prefix . '/', $key)) {
				unset($_SESSION[$key]);
			}
		}
	}
}