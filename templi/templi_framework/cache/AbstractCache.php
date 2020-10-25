<?php
/**
 * Email:739800600@qq.com
 * User: 七觞酒
 * Date: 2014/10/7
 */
namespace framework\cache;

abstract class AbstractCache
{

    /**
     * 获取缓存内容
     * @param string $key
     * @return mixed
     */
    abstract public function get($key);

    /**
     * 设置缓存
     *
     * @param string $key
     * @param mixed $value
     * @param int $expire
     * @return mixed
     */
    abstract public function set($key, $value, $expire=null);

    /**
     * 清除缓存
     * $name 为空时清除全部缓存
     * @param string $key
     * @return mixed
     */
    abstract public function clean($key=null);

    /**
     * 判断之指定key的缓存是否存在
     * @param string $key
     * @return bool
     */
    public function exists($key)
    {
        return $this->get($key) !== false;
    }
    /**
     * 获取key
     * @param string $key
     * @return string
     */
    public function buildKey($key)
    {
        if(is_string($key)){
            return strlen($key) <= 32? $key : md5($key);
        } else {
            return md5(json_encode($key));
        }

    }
} 