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
use Tang\Exception\SystemException;
use Tang\Config\IConfig;
use Tang\Log\LogService;

/**
 * 服务提供基类
 * Class ServiceProvider
 * @package Tang\Services
 */
abstract class ServiceProvider
{
	protected static $objects = array();
    protected static $services = array (
				'config' => '\Tang\Config\PhpConfig',
                'event' => '\Tang\Event\Event',
                'file' => '\Tang\IO\File',
                'directory' => '\Tang\IO\Directory',
                'i18n' => '\Tang\I18n\I18n',
                'browser' => '\Tang\Web\Browser\HttpBrowserCapabilities',
                'cookie' => '\Tang\Web\Cookie\Cookier',
                'request' =>  '\Tang\Request\RequestManager',
                'router' => '\Tang\Routing\RouterManager',
                'response'=> '\Tang\Response\ResponseManager',
                'template' => '\Tang\Web\View\TemplateManager',
                'view' => '\Tang\Web\View\View',
                'cache' => '\Tang\Cache\CacheManager',
                'session' => '\Tang\Web\Session\SessionManager',
                'thirdParty' => '\Tang\ThirdParty\ThirdPartyImport',
                'crypt' => '\Tang\Crypt\CryptManager',
                'storage' => '\Tang\Storage\StorageManager',
                'token' => '\Tang\Token\Token',
                'pagination' => '\Tang\Pagination\Paginator',
                'log' => '\Tang\Log\LogManager',
                'ipLocation' => '\Tang\Web\Ip\Location\LocationManager',
                'clientIp' => '\Tang\Web\Ip\ClientIp',
                'amqp' => '\Tang\Amqp\Servers'
    );
	/**
	 * 
	 * @var IConfig
	 */
	protected static $config;
	public static function getService()
	{
		$calledClass = get_called_class();
		if(!isset(static::$objects[$calledClass]))
		{
			static::$objects[$calledClass] = static::register();
		}
		return static::$objects[$calledClass];
	}
	public static function setConfig(IConfig $config)
	{
		static::$config = $config;
        static::$services = $config->replaceGet('services',static::$services);
	}
	protected static function initObject($class,$interface)
	{
		if(!isset(static::$services[$class]) || !static::$services[$class])
		{
			throw new SystemException('The [%s] service does not exist or service name is empty!',array($class),10100,LogService::EMERG);
		}
		$interface = ltrim($interface,'\\');
		$class = static::$services[$class];
		$interfaces = class_implements($class);
		if(!$interfaces || !isset($interfaces[$interface]))
		{
			throw new SystemException('Class [%s] does not implement the [%s] interface!',array($class,$interface),10101,LogService::ALERT);
		}
		return new $class;
	}
	protected static function register()
	{
		throw new SystemException('Service class [%s] does not implement the register method!',array(get_called_class()),10102,LogService::ALERT);
	}
	public static function __callStatic($method,$parameters)
	{
		return call_user_func_array(array(static::getService(),$method),$parameters);
	}
}