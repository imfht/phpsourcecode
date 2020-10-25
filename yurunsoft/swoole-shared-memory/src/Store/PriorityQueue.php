<?php
namespace Yurun\Swoole\SharedMemory\Store;

use Yurun\Swoole\SharedMemory\Interfaces\IPriorityQueue;

class PriorityQueue implements IPriorityQueue
{
    /**
     * 存储的数据
     *
     * @var array
     */
    private $data = [];

    /**
     * 队列是否为空
     *
     * @param string $name
     * @return boolean
     */
    public function empty($name)
    {
        return $this->getInstance($name)->isEmpty();
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
        return $this->getInstance($name)->insert($element, $priority);
    }

    /**
     * 弹出一个元素
     *
     * @param string $name
     * @return mixed
     */
    public function extract($name)
    {
        $queue = $this->getInstance($name);
        return $queue->isEmpty() ? false : $queue->extract();
    }

    /**
     * 返回队列长度
     *
     * @param string $name
     * @return int
     */
    public function size($name)
    {
        return $this->getInstance($name)->count();
    }

    /**
     * 清空队列
     *
     * @param string $name
     * @return void
     */
    public function clear($name)
    {
        $this->data[$name] = new \Yurun\Swoole\SharedMemory\Struct\PriorityQueue;
    }

    /**
     * 获取数组
     *
     * @param string $name
     * @return array
     */
    public function getArray($name)
    {
        $queue = clone $this->getInstance($name);
        return iterator_to_array($queue);
    }

    /**
     * 获取实例对象
     *
     * @param string $name
     * @return \Yurun\Swoole\SharedMemory\Struct\PriorityQueue
     */
    public function getInstance($name)
    {
        if(!isset($this->data[$name]))
        {
            $this->data[$name] = new \Yurun\Swoole\SharedMemory\Struct\PriorityQueue;
        }
        return $this->data[$name];
    }

}