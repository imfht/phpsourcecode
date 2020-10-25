<?php
/**
 * 储存工厂类
 * @author lajox <lajox@19www.com>
 */
namespace Took;
abstract class Storage
{
    //处理程序
    static private $handler = null;

    static public function init($driver = 'File')
    {
        if (is_null(self::$handler)) {
            self::connect($driver);
        }
        return self::$handler;
    }

    //驱动连接
    static public function connect($driver = '')
    {
        $driver = empty($driver) ? C('STORAGE_DRIVER') : $driver;
        $class = '\\Took\\Storage\\Driver\\'.$driver;
        self::$handler = new $class;
    }

    //调用驱动的方法
    public function __call($method, $args)
    {
        if (method_exists(self::$handler, $method)) {
            return call_user_func_array(array(self::$handler, $method), $args);
        }
    }

    //调用驱动的静态方法
    static public function __callStatic($method, $args){
        if(method_exists(self::$handler, $method)){
            return call_user_func_array(array(self::$handler,$method), $args);
        }
    }

}
