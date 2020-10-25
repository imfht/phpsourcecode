<?php
/**
 * Trotri Foundation Classes
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright (c) 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace tfc\mvc\routes;

use tfc\ap\HttpRequest;

/**
 * Route abstract class file
 * 路由器基类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Route.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.mvc.routers
 * @since 1.0
 */
abstract class Route
{
    /**
     * @var string URI分隔符
     */
    const URI_DELIMITER = '/';

    /**
     * @var string|null 方法名
     */
    protected $_action;

    /**
     * @var string|null 控制器名
     */
    protected $_controller;

    /**
     * @var string|null 模型名
     */
    protected $_module;

    /**
     * @var array 默认参数
     */
    protected $_defaults = array();

    /**
     * 获取Action名
     * @return string
     */
    public function getAction()
    {
        return $this->_action;
    }

    /**
     * 设置Action名
     * @param string $action
     * @return \tfc\mvc\routers\Route
     */
    public function setAction($action)
    {
        if (is_string($action) && ($action = trim($action)) != '') {
            $this->_action = strtolower($action);
        }

        return $this;
    }

    /**
     * 获取Controller名
     * @return string
     */
    public function getController()
    {
        return $this->_controller;
    }

    /**
     * 设置Controller名
     * @param string $controller
     * @return \tfc\mvc\routers\Route
     */
    public function setController($controller)
    {
        if (is_string($controller) && ($controller = trim($controller)) != '') {
            $this->_controller = strtolower($controller);
        }

        return $this;
    }

    /**
     * 获取Module名
     * @return string
     */
    public function getModule()
    {
        return $this->_module;
    }

    /**
     * 设置Module名
     * @param string $module
     * @return \tfc\mvc\routers\Route
     */
    public function setModule($module)
    {
        if (is_string($module) && ($module = trim($module)) != '') {
            $this->_module = strtolower($module);
        }

        return $this;
    }

    /**
     * 获取所有默认的值，包括Module、Controller、Action和参数
     * @return array
     */
    public function getDefaults()
    {
        return $this->_defaults;
    }

    /**
     * 通过Path路径匹配当前路由，并且获取Module、Controller、Action，并将参数设置到Request中
     * @param \tfc\ap\HttpRequest $request
     * @return boolean
     */
    abstract protected function match(HttpRequest $request);
}
