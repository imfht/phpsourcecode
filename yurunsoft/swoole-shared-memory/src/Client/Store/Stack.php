<?php
namespace Yurun\Swoole\SharedMemory\Client\Store;

use Yurun\Swoole\SharedMemory\Message\Operation;
use Yurun\Swoole\SharedMemory\Interfaces\IStack;

class Stack extends Base implements IStack
{
    /**
     * 栈是否为空
     *
     * @param string $name
     * @return boolean
     */
    public function empty($name)
    {
        return $this->doCall(new Operation('Stack', 'empty', [$name]));
    }

    /**
     * 弹出栈顶元素
     *
     * @param string $name
     * @return mixed
     */
    public function pop($name)
    {
        return $this->doCall(new Operation('Stack', 'pop', [$name]));
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
        return $this->doCall(new Operation('Stack', 'push', array_merge([$name], $element)));
    }

    /**
     * 返回栈中元素数目
     *
     * @param string $name
     * @return int
     */
    public function size($name)
    {
        return $this->doCall(new Operation('Stack', 'size', [$name]));
    }

    /**
     * 返回栈顶元素
     *
     * @param string $name
     * @return mixed
     */
    public function top($name)
    {
        return $this->doCall(new Operation('Stack', 'top', [$name]));
    }

    /**
     * 清空栈
     *
     * @param string $name
     * @return void
     */
    public function clear($name)
    {
        return $this->doCall(new Operation('Stack', 'clear', [$name]));
    }

    /**
     * 获取数组
     *
     * @param string $name
     * @return array
     */
    public function getArray($name)
    {
        return $this->doCall(new Operation('Stack', 'getArray', [$name]));
    }

    /**
     * 获取实例对象
     *
     * @param string $name
     * @return \Yurun\Swoole\SharedMemory\Struct\PriorityQueue
     */
    public function getInstance($name)
    {
        return $this->doCall(new Operation('Stack', 'getInstance', [$name]));
    }

}