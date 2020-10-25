<?php
/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\Base;

use \ArrayObject;
use \Cute\Cache\BaseCache;


/**
 * 存储容器
 */
class Storage extends ArrayObject
{
    /**
     * 构造函数，flags默认为ARRAY_AS_PROPS
     */
    public function __construct($input = null, $flags = ArrayObject::ARRAY_AS_PROPS)
    {
        parent::__construct($input ?: [], $flags);
    }

    /**
     * 读取配置项
     */
    public function getItem($name, $default = null)
    {
        $item = @$this->offsetGet($name);
        return is_null($item) ? $default : $item;
    }

    /**
     * 读取配置项，但不区分大小写
     */
    public function getItemInsensitive($name, $default = null)
    {
        if ($this->offsetExists($name)) {
            return $this->getItem($name, $default);
        }
        $low_name = strtolower($name);
        if ($name !== $low_name) {
            //将对象的key都改为小写
            $low_copy = array_change_key_case($this->getArrayCopy());
            $this->exchangeArray($low_copy);
            return $this->getItem($low_name, $default);
        }
    }

    /**
     * 读取数组类配置项
     */
    public function getArray($name, array $default = [], $insensitive = false)
    {
        if ($insensitive) {
            $data = $this->getItemInsensitive($name);
        } else {
            $data = $this->getItem($name);
        }
        return is_array($data) ? $data : $default;
    }

    /**
     * 读取配置区
     */
    public function getSection($name, $insensitive = false)
    {
        $data = $this->getArray($name, [], $insensitive);
        return new self($data);
    }

    /**
     * 读取配置区，并缓存起来
     */
    public function getSectionOnce($name, $insensitive = false)
    {
        if ($insensitive) {
            $data = $this->getItemInsensitive($name);
        } else {
            $data = $this->getItem($name);
        }
        if (!($data instanceof self)) {
            $data = new self($data);
            $this->offsetSet($name, $data);
        }
        return $data;
    }
}
