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

use tfc\ap\Ap;
use tfc\ap\InvalidArgumentException;
use tfc\mvc\routes;
use tfc\util\String;

/**
 * UrlManager class file
 * URL管理类，根据路由规则生成URL
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: UrlManager.php 1 2013-05-18 14:58:59Z huan.song $
 * @package tfc.mvc
 * @since 1.0
 */
class UrlManager
{
    /**
     * @var string 伪静态类型路由，如：RouteRewrite、RouteRegex、RouteStatic
     */
    const ROUTE_REWRITE = 'REWRITE';

    /**
     * @var string 简单路由，如：index.php?m=main&c=archive&a=show
     */
    const ROUTE_SIMPLE = 'SIMPLE';

    /**
     * @var string 默认路由，如：index.php?r=main/archive/show
     */
    const ROUTE_SUPERVAR = 'SUPERVAR';

    /**
     * @var string|null 当前的匹配成功的路由类型
     */
    protected $_routeType = null;

    /**
     * 通过路由类型，获取URL
     * 如果指定了Action，但没指定Controller，则Controller默认为当前Controller
     * 如果指定了Controller，但没指定Module，则Module默认为当前Module
     * @param string $action
     * @param string $controller
     * @param string $module
     * @param array $params
     * @return string
     */
    public function getUrl($action = '', $controller = '', $module = '', array $params = array())
    {
        $url = $this->getScriptUrl();
        $url = $this->applyQueryString($url, $action, $controller, $module, $params);
        return $url;
    }

    /**
     * 获取当前脚本的路径
     * @return string
     */
    public function getScriptUrl()
    {
        return Ap::getRequest()->getScriptUrl();
    }

    /**
     * 获取要访问的页面名
     * @return string
     */
    public function getRequestUri()
    {
        return Ap::getRequest()->getRequestUri();
    }

    /**
     * 通过路由类型，在URL后拼接该路由的QueryString参数
     * @param string $url
     * @param string $action
     * @param string $controller
     * @param string $module
     * @param array $params
     * @return string
     */
    public function applyQueryString($url, $action = '', $controller = '', $module = '', array $params = array())
    {
        $method = 'apply' . $this->getRouteType() . 'query';
        $url = $this->$method($url, $action, $controller, $module, $params);
        return $url;
    }

    /**
     * 在URL后拼接Rewrite路由的QueryString，QueryString：/module/controller/action/k1/v1/k2/v2/k3/v3
     * @param string $url
     * @param string $action
     * @param string $controller
     * @param string $module
     * @param array $params
     * @return string
     */
    public function applyRewriteQuery($url, $action = '', $controller = '', $module = '', array $params = array())
    {
        $routes = $this->getCleanRoutes($action, $controller, $module);
        if ($routes !== array()) {
            $url .= '/' . implode('/', $routes);
        }

        $url = $this->applyRewriteParams($url, $params);
        return $url;
    }

    /**
     * 在URL后拼接Simple路由的QueryString，QueryString：?m=module&c=controller&a=action&k1=v1&k2=v2&k3=v3
     * @param string $url
     * @param string $action
     * @param string $controller
     * @param string $module
     * @param array $params
     * @return string
     */
    public function applySimpleQuery($url, $action = '', $controller = '', $module = '', array $params = array())
    {
        $routes = $this->getCleanRoutes($action, $controller, $module);
        if (strpos($url, '?') === false) {
            $url .= '?';
        }

        foreach ($routes as $key => $value) {
            $url .= '&' . $key . '=' . String::urlencode($value);
        }

        $url = $this->applySimpleParams($url, $params);
        return $url;
    }

    /**
     * 在URL后拼接Supervar路由的QueryString，QueryString：?r=module/controller/action&k1=v1&k2=v2&k3=v3
     * @param string $url
     * @param string $action
     * @param string $controller
     * @param string $module
     * @param array $params
     * @return string
     */
    public function applySupervarQuery($url, $action = '', $controller = '', $module = '', array $params = array())
    {
        $routes = $this->getCleanRoutes($action, $controller, $module);
        if (strpos($url, '?') === false) {
            $url .= '?';
        }

        if ($routes !== array()) {
            $url .= 'r=' . implode('/', $routes);
        }

        $url = $this->applySimpleParams($url, $params);
        return $url;
    }

