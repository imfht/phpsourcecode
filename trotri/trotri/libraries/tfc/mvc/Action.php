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
use tfc\mvc\interfaces;

/**
 * Action abstract class file
 * Action基类，辅助分解Controller类业务，将Controller业务化整为零，方便管理和重用
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Action.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.mvc
 * @since 1.0
 */
abstract class Action extends Application implements interfaces\Action
{
    /**
     * @var string Action名
     */
    private $_id;

    /**
     * @var instance of tfc\mvc\Controller
     */
    private $_controller;

    /**
     * 构造方法：初始化控制器类和Action名
     * @param \tfc\mvc\Controller $controller
     * @param string $id
     */
    public function __construct(Controller $controller, $id)
    {
        $this->_controller = $controller;
        $this->_id = $id;
        $this->_init();
    }

    /**
     * 子类构造方法：子类调用此方法作为构造方法，避免重写父类构造方法
     */
    protected function _init()
    {
    }

    /**
     * (non-PHPdoc)
     * @see \tfc\mvc\interfaces\Action::getController()
     */
    public function getController()
    {
        return $this->_controller;
    }

    /**
     * (non-PHPdoc)
     * @see \tfc\mvc\interfaces\Action::getId()
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * 需要子类重写此方法
     * 在Action调用run方法前调用的方法
     * 调用此方法后一定要返回true，后面方法才会执行
     * @return boolean
     */
    public function preRun()
    {
        return true;
    }

    /**
     * 需要子类重写此方法
     * 在Action调用run方法后调用的方法
     * 如果不想调用此方法，可以调用run方法后exit;
     */
    public function postRun()
    {
    }
}
