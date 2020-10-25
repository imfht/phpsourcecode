<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nb;

use ArrayAccess;

/**
 * 数据包装类
 *
 * @package nb
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2017/3/30
 */
abstract class Access implements ArrayAccess {

    /**
     * 缓存处理过的数据
     * @var array
     */
    protected $tmp = [];


    public function tmp($name,$value=null) {
        return $this->tmp[$name] = $value;
    }

    /**
     * 检查偏移位置是否存在
     * ArrayAccess
     * @param mixed $key
     * @return bool
     */
    public function offsetExists($name) {
        return isset($this->$name);//$this->__isset($name);//  isset($this->_row[$key]);
    }

    /**
     * 设置一个偏移位置的值
     * ArrayAccess
     * @param mixed $key
     * @param mixed $value
     */
    public function offsetSet($name, $value) {
        $this->$name = $value;
    }

    /**
     * 获取一个偏移位置的值
     * ArrayAccess
     * @param mixed $key
     * @return mixed|null
     */
    public function offsetGet($name) {
        //此种方式，可以在以数组形式访问时，直接读取public属性
        return $this->$name;
    }

    /**
     * 复位一个偏移位置的值
     * ArrayAccess
     * @param mixed $key
     */
    public function offsetUnset($name) {
        unset($this->$name);
        //if(isset($this->_row[$key])) {
        //    unset($this->_row[$key]);
        //}
    }

    public function __unset($name) {
        // TODO: Implement __unset() method.
        if(isset($this->tmp[$name])) {
            unset($this->tmp[$name]);
        }
    }

    /**
     * 设定堆栈的值
     *
     * @param string $name 值对应的键值
     * @param mixed $value 相应的值
     * @return void
     */
    public function __set($name, $value) {
        // TODO: Implement __set() method.
        $method = '___' . $name;

        if (method_exists($this, $method)) {
            $this->tmp[$name] = $this->$method($value);
        }
        else {
            $this->tmp[$name] = $value;
        }
        //if(property_exists(get_class($this), $name)) {
        //    $this->$name = $value;
        //    e($name, $value,$this->$name);
        //}

        /*
        if(property_exists(get_class($this), $name)) {
            $this->$name = $value;
            e($name, $value,$this->$name);
        }
        $method = 'set' . $name;
        if (method_exists($this, $method)) {
            $this->$method($value);
        }

        if(isset($this->_tmp[$name])) {
            $this->_tmp[$name] = $value;
        }
        */
        //if(isset($this->_row[$name])) {
        //    return $this->_row[$name] = $value;
        //}
    }

    /**
     * 获取当前行中的值
     * @param $name
     * @return mixed|null
     */
    public function __get($name) {
        // TODO: Implement __get() method.
        if(isset($this->tmp[$name])) {
            return $this->tmp[$name];
        }

        $method = '_' . $name;
        if (method_exists($this, $method)) {
            return $this->tmp[$name] = $this->$method();
        }

        //if(property_exists($this, $name)) {
        //    return $this->tmp[$name] = $this->$name;
        //}

        return $this->tmp[$name] = null;
    }

    /**
     * 验证堆栈值是否存在
     *
     * @access public
     * @param string $name
     * @return boolean
     */
    public function __isset($name) {
        if(isset($this->tmp[$name])) {
            return true;
        }
        if (method_exists($this, '_' . $name)) {
            return true;
        }
        return false;
    }

    public function __call($name, $arguments) {
        // TODO: Implement __call() method.
        return $this;
    }


}