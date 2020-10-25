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
namespace Tang\Request;
use Tang\Response\IResponse;
use Tang\Routing\IRouter;

/**
 * Request基类
 * Class BaseRequest
 * @package Tang\Request
 */
abstract class BaseRequest
{
    /**
     * 系统路径
     * @var string
     */
    protected $applicationPath;
    /**
     * router
     * @var IRouter
     */
    protected $router;
    /**
     * @var IResponse
     */
    protected $response;
	/**
	 * 设置应用程序的根路径
	 * @param string $path
	 */
	public function setApplicationPath($path)
	{
		$this->applicationPath = $path;
	}

	/**
	 * 获取服务器上应用程序根路径。
	 * @return string
	 */
	public function getApplicationPath()
	{
		return $this->applicationPath;
	}

    /**
     * 设置Route
     * @param IRouter $router
     */
    public function setRouter(IRouter $router)
    {
        $this->router = $router;
    }

    /**
     * 获取route
     * @return IRouter
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * 设置response
     * @param IResponse $response
     */
    public function setResponse(IResponse $response)
    {
        $this->response = $response;
    }

    /**
     * 获取response
     * @return IResponse
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * 获取请求标示
     * @return string
     */
    public function getMarking()
    {
        return $this->router->getModuleValue().$this->router->getControllerValue().$this->router->getActionValue();
    }
}