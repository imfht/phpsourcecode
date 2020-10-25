<?php
/**
 * Created by PhpStorm.
 * User: man0sions
 * Date: 16/8/25
 * Time: 下午7:53
 */

namespace LuciferP\Orm\base;

/**
 * Class Registry
 * @package LuciferP\Orm\base
 * @author Luficer.p <81434146@qq.com>
 */
class Registry
{
    /**
     * @var array
     */
    protected  static  $objects = [];

    /**
     * @param $key
     * @return null
     */
    public static function get($key)
    {
        if (isset(static::$objects[$key])) {
            return static::$objects[$key];
        }
        return null;
    }

    /**
     * @param $key
     * @param $value
     */
    public static function set($key, $value)
    {
        static::$objects[$key] = $value;
    }

    /**
     * @param $key
     */
    public static function _unset($key)
    {
        unset(static::$objects[$key]);
    }
}