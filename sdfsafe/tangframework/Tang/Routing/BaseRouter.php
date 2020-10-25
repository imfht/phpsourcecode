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
namespace Tang\Routing;
use Tang\Request\IRequest;

/**
 * router基类
 * Class BaseRouter
 * @package Tang\Routing
 */
abstract class BaseRouter
{
    /**
     * 模块值
     * @var string
     */
    protected $moduleValue;
    /**
     * 控制器值
     * @var string
     */
    protected $controllerValue;
    /**
     * 动作值
     * @var string
     */
    protected $actionValue;
    /**
     * request
     * @var IRequest
     */
    protected $request;

    /**
     * 设置request
     * @param IRequest $request
     * @return mixed
     */
    public function setRequest(IRequest $request)
    {
        $this->request = $request;
    }

    /**
     * 获取request
     * @return IRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * 获取模块值
     * @return string
     */
    public function getModuleValue()
    {
        return $this->moduleValue;
    }

    /**
     * 获取动作值
     * @return string
     */
    public function getActionValue()
    {
        return $this->actionValue;
    }

    /**
     * 获取控制器值
     * @return string
     */
    public function getControllerValue()
    {
        return $this->controllerValue;
    }
}