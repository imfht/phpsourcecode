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
 * RouteStatic class file
 * 静态路由
 *
 * 静态路由例子：
 * URL：http://domain.com/login
 * <pre>
 * $route = new RouteStatic(
 *     'login',
 *     array(
 *         'module'     => 'main',
 *         'controller' => 'auth',
 *         'action'     => 'login'
 *     )
 * );
 * $value = array(
 *     'module'     => 'main',
 *     'controller' => 'auth',
 *     'action'     => 'login'
 * );
 * </pre>
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: RouteStatic.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.mvc.routers
 * @since 1.0
 */
class RouteStatic extends Route
{
    /**
     * @var string 路由匹配规则
     */
    protected $_route;

    /**
     * 构造方法：初始化路由匹配规则、默认参数
     * @param string $route
     * @param array $defaults
     */
    public function __construct($route, array $defaults = array())
    {
        $this->_route = trim($route, self::URI_DELIMITER);
        $this->_defaults = $defaults;
    }

    /**
     * (non-PHPdoc)
     * @see \tfc\mvc\routes\Route::match()
     */
    public function match(HttpRequest $request)
    {
        $route = trim($request->pathInfo, self::URI_DELIMITER);
        if ($this->_route === $route) {
            if (isset($this->_defaults['controller'])) {
                $this->setController($this->_defaults['controller']);
            }

            if (isset($this->_defaults['action'])) {
                $this->setAction($this->_defaults['action']);
            }

            if (isset($this->_defaults['module'])) {
                $this->setModule($this->_defaults['module']);
            }

            return true;
        }

        return false;
    }
}
