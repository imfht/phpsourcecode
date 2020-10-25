<?php

namespace SgIoc\Cache;
/**
 * 依赖注入容器
 * User: freelife2020@163.com
 * Date: 2018/3/16
 * Time: 12:50
 */

class CacheContainer
{
    protected static $data;

    public static function bind($name, \Closure $resolver)
    {
        static::$data[$name] = $resolver;
    }

    public static function make($name)
    {
        if (isset(static::$data[$name])) {
            $resolver = static::$data[$name];
            return $resolver();
        }
        throw new \Exception('Alias ' . $name . ' does not exist in the container register');
    }
}