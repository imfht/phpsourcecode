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
namespace Tang\Web\View;
use Tang\Services\ServiceProvider;

class ViewService extends ServiceProvider
{
	/**
	 *
	 * @return \Tang\Web\View\IView
	 */
	public static function getService()
	{
		$calledClass = get_called_class();
		if(!isset(static::$objects[$calledClass]))
		{
			return parent::getService();
		} else
		{
			return clone static::$objects[$calledClass];
		}
	}
	protected static function register()
	{
		$ajax = static::$config->get('ajax');
		$config = static::$config->get('template.*');
		$config = array_replace_recursive(array(
			'directory' => 'Template',
			'defaultTheme' => 'Default',
			'getThemeName' => 'theme',
			'cookieThemeName' => 'theme',
            'callback'=>$ajax['callback']
		),$config);
        $config['applicationPath'] = static::$config->get('applicationDirectory');
        $instance = static::initObject('view','\Tang\Web\View\IView');
        $instance->setConfig($config);
		return $instance;
	}
}


