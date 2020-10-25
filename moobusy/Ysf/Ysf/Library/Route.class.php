<?php
namespace Ysf;
/**
 * App基类 
 */
class Route
{
	public static $controller;
	public static $action;
	
	/**
	 * init route
	 * @return [type] [description]
	 */
	public static function init()
	{
		#TODO number of ways
		switch (config('route/mode')) {
			case 'default':
			default:
				self::parse_default();
				break;
		}
		return true;
	}

	/**
	 * default route parse
	 * @return [type] [description]
	 */
	public static function parse_default()
	{
		$_c_flag = config('route/controller_flag');
		$_a_flag = config('route/action_flag');
		self::$controller = (isset($_GET[$_c_flag]) && !empty($_GET[$_c_flag])) ? $_GET[$_c_flag] : 'index';
		self::$action = (isset($_GET[$_a_flag]) && !empty($_GET[$_a_flag])) ? $_GET[$_a_flag] : 'index';
	}

	/**
	 * get controller
	 * @return [type] [description]
	 */
	public static function get_controller()
	{
		return self::$controller;
	}

	/**
	 * get action
	 * @return [type] [description]
	 */
	public static function get_action()
	{
		return self::$action;
	}
}