<?php
// +-----------------------------------------------------------------------------------
// | TangFrameWork 致力于WEB快速解决方案
// +-----------------------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.tangframework.com All rights reserved.
// +-----------------------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +-----------------------------------------------------------------------------------
// | HomePage ( http://www.tangframework.com/ )
// +-----------------------------------------------------------------------------------
// | Author: wujibing<283109896@qq.com>
// +-----------------------------------------------------------------------------------
// | Version: 1.0
// +-----------------------------------------------------------------------------------
namespace Tang\Services;
/**
 * 事件服务
 * Class EventService
 * @package Tang\Services
 */
class EventService extends ServiceProvider
{
	/**
	 * 返回新的事件
	 * @return \Tang\Event\IEvent
	 */
	public static function newService()
	{
		return static::register();
	}
	protected static function register()
	{
		return static::initObject('event', '\Tang\Event\IEvent');
	}
}