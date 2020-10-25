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
 * 目录服务
 * Class DirectoryService
 * @package Tang\Services
 */
class DirectoryService extends ServiceProvider
{
	/**
	 * 返回服务
	 * 为了实现代码提示，这里使用硬编码
	 * @return \Tang\IO\Interfaces\IDirectory
	 */
	public static function getService()
	{
		return parent::getService();
	}
	protected static function register()
	{
		return static::initObject('directory','\Tang\IO\Interfaces\IDirectory');
	}
}