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
 * Request接口
 * Interface IRequest
 * @package Tang\Request
 */
interface IRequest
{
	/**
	 * 判断是否cli模式
	 * @return boolean
	 */
	public function isCli();
	/**
	 * 判断是否ssl
	 * @return boolean
	 */
	public function isSsl();
	/**
	 * 获取get参数
	 * @param string $name
     * @param string $default 没有值的情况下返回的默认值
	 */
	public function get($name,$default='');
	/**
	 * 获取post参数
	 * @param string $name
     * @param string $default 没有值的情况下返回的默认值
	 */
	public function post($name,$default='');
	/**
	 * 获取put参数
	 * @param string $name
     * @param string $default 没有值的情况下返回的默认值
	 */
	public function put($name,$default='');
	/**
	 * 设置应用程序的根路径
	 * @param string $path
	 */
	public function setApplicationPath($path);
	/**
	 * 获取服务器上应用程序根路径。
	 * @return string
	 */
	public function getApplicationPath();
    /**
     * 设置Route
     * @param IRouter $router
     */
    public function setRouter(IRouter $router);
    /**
     * 获取route
     * @return IRouter
     */
    public function getRouter();

    /**
     * 设置response
     * @param IResponse $response
     */
    public function setResponse(IResponse $response);

    /**
     * 获取response
     * @return IResponse
     */
    public function getResponse();
    /**
     * 获取请求标示
     * 模块名_控制器名_动作名
     * @return string
     */
    public function getMarking();
}