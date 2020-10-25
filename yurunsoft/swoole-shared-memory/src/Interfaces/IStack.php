<?php
namespace Yurun\Swoole\SharedMemory\Interfaces;

interface IStack
{
    /**
     * 栈是否为空
     *
     * @param string $name
     * @return boolean
     */
    public function empty($name);

    /**
     * 弹出栈顶元素
     *
     * @param string $name
     * @return mixed
     */
    public function pop($name);

    /**
     * 在栈底增加元素
     *
     * @param string $name
     * @param mixed $element
     * @return boolean
     */
    public function push($name, ...$element);

    /**
     * 返回栈中元素数目
     *
     * @param string $name
     * @return int
     */
    public function size($name);

    /**
     * 返回栈顶元素
     *
     * @param string $name
     * @return mixed
     */
    public function top($name);

    /**
     * 清空栈
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
     * @return \SplStack
     */
    public function getInstance($name);
}