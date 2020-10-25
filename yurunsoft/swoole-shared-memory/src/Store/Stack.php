<?php
namespace Yurun\Swoole\SharedMemory\Store;

use Yurun\Swoole\SharedMemory\Interfaces\IStack;

class Stack implements IStack
{
    /**
     * 存储的数据
     *
     * @var array
     */
    private $data = [];

    /**
     * 栈是否为空
     *
     * @param string $name
     * @return boolean
     */
    public function empty($name)
    {
        return $this->getInstance($name)->isEmpty();
    }

    /**
     * 弹出栈顶元素
     *
     * @param string $name
     * @return mixed|boolean
     */
    public function pop($name)
    {
        $stack = $this->getInstance($name);
        return $stack->isEmpty() ? false : $stack->pop();
    }

    /**
     * 在栈底增加元素
     *
     * @param string $name
     * @param mixed $element
     * @return boolean
     */
    public function push($name, ...$element)
    {
        $result = true;
        foreach($element as $e)
        {
            $result &= $this->getInstance($name)->push($e);
        }
        return $result;
    }

    /**
     * 返回栈中元素数目
     *
     * @param string $name
     * @return int
     */
    public function size($name)
    {
        return $this->getInstance($name)->count();
    }

    /**
     * 返回栈顶元素
     *
     * @param string $name
     * @return mixed
     */
    public function top($name)
    {
        $stack = $this->getInstance($name);
        return $stack->isEmpty() ? false : $stack->top();
    }

    /**
     * 清空栈
     *
     * @param string $name
     * @return void
     */
    public function clear($name)
    {
        $this->data[$name] = new \SplStack;
    }

    /**
     * 获取数组
     *
     * @param string $name
     * @return array
     */
    public function getArray($name)
    {
        $stack = clone $this->getInstance($name);
        return iterator_to_array($stack);
    }

    /**
     * 获取实例对象
     *
     * @param string $name
     * @return \SplQueue
     */
    public function getInstance($name)
    {
        if(!isset($this->data[$name]))
        {
            $this->data[$name] = new \SplQueue;
        }
        return $this->data[$name];
    }

}