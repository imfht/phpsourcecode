<?php
namespace Yurun\Swoole\SharedMemory\Client\Store;

use Yurun\Swoole\SharedMemory\Message\Operation;
use Yurun\Swoole\SharedMemory\Interfaces\IPriorityQueue;

class PriorityQueue extends Base implements IPriorityQueue
{
    /**
     * 队列是否为空
     *
     * @param string $name
     * @return boolean
     */
    public function empty($name)
    {
        return $this->doCall(new Operation('PriorityQueue', 'empty', [$name]));
    }

    /**
     * 插入
     *
     * @param string $name
     * @param mixed $name
     * @param mixed $priority
     * @return int
     */
    public function insert($name, $element, $priority)
    {
        return $this->doCall(new Operation('PriorityQueue', 'insert', [$name, $element, $priority]));
    }

    /**
     * 弹出一个元素
     *
     * @param string $name
     * @return mixed
     */
    public function extract($name)
    {
        return $this->doCall(new Operation('PriorityQueue', 'extract', [$name]));
    }

    /**
     * 返回队列长度
     *
     * @param string $name
     * @return int
     */
    public function size($name)
    {
        return $this->doCall(new Operation('PriorityQueue', 'size', [$name]));
    }

    /**
     * 清空队列
     *
     * @param string $name
     * @return void
     */
    public function clear($name)
    {
        return $this->doCall(new Operation('PriorityQueue', 'clear', [$name]));
    }

    /**
     * 获取数组
     *
     * @param string $name
     * @return array
     */
    public function getArray($name)
    {
        return $this->doCall(new Operation('PriorityQueue', 'getArray', [$name]));
    }

    /**
     * 获取实例对象
     *
     * @param string $name
     * @return \Yurun\Swoole\SharedMemory\Struct\PriorityQueue
     */
    public function getInstance($name)
    {
        return $this->doCall(new Operation('PriorityQueue', 'getInstance', [$name]));
    }

}