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
use Tang\Cache\Stores\Apc;
use Tang\Cache\Stores\Wincache;

/**
 * Class ConfigService
 * @package Tang\Services
 */
class ConfigService extends ServiceProvider
{
	/**
	 * 设置配置类地址
	 * @param $class
	 */
	public static function setConfigClass($class)
	{
		static::$services['config'] = $class;
	}

	/**
	 * register
	 */
	protected static function register()
	{
		return static::initObject('config', '\Tang\Config\IConfig');
	}

	/**
	 * 获取Config
	 * return \Tang\Config\IConfig;
	 */
	public static function getService()
	{
		return parent::getService();
	}
}