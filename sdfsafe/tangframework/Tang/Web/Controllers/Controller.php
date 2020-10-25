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
namespace Tang\Web\Controllers;
use Tang\Cli\MessageController as CliMessageController;
use Tang\Services\ConfigService;
use Tang\Services\I18nService;
use Tang\Services\RequestService;
use Tang\Web\Parameters;

/**
 * 控制器
 * Class Controller
 * @package Tang\Web\Controllers
 */
abstract class Controller
{
	private static $controllers = array();
	/**
	 * @var Parameters
	 */
	protected $parameters;
	/**
	 * @var \Tang\Request\IRequest
	 */
	protected $request;
    /**
     * 配置
     * @var \Tang\Config\IConfig
     */
    protected $config;
    /**
     * 配置
     * @var \Tang\I18n\II18n
     */
    protected $i18n;
    /**
     * 加载控制器
     * @param $module
     * @param $controller
     * @param $type
     * @return Controller
     */
    public static function load($module,$controller,$type)
	{
		$name = $module.'_'.$controller.'_'.$type;
		if(!isset(static::$controllers[$name]) || !static::$controllers[$name] instanceof Controller)
		{
			$class = '\\Lib\\Applications\\'.$module.'\\Controllers\\'.$type.'\\'.$controller;
			if(!class_exists($class))
			{
				$instance = strtolower($type) == 'web' ?MessageController::create():CliMessageController::create();
				$instance->notFound('not found controller');
			}
			$instance = new $class;
			$instance->request = RequestService::getService();
            $instance->config = ConfigService::getService();
            $instance->i18n = I18nService::getService();
			return static::$controllers[$name] = $instance;
		}
		return clone static::$controllers[$name];
	}

    /**
     * 载入控制器并执行$action
     * @param $module
     * @param $controller
     * @param $action
     * @param $type
     */
    public static function loadRun($module,$controller,$action,$type)
	{
		$instance = static::load($module,$controller,$type);
		$instance->setParameters(new Parameters($module,$controller,$action,$type));
		$instance->invoke($action);
	}

    /**
     * 执行$action
     * @param $action
     * @return mixed
     */
    protected abstract function invoke($action);

    /**
     * 设置参数
     * @param Parameters $parameters
     */
    protected function setParameters(Parameters $parameters)
	{
		$this->parameters = $parameters;
	}

	/**
	 * 未找到页面
	 * @param $message
	 */
	protected abstract function notFound($message);

	/**
	 * 消息提示
	 * @param $message 消息
	 * @param int $code 错误码 200表示成功
	 * @param string $jumpUrl 跳转页面
	 * @param string $page 消息页面
	 */
	protected abstract function message($message,$code=200,$jumpUrl='',$page='message');
}