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
 * Request服务
 * Class RequestService
 * @package Tang\Services
 */
class RequestService extends ServiceProvider
{
	/**
	 *
	 * @return \Tang\Request\IRequest
	 */
	public static function getService()
	{
		return parent::getService();
    }
    protected static function register()
    {
        $driver = php_sapi_name() == 'cli'?'cli':'web';
        $instance = static::initObject('request', '\Tang\Manager\IManager')->driver($driver);
        $router = RouterService::driver($driver);
        $config = static::$config->replaceGet(ucfirst($driver).'Router.*',$router->getDefaultConfig());
        $router->setConfig($config);
        $router->setRequest($instance);
        $responseManager = ResponseService::getService();
        $responseManager->setConfig(array('charset' => static::$config->get('charset'),'contentType'=>''));
        $instance->setRouter($router);
        $instance->setResponse($responseManager->driver($driver));
        return $instance;
	}
}