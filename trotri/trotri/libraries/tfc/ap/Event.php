<?php
/**
 * Trotri Foundation Classes
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright (c) 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace tfc\ap;

/**
 * Event abstract class file
 * observer模式的事件基类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Event.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.ap
 * @since 1.0
 */
abstract class Event extends Application
{
    /**
     * @var boolean 事件是否已经被注册
     */
    protected $_enabled = false;

    /**
     * @var instance of tfc\ap\EventDispatcher
     */
    protected $_owner = null;

    /**
     * 将本类注册到事件处理类
     * @param \tfc\ap\EventDispatcher $owner
     * @return void
     */
    public function attach(EventDispatcher $owner)
    {
        $this->_enabled = true;
        $this->_owner = $owner;

        $owner->attach($this);
    }

    /**
     * 将本类从事件处理类中销毁
     * @param \tfc\ap\EventDispatcher $owner
     * @return void
     */
    public function detach(EventDispatcher $owner)
    {
        $owner->detach($this);

        $this->_enabled = false;
        $this->_owner = null;
    }

    /**
     * 判断事件是否已经被注册
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->_enabled;
    }

    /**
     * 获取事件处理类
     * @return \tfc\ap\EventDispatcher
     */
    public function getOwner()
    {
        return $this->_owner;
    }
}
