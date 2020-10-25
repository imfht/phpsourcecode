<?php

/**
 * 插件、Hook类
 * 
 * @author ShuangYa
 * @package SYFramework
 * @category Library
 * @link http://www.sylingd.com/
 * @copyright Copyright (c) 2017 ShuangYa
 * @license http://lab.sylingd.com/go.php?name=framework&type=license
 */

namespace sy\lib;
use \Sy;

class Plugin {
	protected static $_list = [];
	/**
	 * 注册插件，用于自定义一些操作（例如对404的处理）
	 * @access public
	 * @param object $obj 实现plugin的类
	 */
	public static function register($obj) {
		if (!($obj instanceof \sy\interfaces\plugin)) {
			return;
		}
		if (!is_array(static::$_list[$obj->type])) {
			static::$_list[$obj->type] = [];
		}
		static::$_list[$obj->type][] = $obj;
	}
	/**
	 * 触发一个事件
	 * @access public
	 * @param string $type
	 * @param array $data 数据
	 */
	public static function trigger($type, $data = []) {
		if (!is_array(static::$_list[$type])) {
			return NULL;
		}
		foreach (static::$_list[$type] as $plugin) {
			$result = call_user_func_array($plugin, $data);
			if ($result !== NULL) {
				return $result;
			}
		}
		return NULL;
	}
}