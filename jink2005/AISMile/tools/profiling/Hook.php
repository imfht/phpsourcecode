<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

//
// IMPORTANT : don't forget to delete the underscore _ in the file name if you want to use it !
//

class Hook extends HookCore
{
	private static $hookTime = array();
	private static $hookMemoryUsage = array();
	
	public static function getHookTime()
	{
		return self::$hookTime;
	}
	
	public static function getHookMemoryUsage()
	{
		return self::$hookMemoryUsage;
	}
	
	public static function exec($hook_name, $hookArgs = array(), $id_module = null)
	{
		$memoryUsage = memory_get_usage();
		$t0 = microtime(true);
		$result = parent::exec($hook_name, $hookArgs, $id_module);
		self::$hookTime[$hook_name] = microtime(true) - $t0;
		self::$hookMemoryUsage[$hook_name] = memory_get_usage() - $memoryUsage;
		return $result;
	}
}
