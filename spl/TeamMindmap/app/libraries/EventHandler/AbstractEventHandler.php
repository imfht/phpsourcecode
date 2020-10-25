<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14-12-10
 * Time: 上午10:30
 */

namespace Libraries\EventHandler;

/**
 * 事件订阅者的抽象类，封装事件订阅者的subscribe方法和提供一些辅助方法
 * Class AbstractEventHandler
 * @package Libraries\EventHandler
 */
abstract class AbstractEventHandler
{
    /**
     * 每个事件订阅者都必须定义的方法，用于设置所要监听的事件
　　  *
     * @param $events object
     */
    public function subscribe($events)
    {
        $this->config();
        $this->listenEvent($events);
    }


    /**
     * 设置监听的事件和处理方法
     *
     * @param $eventName string 要监听的事件名
     * @param $action string  处理事件的方法名
     */
    protected function setEvent($eventName, $action)
    {
        $this->eventAction[ $eventName ] = $action;
    }

    /**
     * 设置监听Eloquent发出的模型事件和处理方法
     *
     * @param $eloquentEventAction array 关联数组，存储Eloquent的事件名和对应的处理方法
     * @param $modelName string　对应的Eloquent模型名
     */
    protected function setEloquentEvent($eloquentEventAction = array(), $modelName)
    {
        foreach ( $eloquentEventAction as $eventName => $eloquentEventAction ) {
            $this->setEvent("eloquent.{$eventName}: {$modelName}", $this->handlerName.$eloquentEventAction);
        }
    }

    /**
     * 进行事件监听
     * 
     * @param $events object
     */
    private function listenEvent($events)
    {
        foreach ( $this->eventAction as $eventName => $this->eventAction ){
            $events->listen( $eventName, $this->eventAction );
        }
    }

    /**
     * 抽象方法，用于给子类进行事件监听的设置
     */
    protected abstract function config();

    protected $eventAction = [];  //关联数组，存储监听的事件名和对应的处理方法

    protected $handlerName;  //事件订阅者的名称

}
