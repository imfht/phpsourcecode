<?php
namespace Kernel\Abstract;

abstract class Singleton
{
    //存储实例
    private static $_instances=[];
    //获取实例
    public static function getInstance() {
        $className = self::getClassName();
        if (!isset(self::$_instances[$className])) {
            self::$_instances[$className] = new $className();
        }
        return self::$_instances[$className];
    }
    //删除实例
    public static function unsetInstance() {
        $className = self::getClassName();
        if (array_key_exists($className, self::$_instances)) {
            unset(self::$_instances[$className]);
        }
    }
    //获取类名
    final protected static function getClassName() {
        return get_called_class();
    }
}