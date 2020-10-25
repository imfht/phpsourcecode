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

/**
 * Mvc class file
 * mvc包中类管理器
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Mvc.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.mvc
 * @since 1.0
 */
class Mvc
{
    /**
     * @var string 模型名
     */
    public static $module = '';

    /**
     * @var string 控制器名
     */
    public static $controller = '';

    /**
     * @var string 方法名
     */
    public static $action = '';

    /**
     * @var instance of tfc\mvc\Dispatcher
     */
    protected static $_dispatcher = null;

    /**
     * @var instance of tfc\mvc\Router
     */
    protected static $_router = null;

    /**
     * @var instance of tfc\mvc\interfaces\View
     */
    protected static $_view = null;

    /**
     * 以单入口Mvc方式运行项目
     * @return void
     */
    public static function run()
    {
        $router = self::getRouter()->route(Ap::getRequest());
        self::$module = $router->getModule();
        self::$action = $router->getAction();
        self::$controller = $router->getController();

        self::getDispatcher()->run($router);
    }

    /**
     * 获取路由器
     * @return \tfc\mvc\Dispatcher
     */
    public static function getDispatcher()
    {
        if (self::$_dispatcher === null) {
            self::setDispatcher();
        }

        return self::$_dispatcher;
    }

    /**
     * 设置路由器
     * @param \tfc\mvc\Dispatcher $dispatcher
     * @return void
     */
    public static function setDispatcher(Dispatcher $dispatcher = null)
    {
        if ($dispatcher === null) {
            $dispatcher = new Dispatcher();
        }

        self::$_dispatcher = $dispatcher;
    }

    /**
     * 获取路由器
     * @return \tfc\mvc\Router
     */
    public static function getRouter()
    {
        if (self::$_router === null) {
            self::setRouter();
        }

        return self::$_router;
    }

    /**
     * 设置路由器
     * @param \tfc\mvc\Router $router
     * @return void
     */
    public static function setRouter(Router $router = null)
    {
        if ($router === null) {
            $router = new Router();
        }

        self::$_router = $router;
    }

    /**
     * 获取模板解析类
     * @return \tfc\mvc\interfaces\View
     */
    public static function getView()
    {
        if (self::$_view === null) {
            self::setView();
        }

        return self::$_view;
    }

    /**
     * 设置模板解析类
     * @param \tfc\mvc\interfaces\View $view
     * @return void
     */
    public static function setView(interfaces\View $view = null)
    {
        if ($view === null) {
            $view = new View();
        }

        self::$_view = $view;
    }
}
