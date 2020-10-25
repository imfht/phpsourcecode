<?php
/**
 * Created by Wenlong li
 * User: wenlong
 * Date: 2018/9/19
 * Time: 上午10:21
 */

namespace Kernel\Core\Conf\Type;


use Kernel\Core\Conf\IConfig;

class YafConfig implements IConfig,\Iterator
{
    /**
     * @var \Yaf_Config_Ini $yafConfig
     */
    protected $yafConfig;
    public function __construct()
    {
    }

    /**
     *
     *获取配置节点的值
     *当不传递$name参数时，返回配置对象本身
     * @example
     *
     * @param string $name  配置键
     * @param mixed  $value 默认值
     *
     * @return mixed
     */
    public function get(string $name, $value)
    {
        return $this->yafConfig->get($name, $value);
    }

    /**
     *
     *设置配置节点的值(只读)
     * @example
     * @return \Yaf_Config_Abstract
     */
    public function set(): Yaf_Config_Abstract
    {
        return $this->yafConfig;
    }

    /**
     *
     *配置是否只读
     * @example
     * @return bool
     */
    public function readonly(): bool
    {
        return $this->yafConfig->readonly();
    }

    /**
     *
     *将配置转换为数组
     * @example
     * @return array
     */
    public function toArray(): array
    {
        return $this->yafConfig->toArray();
    }

    /**
     *
     *返回当前值
     * @example
     * @return mixed
     */
    public function current()
    {
        return $this->yafConfig->current();
    }

    /**
     *
     *返回下一个值
     * @example
     * @return mixed
     */
    public function next()
    {
        return $this->yafConfig->next();
    }

    /**
     *
     *返回当前键
     * @example
     * @return string
     */
    public function key()
    {
        return $this->yafConfig->key();
    }

    /**
     *
     *验证当前对象是否合法
     * @example
     * @return boolean
     */
    public function valid()
    {
        return $this->yafConfig->valid();
    }

    /**
     *
     *将迭代器重置到第一个位置
     * @example
     * @return \Kernel\Core\Conf\Type\YafConfig
     */
    public function rewind()
    {
        $this->yafConfig->rewind();
        return $this;
    }

    /**
     *
     *返回当前迭代器的个数
     * @example
     * @return int
     */
    public function count()
    {
        return $this->yafConfig->count();
    }

    /**
     *
     *当前索引对应的对象是否存在
     * @example
     *
     * @param string $offset 索引
     *
     * @return boolean
     */
    public function offsetExists(string $offset)
    {
        return $this->yafConfig->offsetExists($offset);
    }

    /**
     *
     *通过索引获取当前对象
     * @example
     *
     * @param string $offset 索引
     *
     * @return mixed
     */
    public function offsetGet(string $offset)
    {
        return $this->yafConfig->offsetGet($offset);
    }

    /**
     *
     *通过索引设置值
     * @example
     *
     * @param string $offset 索引
     * @param mixed  $value  配置值
     *
     * @return \Kernel\Core\Conf\Type\YafConfig
     */
    public function offsetSet(string $offset, $value)
    {
        $this->yafConfig->offsetSet($offset, $value);
        return $this;
    }

    /**
     *
     *通过索引删除值
     * @example
     *
     * @param string $offset 索引
     *
     * @return \Kernel\Core\Conf\Type\YafConfig
     */
    public function offsetUnset(string $offset){
        $this->yafConfig->offsetUnset($offset);
        return $this;
    }

    public function load(string $filename): array
    {
        $this->yafConfig = new \Yaf_Config_Ini($filename);
        return $this->yafConfig->toArray();
    }

    public function supports(string $filename): bool
    {
        return (bool) preg_match('#\.ini(\.dist)?$#', $filename);
    }
}