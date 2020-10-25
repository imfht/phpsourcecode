<?php
namespace Yurun\Swoole\SharedMemory\Store;

use Yurun\Swoole\SharedMemory\Interfaces\IKV;

class KV implements IKV
{
    /**
     * 存储的数据
     *
     * @var array
     */
    private $data = [];

    /**
     * 写入值
     *
     * @param string $name
     * @param mixed $value
     * @return boolean
     */
    public function set($name, $value)
    {
        $this->data[$name] = $value;
        return true;
    }

    /**
     * 获取值
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
        return array_key_exists($name, $this->data) ? $this->data[$name] : $default;
    }

    /**
     * 移除值
     *
     * @param string $name
     * @return boolean
     */
    public function remove($name)
    {
        if(array_key_exists($name, $this->data))
        {
            unset($this->data[$name]);
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * 是否存在
     *
     * @param string $name
     * @return boolean
     */
    public function exists($name)
    {
        return array_key_exists($name, $this->data);
    }

    /**
     * 清除
     *
     * @return void
     */
    public function clear()
    {
        $this->data = [];
        return true;
    }

    /**
     * 获取总的存储数据条数
     *
     * @return iont
     */
    public function count()
    {
        return count($this->data);
    }

}