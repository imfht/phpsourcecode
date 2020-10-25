<?php
/**
 * Trotri Foundation Classes
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright (c) 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace tfc\mvc;

use tfc\ap\Application;
use tfc\ap\HttpRequest;
use tfc\ap\ErrorException;
use tfc\ap\RuntimeException;
use tfc\mvc\routes\Route;
use tfc\mvc\routes\RouteSupervar;

/**
 * Router class file
 * 路由器管理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Router.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.mvc
 * @since 1.0
 */
class Router extends Application
{
    /**
     * @var instance of tfc\mvc\routers\Route 当前的匹配成功的路由类实例
     */
    protected $_currRoute = null;

    /**
     * @var string|null 当前的匹配成功的路由名
     */
    protected $_currRouteName = null;

    /**
     * @var array 所有的路由器
     */
    protected $_routes = array();

    /**
     * @var string 默认的方法名
     */
    protected $_defaultAction = 'index';

    /**
     * @var string 默认的控制器名
     */
    protected $_defaultController = 'index';

    /**
     * @var string 默认的模型名
     */
    protected $_defaultModule = '';

    /**
     * 构造方法：添加默认的路由，在用户没有设置任何路由的情况下，用RouteSupervar路由
     */
    public function __construct()
    {
        $this->addRoute('default', new RouteSupervar());
    }

    /**
     * 获取Action名，如果从路由器中获取的Action名为null，则返回默认的Action名
     * @return string
     */
    public function getAction()
    {
        $action = $this->getCurrRoute()->getAction();
        if ($action === null) {
            return $this->getDefaultAction();
        }

        return $action;
    }

    /**
     * 获取Controller名，如果从路由器中获取的Controller名为null，则返回默认的Controller名
     * @return string
     */
    public function getController()
    {
        $controller = $this->getCurrRoute()->getController();
        if ($controller === null) {
            return $this->getDefaultController();
        }

        return $controller;
    }

    /**
     * 获取Module名，如果从路由器中获取的Module名为null，则返回默认的Module名
     * @return string
     */
    public function getModule()
    {
        $module = $this->getCurrRoute()->getModule();
        if ($module === null) {
            return $this->getDefaultModule();
        }

        return $module;
    }

    /**
     * 倒序遍历所有路由器，直到匹配一个成功后退出
     * @param \tfc\ap\HttpRequest $request
     * @return \tfc\mvc\Router
     * @throws RuntimeException 如果所有的路由器都匹配失败，抛出异常
     */
    public function route(HttpRequest $request)
    {
        $notMatch = true;
        foreach (array_reverse($this->getRoutes(), true) as $name => $route) {
            if ($route->match($request)) {
                $this->_currRouteName = $name;
                $this->_currRoute = $route;
                $notMatch = false;
                break;
            }
        }

        if ($notMatch) {
            throw new RuntimeException('Router all routes match wrong');
        }

        return $this;
    }

    /**
     * 获取当前的匹配成功的路由类
     * @return \tfc\mvc\routes\Route
     * @throws RuntimeException 如果还未匹配出当前的路由，抛出异常
     */
    public function getCurrRoute()
    {
        if ($this->_currRoute === null) {
            throw new RuntimeException('Router current route was not defined');
        }

        return $this->_currRoute;
    }

    /**
     * 获取当前的匹配成功的路由名
     * @return string
     * @throws RuntimeException 如果还未匹配出当前的路由，抛出异常
     */
    public function getCurrRouteName()
    {
        if ($this->_currRouteName === null) {
            throw new RuntimeException('Router current route was not defined');
        }

        return $this->_currRouteName;
    }

    /**
     * 获取所有的路由器
     * @return array
     */
    public function getRoutes()
    {
        return $this->_routes;
    }

    /**
     * 通过路由名获取路由类的实例
     * @param string $name
     * @return \tfc\mvc\routes\Route
     * @throws ErrorException 如果指定的路由名不存在，抛出异常
     */
    public function getRoute($name)
    {
        if ($this->hasRoute($name)) {
            return $this->_routes[$name];
        }

        throw new ErrorException(sprintf(
            'Router no route is registered for name "%s"', $name
        ));
    }

    /**
     * 添加一个路由名和路由类
     * @param string $name
     * @param \tfc\mvc\routes\Route $route
     * @return \tfc\mvc\Router
     */
    public function addRoute($name, Route $route)
    {
        if ($route !== null) {
            $this->_routes[$name] = $route;
        }

        return $this;
    }

    /**
     * 通过路由名删除路由类
     * @param string $name
     * @return \tfc\mvc\Router
     */
    public function removeRoute($name)
    {
        if ($this->hasRoute($name)) {
            unset($this->_routes[$name]);
        }

        return $this;
    }

    /**
     * 通过路由名判断路由类是否存在
     * @param string $name
     * @return boolean
     */
    public function hasRoute($name)
    {
        return isset($this->_routes[$name]);
    }

    /**
     * 获取默认的Action名
     * @return string
     */
    public function getDefaultAction()
    {
        return $this->_defaultAction;
    }

    /**
     * 设置默认的Action名
     * @param string $action
     * @return \tfc\mvc\Router
     */
    public function setDefaultAction($action)
    {
        $this->_defaultAction = (string) $action;
        return $this;
    }

    /**
     * 获取默认的Controller名
     * @return string
     */
    public function getDefaultController()
    {
        return $this->_defaultController;
    }

    /**
     * 设置默认的Controller名
     * @param string $controller
     * @return \tfc\mvc\Router
     */
    public function setDefaultController($controller)
    {
        $this->_defaultController = (string) $controller;
        return $this;
    }

    /**
     * 获取默认的Module名
     * @return string
     */
    public function getDefaultModule()
    {
        return $this->_defaultModule;
    }

    /**
     * 设置默认的Module名
     * @param string $defaultModule
     * @return \tfc\mvc\Router
     */
    public function setDefaultModule($defaultModule)
    {
        $this->_defaultModule = (string) $defaultModule;
        return $this;
    }
}
