<?php

namespace SgIoc\Cache;
/**
 * 存储抽象类
 * User: freelife2020@163.com
 * Date: 2018/3/26
 * Time: 16:02
 */
abstract class StoreAbstract implements StoreInterface
{
    protected $app;
    protected $config = array(//默认配置
        'preFix'         => '',//前缀
        'expired'        => 7200,//存储时间,分钟
        'is_zip'         => 0,//是否压缩
        'zip_level'      => 6, //压缩等级
        'forever_second' => 2592000,//30天
    );


    /**
     * 判断是否是匿名函数还是普通的值
     * @param $value
     * @return mixed
     */
    public function value($value)
    {
        return $value instanceof \Closure ? $value() : $value;
    }

    /**
     * igbinary serialize 序列化
     * @param $value mixed 支持匿名函数
     * @return string
     */
    public function serialize($value)
    {
        $value = $this->value($value);
        if (extension_loaded('igbinary')) {
            return igbinary_serialize($value);
        }
        return serialize($value);
    }

    /**
     * igbinary unserialize 解序列化
     * @param string $value 字符串
     * @return mixed
     */
    public function unserialize($value)
    {
        $value = $this->value($value);
        if (extension_loaded('igbinary')) {
            return igbinary_unserialize($value);
        }
        return unserialize($value);
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array(array($this->app, $name), $arguments);
    }
}