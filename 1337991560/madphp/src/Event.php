<?php

/**
 * Event
 * @author 徐亚坤 hdyakun@sina.com
 */

namespace Madphp;
use Madphp\Event\Dispatcher;

class Event
{

    public static $instance = null;

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new Dispatcher();
        }
        return self::$instance;
    }

    private function __construct()
    {

    }

    public static function __callStatic($method, $parameters)
    {
        return call_user_func_array(array(self::getInstance(), $method), $parameters);
    }

    private function __clone() { }

    private function __wakeup() { }
}