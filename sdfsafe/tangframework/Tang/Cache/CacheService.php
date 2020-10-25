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
namespace Tang\Cache;
use Tang\Services\ServiceProvider;

/**
 * Class CacheService
 * @package Tang\Cache
 */
class CacheService extends ServiceProvider
{
	/**
	 * @return \Tang\Manager\IManager
	 */
	public static function getService()
	{
		return parent::getService();
	}
	protected static function register()
	{
		$config = static::$config->replaceGet('cache',array('directory'=>'Cache','defaultDriver'=>'json','redisSource'=>'cache','memcachedSource'=>'cache','database'=>array('source'=>'','tableName'=>'cache')));
		$config['directory'] = static::$config->get('dataDirctory').trim(ucfirst($config['directory']),'/\\').DIRECTORY_SEPARATOR;
		$instance = static::initObject('cache','\Tang\Manager\IManager');
		$instance->setConfig($config);
		return $instance;
	}
}