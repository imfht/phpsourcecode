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
namespace Tang\Web\Session;
use Tang\Services\ServiceProvider;

class SessionService extends ServiceProvider
{
	/**
	 * @return ISession
	 */
	public static function getService()
	{
		return parent::getService();
	}
	protected static function register()
	{
		$config = static::$config->replaceGet('session',array('database'=>array('source'=>'','tableName'=>'session'),'directory'=>'Session','defaultDriver'=>'json','redisSource'=>'session','memcachedSource'=>'session','expire'=>86400));
        $config['directory'] = static::$config->get('dataDirctory').trim(ucfirst($config['directory']),'/\\').DIRECTORY_SEPARATOR;
		$instance = static::initObject('session','\Tang\Web\Session\ISession');
		$instance->setConfig($config);
		$driver = $instance->driver();
		session_set_save_handler($driver,true);
		session_start();
		return $instance;
	}
}