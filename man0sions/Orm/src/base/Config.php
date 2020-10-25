<?php
/**
 * Created by PhpStorm.
 * User: man0sions
 * Date: 16/8/25
 * Time: 下午8:50
 */

namespace LuciferP\Orm\base;

/**
 * 把配置文件当作对象操作
 *
 * Class Config
 * @package src\base
 */
class Config implements \ArrayAccess
{
    private $path;

    private $objects = [];

    function __construct($file)
    {
        $this->path = $file;
    }

    function offsetExists($offset)
    {
        return isset($this->objects[$offset]);
    }
    function offsetGet($offset)
    {
        try{
            $this->objects[$offset] = require $this->path;
        }
        catch(\Exception $e)
        {
            throw new AppException("{$this->path} not exists");

        }
        return $this->objects[$offset];

    }
    function offsetSet($offset, $value)
    {
        throw new AppException("can't set config");
    }
    function offsetUnset($offset)
    {
        unset($this->objects[$offset]);
    }

}