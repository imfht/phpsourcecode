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
 * Singleton class file
 * 单例管理类，通过类名获取类的实例，并且保证在一次PHP的运行周期内只创建一次实例
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Singleton.php 1 2013-04-05 20:00:06Z huan.song $
 * @package tfc.ap
 * @since 1.0
 */
class Singleton
{
    /**
     * @var array 用于寄存全局的实例
     */
    protected static $_instances = array();

    /**
     * 根据完整的类名获取类的实例，适用于类的构造方法没有参数，如果类的构造方法有参数，不能只通过类名区分不同的类
     * @param string $className
     * @return object
     */
    public static function getInstance($className)
    {
        if (!self::has($className)) {
            self::set($className, new $className());
        }

        return self::get($className);
    }

    /**
     * 通过类名获取类的实例
     * @param string $className
     * @return object
     */
    public static function get($className)
    {
        if (self::has($className)) {
            return self::$_instances[$className];
        }

        return null;
    }

    /**
     * 设置类名和类的实例
     * @param string $className
     * @param object $instance
     * @return void
     */
    public static function set($className, $instance)
    {
        self::$_instances[$className] = $instance;
    }

    /**
     * 通过类名删除类的实例
     * @param string $className
     * @return void
     */
    public static function remove($className)
    {
        if (self::has($className)) {
            unset(self::$_instances[$className]);
        }
    }

    /**
     * 通过类名判断类的实例是否已经存在
     * @param string $className
     * @return boolean
     */
    public static function has($className)
    {
        return isset(self::$_instances[$className]);
    }
}
