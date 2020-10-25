<?php
namespace Yurun\Swoole\SharedMemory\Interfaces;

interface IQueue
{
    /**
     * 队列是否为空
     *
     * @param string $name
     * @return boolean
     */
    public function empty($name);

    /**
     * 弹出队列首个元素
     *
     * @param string $name
     * @return mixed
     */
    public function pop($name);

    /**
     * 在队列尾部增加元素
     *
     * @param string $name
     * @param mixed $element
     * @return boolean
     */
    public function push($name, ...$element);

    /**
     * 返回队列长度
     *
     * @param string $name
     * @return int
     */
    public function size($name);

    /**
     * 返回队首元素
     *
     * @param string $name
     * @return mixed
     */
    public function front($name);

    /**
     * 返回队尾元素
     *
     * @param string $name
     * @return void
     */
    public function back($name);

    /**
     * 清空队列
     *
     * @param string $name
     * @return void
     */
    public function clear($name);

    /**
     * 获取数组
     *
     * @param string $name
     * @return array
     */
    public function getArray($name);

    /**
     * 获取实例对象
     *
     * @param string $name
     * @return \SplQueue
     */
    public function getInstance($name);
}