<?php
namespace Yurun\Swoole\SharedMemory\Interfaces;

interface IKV
{
    /**
     * 写入值
     *
     * @param string $name
     * @param mixed $value
     * @return boolean
     */
    public function set($name, $value);

    /**
     * 获取值
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function get($name, $default = null);

    /**
     * 移除值
     *
     * @param string $name
     * @return boolean
     */
    public function remove($name);

    /**
     * 是否存在
     *
     * @param string $name
     * @return boolean
     */
    public function exists($name);

    /**
     * 清除
     *
     * @return void
     */
    public function clear();

    /**
     * 获取总的存储数据条数
     *
     * @return iont
     */
    public function count();

    
}