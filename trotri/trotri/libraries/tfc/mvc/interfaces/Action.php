<?php
/**
 * Trotri Foundation Classes
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright (c) 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace tfc\mvc\interfaces;

/**
 * Action interface file
 * Action接口，辅助分解Controller类业务，将Controller业务化整为零，方便管理和重用
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Action.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.mvc.interfaces
 * @since 1.0
 */
interface Action
{
    /**
     * 调用Action类的入口
     * @return void
     */
    public function run();

    /**
     * 获取控制器类
     * @return \tfc\mvc\Controller
     */
    public function getController();

    /**
     * 获取Action名
     * @return string
     */
    public function getId();
}
