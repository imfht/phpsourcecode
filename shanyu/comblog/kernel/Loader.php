<?php
namespace Kernel;

use Pimple\Container as Container;

class Loader extends Container
{
    protected static $instance;
    
    public static function singleton($key='')
    {
        $instance = self::instance();

        if(empty($key)){
            return $instance;
        }
        if (!$instance->offsetExists($key)) {
            $class = new $key();
            $instance->offsetSet($key,$class);
            return $class;
        }
        return self::instance()->offsetGet($key);
    }

    public static function instance()
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __get($key)
    {
        return self::$instance->offsetGet($key);
    }
    public function __set($key,$value)
    {
        return self::$instance->offsetSet($key,$value);
    }

}