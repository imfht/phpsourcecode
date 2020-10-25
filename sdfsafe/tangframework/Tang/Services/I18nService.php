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
 * 提供语言包服务
 * Class FileService
 * @package Tang\Services
 */
class I18nService extends ServiceProvider
{
	/**
	 * 返回服务
	 * 为了实现代码提示，这里使用硬编码
	 * @return \Tang\I18n\II18n
	 */
	public static function getService()
	{
		return parent::getService();
	}
	protected static function register()
	{
        $instance = static::initObject('i18n', '\Tang\I18n\II18n');
        $instance->setCharset(ucfirst(strtolower(static::$config->get('charset','utf-8'))));
        $instance->setLanguage(ucfirst(strtolower(static::$config->get('lang','zh-cn'))));
		$instance->setApplicationDirectory(static::$config->get('applicationDirectory'));
		return $instance;
	}
}