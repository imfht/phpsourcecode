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
 * 提供File服务
 * Class FileService
 * @package Tang\Services
 */
class FileService extends ServiceProvider
{
	/**
	 * 返回服务
	 * 为了实现代码提示，这里使用硬编码
	 * @return \Tang\IO\Interfaces\IFile
	 */
	public static function getService()
	{
		return parent::getService();
	}
	protected static function register()
	{
		return static::initObject('file', '\Tang\IO\Interfaces\IFile');
	}
}