<?php declare(strict_types = 1);
namespace msqphp\core\container;

final class Module
{
    //已经实例化的服务
    private static $instances = [];

    public static function get(string $name)
    {
        if (!isset($instances[$name])) {
            $class_name = '\\app\\' . str_replace('_', '\\', $name);
            static::$instances[$name] = new $class_name();
        }
        return static::$instances[$name];
    }
}
