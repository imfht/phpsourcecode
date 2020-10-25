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

use Iterator;
use JsonSerializable;
use Countable;

/**
 * 数据集合
 *
 * @package nb
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2017/12/30
 *
 * @property  boolean empty
 * @property  boolean have
 */
class Collection extends Access implements Iterator, JsonSerializable, Countable {

    /**
     * @var array
     */
    protected $row = [];

    /**
     * 数据堆栈
     * @var array
     */
    protected $stack = [];

    /**
     * Collection constructor.
     * @param array $data
     * @param bool $rows 是否为多维数据，
     *      当$rows = true时，多用于二维数组循环遍历，
     *      当$rows = false时，$data会直接赋值给_row，_stack为空，无法循环
     */
    public function __construct(array $data = []) {
        $this->stack = $data;
    }

    /**
     * 是否为空
     * @return bool
     */
    protected function _empty() {
        return empty($this->stack);
    }

    /**
     * 是否不为空
     * @return bool
     */
    protected function _have() {
        return !$this->empty;
    }


    /**
     * 获取原始堆栈数据
     * @return array
     */
    public function stack() {
        return $this->stack;
    }


    /**
     * 返回将已经缓存的数据覆盖堆栈原始数据
     * @return array
     */
    public function mingle() {
        return array_merge($this->stack,$this->tmp);
    }

    /**
     * 截取数组
     * stack
     * @param  int $offset
     * @param  int $length
     * @param  bool $preserveKeys
     * @return static
     */
    public function slice($offset, $length = null, $preserveKeys = false) {
        return new static(array_slice($this->stack, $offset, $length, $preserveKeys));
    }

    /**
     * 以相反的顺序返回数组。
     * stack
     * @return static
     */
    public function reverse() {
        $this->stack = array_reverse($this->stack);
        return $this;
    }

    /**
     * 删除数组中首个元素，并返回被删除元素的值
     * stack
     * @return mixed
     */
    public function shift() {
        $row = array_shift($this->stack);
        return new self($row);
    }

    /**
     * 删除数组的最后一个元素（出栈）
     * stack
     * @return mixed
     */
    public function pop() {
        $row = array_pop($this->stack);
        return new self($row);
    }

    /**
     * 把一个数组分割为新的数组块.
     * stack
     * @param  int $size
     * @param  bool $preserveKeys
     * @return static
     */
    public function chunk($size, $preserveKeys = false) {
        $chunks = [];

        foreach (array_chunk($this->stack, $size, $preserveKeys) as $chunk) {
            $chunks[] = new static($chunk);
        }

        return new static($chunks);
    }

    /**
     * 返回数组中所有的键名
     * row
     * @return static
     */
    public function keys() {
        return array_keys($this->stack);
    }

    /**
     * 交换数组中的键和值
     * row
     * @return static
     */
    public function flip() {
        return new static(array_flip($this->stack));
    }

    /**
     * 比较数组，返回交集
     * row
     * @param  mixed $items
     * @return static
     */
    public function intersect(array $items) {
        return new static(array_intersect($this->stack, $items));
    }

    /**
     * 比较数组，返回差集
     * row
     * @param  mixed $items
     * @return static
     */
    public function diff(array $items) {
        return new static(array_diff($this->stack, $items));
    }

    /**
     * 合并数组
     * row
     * @param  mixed $items
     * @return static
     */
    public function merge(array $items) {
        return new static(array_merge($this->stack, $items));
    }

    /**
     * 将数据压入堆栈
     *
     * @param array $value 每一行的值
     * @return array
     */
    public function push($value) {
        //将行数据按顺序置位
        $this->stack[] = $value;
        return $value;
    }

    /**
     * Iterator
     */
    public function rewind() {
        $this->tmp = [];
        reset($this->stack);
    }

    /**
     * Iterator
     */
    public function current() {
        $this->row = current($this->stack);
        if ($this->row) {
            return $this;
        }
        return false;
    }

    /**
     * Iterator
     */
    public function key() {
        return key($this->stack);
    }

    /**
     * Iterator
     */
    public function next() {
        $this->tmp = [];
        next($this->stack);
    }

    /**
     * Iterator
     */
    public function valid() {
        if(current($this->stack)) {
            return true;
        }
        return false;
    }

    /**
     * Countable
     * @return int
     */
    public function count() {
        return count($this->stack);
    }


    //JsonSerializable
    public function jsonSerialize() {
        return $this->toArray();
    }

    public function toArray() {
        $data = $this->stack?:$this->row;
        return array_map(function ($value) {
            return ($value instanceof self) ? $value->toArray() : $value;
        }, $data);
    }

    /**
     * 获取原始数据
     * @param $name
     * @return mixed|null
     */
    public function raw($name) {
        // TODO: Implement __get() method.
        if (isset($this->row[$name])) { //array_key_exists($name, $this->_row)
            return  $this->row[$name];
        }
        if(isset($this->stack[$name])){
            $this->row = &$this->stack;
            return $this->row[$name];
        }
        return null;
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
        if (isset($this->row[$name])) { //array_key_exists($name, $this->_row)
            return $this->tmp[$name] = $this->row[$name];
        }
        if(isset($this->stack[$name])){
            $this->row = &$this->stack;
            return $this->row[$name];
        }
        return null;
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
        if (isset($this->row[$name])) { //array_key_exists($name, $this->_row)
            return true;
        }
        if(isset($this->stack[$name])){
            $this->row = &$this->stack;
            return true;
        }
        return false;
    }

    public function __unset($name) {
        // TODO: Implement __unset() method.
        if(isset($this->tmp[$name])) {
            unset($this->tmp[$name]);
        }
        if(isset($this->stack[$name])) {
            unset($this->stack[$name]);
        }
        else if(isset($this->row[$name])) {
            unset($this->row[$name]);
        }

    }

    public function __call($name, $arguments) {
        // TODO: Implement __call() method.
        return $this;
    }


}