<?php

namespace Huasuan\Misc;


class Menu
{
    private static $_instance;

    public function __construct()
    {

    }

    protected function _test()
    {
        return $this;
    }

    private static function _caller_($method, $args)
    {
        if (method_exists(self::$_instance, '_'.$method)) {
            return call_user_func_array(array(self::$_instance, '_'.$method), $args);
        } else {
            throw New \Exception('The method '.$method.' is not found in '.__CLASS__);
        }
    }

    public static function __callStatic($method, $args)
    {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self;
        }
        return self::_caller_($method, $args);
    }

    public function __call($method, $args)
    {
        return self::_caller_($method, $args);
    }
}