<?php
namespace Yurun\Util;

class Event
{
	/**
	 * 注册事件(监听)
	 * @param string $event 事件名称
	 * @param mixed $callback 回调
	 * @param bool $first 是否优先执行，以靠后设置的为准
	 * @param bool $once 是否只执行一次
	 */
	public static function register($event, $callback, $first = false, $once = false)
	{
		return call_user_func_array([EventClass::getInstance(), 'register'], func_get_args());
	}

	/**
	 * 注册事件(监听)，register的别名
	 * @param string $event 事件名称
	 * @param mixed $callback 回调
	 * @param bool $first 是否优先执行，以靠后设置的为准
	 */
	public static function on($event, $callback, $first = false)
	{
		return call_user_func_array([EventClass::getInstance(), 'on'], func_get_args());
	}

	/**
	 * 注册一次性事件(监听)
	 * @param string $event 事件名称
	 * @param mixed $callback 回调
	 * @param boolean $first 是否优先执行，以靠后设置的为准
	 */
	public static function once($event, $callback, $first = false)
	{
		return call_user_func_array([EventClass::getInstance(), 'once'], func_get_args());
	}
	
	/**
	 * 触发事件
	 * @param name $event 事件名称
	 * @param array $params 参数
	 * @return mixed
	 */
	public static function trigger($event, $params = array())
	{
		return call_user_func_array([EventClass::getInstance(), 'trigger'], func_get_args());
	}

}