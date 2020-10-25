<?php
namespace workerbase\classs;

/**
 * 附加事件处理
 */
class AttachEvent {

    /**
     * 事件处理器数组
     */
    private static $_event = [];

    public static function attachEventHandler($eventName, callable $handler){
        self::$_event[$eventName][md5(var_export($handler, true))] = $handler;
    }

    //程序执行前事件
    public static function onBeginRequest(){
       if (isset(self::$_event['onBeginRequest'])) {
           foreach (self::$_event['onBeginRequest'] as $callable) {
               if (is_object($callable)) {
                   call_user_func($callable);
               } elseif (is_array($callable)) {
                   call_user_func([$callable[0], $callable[1]]);
               }
           }
       }

       return true;
    }

    //程序退出前事件
    public static function onEndRequest(){
       if (isset(self::$_event['onEndRequest'])) {
           foreach (self::$_event['onEndRequest'] as $callable) {
               if (is_object($callable)) {
                   call_user_func($callable);
               } elseif (is_array($callable)) {
                   call_user_func([$callable[0], $callable[1]]);
               }
           }
       }
       return true;
    }

    /**
     * 判断事件处理器是否存在
     * @param $eventName -事件名
     * @return bool
     */
    public static function hasEventHandler($eventName)
    {
        if (isset(self::$_event[$eventName])) {
            return true;
        }
        return false;
    }

    /**
     * 清除事件处理器
     * @return mixed
     */
    public static function clearEvent()
    {
        self::$_event = [];
    }

}
