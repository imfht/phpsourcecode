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
 * Controller abstract class file
 * 控制器基类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Controller.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.mvc
 * @since 1.0
 */
abstract class Controller extends Application
{
    /**
     * @var string 默认的ActionID
     */
    protected $_defaultActionId = 'index';

    /**
     * 获取Action配置，包括所有Action的路径
     * @return array
     */
    public function actions()
    {
        return array();
    }

    /**
     * 请求Action类中的run方法
     * @param \tfc\mvc\Action $action
     * @return void
     */
    public function runAction(Action $action)
    {
        if ($this->preAction()) {
            if ($action->preRun()) {
                $action->run();
                $action->postRun();
                $this->postAction();
            }
        }
    }

    /**
     * 根据ActionId，创建Action类的实例
     * @param string $id
     * @return \tfc\mvc\Action
     */
    public function createActionById($id)
    {
        if (($id = (string) $id) === '') {
            $id = $this->_defaultActionId;
        }

        if (method_exists($this, $id . 'Action')) {
            return new InlineAction($this, $id);
        }

        return $this->createActionByMap($this->actions(), $id);
    }

    /**
     * 根据ActionId和Action配置，创建Action实例
     * @param array $maps
     * @param string $id
     * @return \tfc\mvc\Action
     * @throws ErrorException 如果Action类不存在，抛出异常
     * @throws ErrorException 如果获取的实例不是tfc\mvc\Action类的子类，抛出异常
     */
    public function createActionByMap(array $maps, $id)
    {
        $action = $this->getActionByMap($maps, $id);
        if (!class_exists($action)) {
            throw new ErrorException(sprintf(
                'Controller is unable to find the requested action "%s".', $action
            ));
        }

        $instance = new $action($this, $id);
        if (!$instance instanceof Action) {
            throw new ErrorException(sprintf(
                'Controller Action class "%s" is not instanceof tfc\mvc\Action.', $action
            ));
        }

        return $instance;
    }

    /**
     * 根据ActionId和Action配置，获取Action类的路径
     * @param array $maps
     * @param string $id
     * @return string
     * @throws ErrorException 如果Action类没有声明，抛出异常
     */
    public function getActionByMap(array $maps, $id)
    {
        if (isset($maps[$id])) {
            return $maps[$id];
        }

        throw new ErrorException(sprintf(
            'Controller is unable to find the action id "%s" in actions.', $id
        ));
    }

    /**
     * 需要子类重写此方法
     * 在Controller调用指定Action前调用的方法
     * 调用此方法后一定要返回true，后面方法才会执行
     * @return boolean
     */
    public function preAction()
    {
        return true;
    }

    /**
     * 需要子类重写此方法
     * 在Controller调用指定Action后调用的方法
     * 如果不想调用此方法，可以调用Action方法后exit;
     */
    public function postAction()
    {
    }
}
