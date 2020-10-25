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
namespace Tang\Web;
use Tang\Services\ServiceProvider;
class Cookie extends ServiceProvider
{
	/**
	 *
	 * @return \Tang\Web\Cookie\ICookie
	 */
	public static function getService()
	{
		return parent::getService();
	}
	protected static function register()
	{
		$obj = static::initObject('cookie', '\Tang\Web\Cookie\ICookie');
		$obj->setConfig(static::$config->get('cookie'));
		return $obj;
	}
}