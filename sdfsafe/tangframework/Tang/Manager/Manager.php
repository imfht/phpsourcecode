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
namespace Tang\Manager;
use Tang\Exception\SystemException;
use Tang\Log\LogService;

/**
 * 管理器基类
 * Class Manager
 * @package Tang\Manager
 */
abstract class Manager implements IManager
{
    /**
     * 驱动实例
     * @var array
     */
    protected $drivers = array();
    /**
     * 配置
     * @var array
     */
    protected $config = array();

    /**
     * @see ISetConfig::setConfig
     */
    public function setConfig(array $config)
	{
		$this->config = $config;
	}

    /**
     * @see IManager::addDriver
     */
    public function addDriver($name,$driver)
	{
		if(isset($this->drivers[$name]))
		{
			throw new SystemException('%s drive already exists!',array($name),10200,LogService::ALERT);
		}
		$interface = $this->getIntreface();
		if(!$driver instanceof $interface)
		{
			throw new SystemException('Class [%s] does not implement the [%s] interface!',array($name,$interface),10201,LogService::ALERT);
		}
		$this->drivers[$name] = $driver;
		return $this;
	}

    /**
     * @see IManager::driver
     */
	public function driver($name = '')
	{
		$name == '' && $name = $this->getDefaultDriver();
		if(!isset($this->drivers[$name]))
		{
			$this->drivers[$name] = $this->createDriver($name);
		}
		return $this->drivers[$name];
	}

    /**
     * 调用默认驱动实现
     * @param $method
     * @param $parameters
     * @return mixed
     */
    public function __call($method,$parameters)
	{
		return call_user_func_array(array($this->driver(),$method),$parameters);
	}

    /**
     * 获取默认驱动名
     * @return mixed
     */
    protected function getDefaultDriver()
	{
		return $this->config['defaultDriver'];
	}

    /**
     * 创建驱动
     * @param $name
     * @return mixed
     * @throws \Tang\Exception\SystemException
     */
    protected function createDriver($name)
	{
		$method = 'create'.ucfirst($name).'Driver';
		if(!method_exists($this,$method))
		{
			throw new SystemException('Drivers [%s] not supported!',array($name),10202,LogService::ALERT);
		}
		return $this->{$method}();
	}

    /**
     * 获取驱动接口名称
     * @return string
     */
    protected abstract function getIntreface();
}