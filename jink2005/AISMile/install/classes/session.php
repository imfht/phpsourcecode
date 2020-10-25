<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

/**
 * Manage session for install script
 */
class InstallSession
{
	protected static $_instance;

	public static function getInstance()
	{
		if (!self::$_instance)
			self::$_instance = new self();
		return self::$_instance;
	}

	public function __construct()
	{
		session_name('install_'.md5(__PS_BASE_URI__));
		session_start();
	}

	public function clean()
	{
		foreach ($_SESSION as $k => $v)
			unset($_SESSION[$k]);
	}

	public function &__get($varname)
	{
		if (isset($_SESSION[$varname]))
			$ref = &$_SESSION[$varname];
		else
		{
			$null = null;
			$ref = &$null;
		}
		return $ref;
	}

	public function __set($varname, $value)
	{
		$_SESSION[$varname] = $value;
	}

	public function __isset($varname)
	{
		return isset($_SESSION[$varname]);
	}

	public function __unset($varname)
	{
		unset($_SESSION[$varname]);
	}
}
