<?php
/**
 * Created by PhpStorm.
 * User: man0sions
 * Date: 16/7/26
 * Time: 上午11:14
 */
namespace LuciferP\TinyMvc\base;



class Config implements \ArrayAccess{
    private $path;
    protected $config;
    function __construct($path)
    {
        $this->path = $path;
    }

    function offsetGet($offset)
    {
        $file = $this->path."/".$offset.".php";
        try{
            $this->config[$offset] = require  $file;
        }
        catch(Exception $e)
        {
            var_dump($e);
        }
        return $this->config[$offset];
    }
    function offsetExists($offset)
    {
        return isset($this->config[$offset]);
    }
    function offsetSet($offset, $value)
    {
        // TODO: Implement offsetSet() method.
    }
    function offsetUnset($offset)
    {
        unset($this->config[$offset]);
    }
}