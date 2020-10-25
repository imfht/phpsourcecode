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
use tfc\ap\ErrorException;

/**
 * Dispatcher class file
 * 发报器类，实例化Controller类，并调用Action方法
 * 必须先将Module目录设置成类自动加载目录，才可以使用此类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Dispatcher.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.mvc
 * @since 1.0
 */
class Dispatcher extends Application
{
    /**
     * @var string 控制器的目录名
     */
    protected $_controllerDirName = 'controller';

    /**
     * @var string 模型的目录名
     */
    protected $_moduleDirName = 'modules';

    /**
     * @var string 控制器类名后缀
     */
    protected $_controllerExtName = 'Controller';

    /**
     * 实例化Controller类，并调用Action方法
     * @param \tfc\mvc\Router $router
     * @return void
     */
    public function run(Router $router)
    {
        $controller = $this->createController($router);
        $action = $controller->createActionById(ucfirst($router->getAction()));
        $controller->runAction($action);
    }

    /**
     * 通过路由器获取Controller名，并创建Controller类
     * @param \tfc\mvc\Router $router
     * @return \tfc\mvc\Controller
     * @throws ErrorException 如果Controller类不存在，抛出异常
     * @throws ErrorException 如果获取的实例不是tfc\mvc\Controller类的子类，抛出异常
     */
    public function createController(Router $router)
    {
        $controller = $this->getController($router);
        if (!class_exists($controller)) {
            throw new ErrorException(sprintf(
                'Dispatcher is unable to find the requested controller "%s".', $controller
            ));
        }

        $instance = new $controller();
        if (!$instance instanceof Controller) {
            throw new ErrorException(sprintf(
                'Dispatcher Class "%s" is not instanceof tfc\mvc\Controller.', $controller
            ));
        }

        return $instance;
    }

    /**
     * 通过路由器获取控制器名
     * @param \tfc\mvc\Router $router
     * @return string
     */
    public function getController(Router $router)
    {
        if (($module = trim((string) $router->getModule())) !== '') {
            $module = $this->getModuleDirName() . '\\' . $module . '\\';
        }

        return $module . $this->getControllerDirName() . '\\' . ucfirst($router->getController()) . $this->getControllerExtName();
    }

    /**
     * 获取控制器类所在的目录名
     * @return string
     */
    public function getControllerDirName()
    {
        return $this->_controllerDirName;
    }

    /**
     * 设置控制器类所在的目录名
     * @param string $dirName
     * @return \tfc\mvc\Dispatcher
     */
    public function setControllerDirName($dirName)
    {
        $this->_controllerDirName = (string) $dirName;
        return $this;
    }

    /**
     * 获取控制器类名后缀
     * @return string
     */
    public function getControllerExtName()
    {
        return $this->_controllerExtName;
    }

    /**
     * 设置控制器类名后缀
     * @param string $extName
     * @return \tfc\mvc\Dispatcher
     */
    public function setControllerExtName($extName)
    {
        $this->_controllerExtName = (string) $extName;
        return $this;
    }

    /**
     * 获取模型所在的目录名
     * @return string
     */
    public function getModuleDirName()
    {
        return $this->_moduleDirName;
    }

    /**
     * 设置模型所在的目录名
     * @param string $dirName
     * @return \tfc\mvc\Dispatcher
     */
    public function setModuleDirName($dirName)
    {
        $this->_moduleDirName = (string) $dirName;
        return $this;
    }
}
