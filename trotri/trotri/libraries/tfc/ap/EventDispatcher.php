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
 * EventDispatcher class file
 * observer模式的事件处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: EventDispatcher.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.ap
 * @since 1.0
 */
class EventDispatcher
{
    /**
     * @var instances of tfc\ap\Event
     */
    protected $_events = array();

    /**
     * @var array 被观察事件的方法
     */
    protected $_methods = array();

    /**
     * @var instance of tfc\ap\EventDispatcher
     */
    protected static $_instance = null;

    /**
     * 构造方法：禁止被实例化
     */
    protected function __construct()
    {
    }

    /**
     * 魔术方法：禁止被克隆
     */
    private function __clone()
    {
    }

    /**
     * 单例模式：获取本类的实例化对象
     * @return \tfc\ap\EventDispatcher
     */
    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * 通过事件名，批量注册被观察的事件
     * @param array $eventNames
     * @return void
     */
    public function loadEvents(array $eventNames)
    {
        if ($eventNames === array()) {
            return ;
        }

        foreach ($eventNames as $eventName)
        {
            $instance = $this->createEvent($eventName);
            $this->attach($instance);
        }
    }

    /**
     * 通过被观察的事件名，创建被观察的事件类
     * @param string $eventName
     * @throws ErrorException 如果事件类不存在，抛出异常
     * @throws ErrorException 如果获取的实例不是tfc\ap\Event类的子类，抛出异常
     * @return \tfc\ap\Event
     */
    public function createEvent($eventName)
    {
        if (!class_exists($eventName)) {
            throw new ErrorException(sprintf(
                'EventDispatcher is unable to find the requested event "%s".', $eventName
            ));
        }

        $instance = new $eventName();
        if (!$instance instanceof Event) {
            throw new ErrorException(sprintf(
                'EventDispatcher Class "%s" is not instanceof tfc\ap\Event.', $eventName
            ));
        }

        return $instance;
    }

    /**
     * 触发所有被观察的方法
     * @param string $method
     * @param array $args
     * @return array
     */
    public function trigger($method, $args = array())
    {
        $result = array();

        if (!$this->hasMethod($method) || empty($this->_methods[$method])) {
            return $result;
        }

        $args = (array) $args;

        foreach ($this->_methods[$method] as $handler) {
            if (!isset($this->_events[$handler])) {
                continue;
            }

            if (!($instance = $this->_events[$handler]) instanceof Event) {
                continue;
            }

            if (!method_exists($instance, $method)) {
                continue;
            }

            if (($value = call_user_func_array(array($instance, $method), $args)) !== null) {
                $result[] = $value;
            }
        }

        return $result;
    }

    /**
     * 注册一个被观察的事件
     * @param \tfc\ap\Event $event
     * @return void
     */
    public function attach(Event $event)
    {
        $handler = strtolower(get_class($event));
        if ($this->hasEvent($handler)) {
            return ;
        }

        $this->_events[$handler] = $event;

        $methods = $this->getAttachableMethods($event);
        foreach ($methods as $method) {
            if (!$this->hasMethod($method)) {
                $this->_methods[$method] = array();
            }

            $this->_methods[$method][] = $handler;
        }
    }

    /**
     * 销毁一个被观察的事件
     * @param \tfc\ap\Event $event
     * @return void
     */
    public function detach(Event $event) {
        $handler = strtolower(get_class($event));
        if (!$this->hasEvent($handler)) {
            return ;
        }

        unset($this->_events[$handler]);

        foreach ($this->_methods as $method => $handlers) {
            if (!method_exists($event, $method)) {
                continue;
            }

            if (($key = array_search($handler, $handlers)) !== false) {
                unset($this->_methods[$method][$key]);
            }
        }
    }

    /**
     * 获取一个事件所有可绑定的方法
     * @param \tfc\ap\Event $event
     * @return array
     */
    public function getAttachableMethods(Event $event)
    {
        $result = array();

        $ref = new \ReflectionClass($event);
        $methods = $ref->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            $name = $method->getName();

            if ($this->isAttachable($name)) {
                $result[] = $name;
            }
        }

        return $result;
    }

    /**
     * 判断事件的方法是否可被注册
     * @param string $method
     * @return boolean
     */
    public function isAttachable($method)
    {
        return (strncasecmp($method, 'on', 2) === 0);
    }

    /**
     * 通过名称判断事件是否已经存在
     * @param string $name
     * @return boolean
     */
    public function hasEvent($name)
    {
        return isset($this->_events[$name]);
    }

    /**
     * 通过名称判断事件的方法是否已经存在
     * @param string $name
     * @return boolean
     */
    public function hasMethod($name)
    {
        return isset($this->_methods[$name]);
    }
}
