<?php
/**
 * Event主类
 * 
 * @author ShuangYa
 * @package Yesf
 * @category Base
 * @link https://www.sylingd.com/
 * @copyright Copyright (c) 2017-2019 ShuangYa
 * @license https://yesf.sylibs.com/license.html
 */
namespace Yesf;

class Event {
	protected static $events = [];
	/**
	 * 注册一个插件
	 * 
	 * @access public
	 * @param string $event 事件名称
	 * @param callable $callback 回调函数
	 */
	public static function listen(string $event, callable $callback) {
		if (!isset(self::$events[$event])) {
			self::$events[$event] = [];
		}
		self::$events[$event][] = $callback;
	}
	/**
	 * 清除已注册的插件
	 * 
	 * @access public
	 * @param string $event 事件名称
	 */
	public static function clear(string $event = '') {
		if (empty($event)) {
			self::$events = [];
		} else {
			self::$events[$event] = [];
		}
	}
	/**
	 * 触发一个事件
	 * 
	 * @access public
	 * @param string $event 事件名称
	 * @param array $data 参数
	 */
	public static function trigger(string $event, $data = []) {
		$result = null;
		if (isset(self::$events[$event])) {
			foreach (self::$events[$event] as $callback) {
				$result = Utils::call($callback, $data);
				if ($result !== null) {
					break;
				}
			}
		}
		return $result;
	}
}
