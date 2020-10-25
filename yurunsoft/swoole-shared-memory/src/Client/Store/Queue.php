<?php
namespace Yurun\Swoole\SharedMemory\Client\Store;

use Yurun\Swoole\SharedMemory\Interfaces\IQueue;
use Yurun\Swoole\SharedMemory\Message\Operation;

class Queue extends Base implements IQueue
{
    /**
     * 队列是否为空
     *
     * @param string $name
     * @return boolean
     */
    public function empty($name)
    {
        return $this->doCall(new Operation('Queue', 'empty', [$name]));
    }

    /**
     * 弹出队列首个元素
     *
     * @param string $name
     * @return mixed|boolean
     */
    public function pop($name)
    {
        return $this->doCall(new Operation('Queue', 'pop', [$name]));
    }

    /**
     * 在队列尾部增加元素
     *
     * @param string $name
     * @param mixed $element
     * @return boolean
     */
    public function push($name, ...$element)
    {
        return $this->doCall(new Operation('Queue', 'push', array_merge([$name], $element)));
    }

    /**
     * 返回队列长度
     *
     * @param string $name
     * @return int
     */
    public function size($name)
    {
        return $this->doCall(new Operation('Queue', 'size', [$name]));
    }

    /**
     * 返回队首元素
     *
     * @param string $name
     * @return mixed
     */
    public function front($name)
    {
        return $this->doCall(new Operation('Queue', 'front', [$name]));
    }

    /**
     * 返回队尾元素
     *
     * @param string $name
     * @return void
     */
    public function back($name)
    {
        return $this->doCall(new Operation('Queue', 'back', [$name]));
    }

    /**
     * 清空队列
     *
     * @param string $name
     * @return void
     */
    public function clear($name)
    {
        return $this->doCall(new Operation('Queue', 'clear', [$name]));
    }

    /**
     * 获取数组
     *
     * @param string $name
     * @return array
     */
    public function getArray($name)
    {
        return $this->doCall(new Operation('Queue', 'getArray', [$name]));
    }

    /**
     * 获取实例对象
     *
     * @param string $name
     * @return \Yurun\Swoole\SharedMemory\Struct\PriorityQueue
     */
    public function getInstance($name)
    {
        return $this->doCall(new Operation('Queue', 'getInstance', [$name]));
    }

}