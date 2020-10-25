<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace member\library;

use tfc\ap\EventDispatcher;

/**
 * Plugin class file
 * 当前业务的插件管理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Plugin.php 1 2013-04-05 01:38:06Z huan.song $
 * @package member.library
 * @since 1.0
 */
class Plugin extends EventDispatcher
{
	/**
	 * @var array 用于寄存需要加载的插件名
	 */
	public static $plgNames = array(
	);

	/**
	 * 通过插件名，批量注册被观察的插件
	 * @return void
	 */
	public static function loadPlugins()
	{
		$dispatcher = Plugin::getInstance();
		$dispatcher->loadEvents(self::getEventNames());
	}

	/**
	 * 获取所有的插件名
	 * @return array
	 */
	public static function getEventNames()
	{
		$eventNames = array();

		$srvName = pathinfo(substr(dirname(__FILE__), 0, -8), PATHINFO_BASENAME);
		foreach (self::$plgNames as $plgName) {
			$plgName = strtolower(trim($plgName));
			$eventNames[] = '\\' . $srvName . '\\plugins\\' . $plgName . '\\Plg' . ucfirst($plgName);
		}

		return $eventNames;
	}

}

Plugin::loadPlugins();
