<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/1
 * Time: 19:07
 */

namespace fastwork;


class Facade
{
    /**
     * 绑定对象
     * @var array
     */
    protected static $bind = [];

    /**
     * 始终创建新的对象实例
     * @var bool
     */
    protected static $alwaysNewInstance;


    /**
     * 创建Facade实例
     * @static
     * @access protected
     * @param  string $class 类名或标识
     * @param  array $args 变量
     * @param  bool $newInstance 是否每次创建新的实例
     * @return object
     * @throws \ReflectionException
     * @throws exception\ClassNotFoundException
     */
    protected static function createFacade($class = '', $args = [], $newInstance = false)
    {
        $class = $class ?: static::class;

        $facadeClass = static::getFacadeClass();

        if ($facadeClass) {
            $class = $facadeClass;
        } elseif (isset(self::$bind[$class])) {
            $class = self::$bind[$class];
        }

        if (static::$alwaysNewInstance) {
            $newInstance = true;
        }

        return Container::getInstance()->make($class, $args, $newInstance);
    }

    /**
     * 获取当前Facade对应类名（或者已经绑定的容器对象标识）
     * @access protected
     * @return string
     */
    protected static function getFacadeClass()
    {}

    // 调用实际类的方法
    public static function __callStatic($method, $params)
    {
        return call_user_func_array([static::createFacade(), $method], $params);
    }
}