    /**
     * 通过路由类型，在URL后拼接QueryString参数
     * @param string $url
     * @param array $params
     * @return string
     */
    public function applyParams($url, array $params = array())
    {
        $method = 'apply' . $this->getRouteType() . 'Params';
        $url = $this->$method($url, $params);
        return $url;
    }

    /**
     * 在URL后拼接QueryString参数，QueryString：/k1/v1/k2/v2/k3/v3
     * @param string $url
     * @param array $params
     * @return string
     */
    public function applyRewriteParams($url, array $params = array())
    {
        if ($params !== null) {
            foreach ($params as $key => $value) {
                $url .= '/' . $key . '/' . String::urlencode($value);
            }
        }

        return $url;
    }

    /**
     * 在URL后拼接QueryString参数，QueryString：&k1=v1&k2=v2&k3=v3
     * @param string $url
     * @param array $params
     * @return string
     */
    public function applySupervarParams($url, array $params = array())
    {
        return $this->applySimpleParams($url, $params);
    }

    /**
     * 在URL后拼接QueryString参数，QueryString：&k1=v1&k2=v2&k3=v3
     * @param string $url
     * @param array $params
     * @return string
     */
    public function applySimpleParams($url, array $params = array())
    {
        if ($params !== null) {
            if (strpos($url, '?') === false) {
                $url .= '?';
            }

            foreach ($params as $key => $value) {
                $url .= '&' . $key . '=' . String::urlencode($value);
            }
        }

        return $url;
    }

    /**
     * 处理路由参数
     * 如果指定了Action，但没指定Controller，则Controller默认为当前Controller
     * 如果指定了Controller，但没指定Module，则Module默认为当前Module
     * @param string $action
     * @param string $controller
     * @param string $module
     * @return array
     */
    public function getCleanRoutes($action = '', $controller = '', $module = '')
    {
        $module = trim((string) $module);
        $controller = trim((string) $controller);
        $action = trim((string) $action);

        $action     !== '' && $controller === '' && $controller = Mvc::$controller;
        $controller !== '' && $module     === '' && $module     = Mvc::$module;

        $routes = array();

        $module     !== '' && $routes['m'] = $module;
        $controller !== '' && $routes['c'] = $controller;
        $action     !== '' && $routes['a'] = $action;

        return $routes;
    }

    /**
     * 获取路由方式
     * @return string
     */
    public function getRouteType()
    {
        if ($this->_routeType === null) {
            $this->setRouteType();
        }

        return $this->_routeType;
    }

    /**
     * 设置路由方式
     * @param string $routeType
     * @return \tfc\mvc\UrlManager
     * @throws InvalidArgumentException 如果参数不是有效的路由方式，抛出异常
     */
    public function setRouteType($routeType = null)
    {
        if ($routeType === null) {
            $route = Mvc::getRouter()->getCurrRoute();
            if ($this->isSupervar($route)) {
                $routeType = self::ROUTE_SUPERVAR;
            }
            elseif ($this->isRewrite($route)) {
                $routeType = self::ROUTE_REWRITE;
            }
            elseif ($this->isSimple($route)) {
                $routeType = self::ROUTE_SIMPLE;
            }
        }

        $routeType = strtoupper((string) $routeType);
        if (!defined('static::ROUTE_' . $routeType)) {
            throw new InvalidArgumentException(sprintf(
                'UrlManager Route Type "%s" invalid.', $routeType
            ));
        }

        $this->_routeType = $routeType;
        return $this;
    }

    /**
     * 判断路由方式是否是Supervar
     * @param \tfc\mvc\routes\Route $route
     * @return boolean
     */
    public function isSupervar(routes\Route $route)
    {
        if ($route instanceof routes\RouteSupervar) {
            return true;
        }

        return false;
    }

    /**
     * 判断路由方式是否是Rewrite
     * @param \tfc\mvc\routes\Route $route
     * @return boolean
     */
    public function isRewrite(routes\Route $route)
    {
        if ($route instanceof routes\RouteRewrite) {
            return true;
        }

        if ($route instanceof routes\RouteRegex) {
            return true;
        }

        if ($route instanceof routes\RouteStatic) {
            return true;
        }

        return false;
    }

    /**
     * 判断路由方式是否是Simple
     * @param \tfc\mvc\routes\Route $route
     * @return boolean
     */
    public function isSimple(routes\Route $route)
    {
        if ($route instanceof routes\RouteSimple) {
            return true;
        }

        return false;
    }
}
