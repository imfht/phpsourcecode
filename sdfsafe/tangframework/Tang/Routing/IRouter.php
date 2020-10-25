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
use Tang\Interfaces\ISetConfig;
use Tang\Request\IRequest;

/**
 * 路由接口
 * @author 吉兵
 *
 */
interface IRouter extends ISetConfig
{
    /**
     * 获取默认的配置
     * @return array
     */
    public function getDefaultConfig();
    /**
     * 执行路由
     * @return mixed
     */
    public function router();

    /**
     * 返回类型
     * @return mixed
     */
    public function getType();
    /**
     * 获取模块名称
     * @return string
     */
    public function getModuleValue();
    /**
     * 获取
     * @return string
     */
    public function getActionValue();
    /**
     * 获取控制器名
     * @return string
     */
    public function getControllerValue();

    /**
     * 设置request
     * @param IRequest $request
     * @return mixed
     */
    public function setRequest(IRequest $request);

    /**
     * 获取request
     * @return IRequest
     */
    public function getRequest();
}