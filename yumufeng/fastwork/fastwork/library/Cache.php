<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/2
 * Time: 13:10
 */

namespace fastwork;


use fastwork\cache\Redis;

class Cache
{

    protected $redis;

    public function __construct(Redis $redis)
    {
        $this->redis = $redis;
    }

    /**
     * 判断缓存
     * @access public
     * @param  string $name 缓存变量名
     * @return bool
     */
    public function has(string $name)
    {
        return $this->redis->exists($this->getCacheKey($name));
    }

    /**
     * 读取缓存
     * @access public
     * @param  string $name 缓存变量名
     * @param  mixed $default 默认值
     * @return mixed
     */
    public function get($name, $default = false)
    {
        $value = $this->redis->get($this->getCacheKey($name));

        if (is_null($value) || false === $value) {
            return $default;
        }

        return unserialize($value);
    }

    /**
     * 是否立即返回数据
     * @param bool $status
     * @return Cache
     */
    public function setDefer($status = true)
    {
        $this->redis->setDefer($status);
        return $this;
    }

    /**
     * 写入缓存
     * @access public
     * @param  string $name 缓存变量名
     * @param  mixed $value 存储数据
     * @param  integer|\DateTime $expire 有效时间（秒）
     * @return boolean
     */
    public function set($name, $value, $expire = null)
    {

        if (is_null($expire)) {
            $expire = $this->redis->config['expire'];
        }

        $key = $this->getCacheKey($name);
        $expire = $this->getExpireTime($expire);

        $value = serialize($value);
        if ($expire) {
            $result = $this->redis->setex($key, $expire, $value);
        } else {
            $result = $this->redis->set($key, $value);
        }

        return $result;
    }

    /**
     * 获取实际的缓存标识
     * @access protected
     * @param  string $name 缓存名
     * @return string
     */
    protected function getCacheKey($name = '')
    {
        return $this->redis->config['prefix'] . $name;
    }

    /**
     * 自增缓存（针对数值缓存）
     * @access public
     * @param  string $name 缓存变量名
     * @param  int $step 步长
     * @return false|int
     */
    public function inc($name, $step = 1)
    {

        $key = $this->getCacheKey($name);

        return $this->redis->incrby($key, $step);
    }

    /**
     * 自减缓存（针对数值缓存）
     * @access public
     * @param  string $name 缓存变量名
     * @param  int $step 步长
     * @return false|int
     */
    public function dec($name, $step = 1)
    {
        $key = $this->getCacheKey($name);

        return $this->redis->decrby($key, $step);
    }

    /**
     * 删除缓存
     * @access public
     * @param  string $name 缓存变量名
     * @return boolean
     */
    public function rm($name)
    {
        return $this->redis->delete($this->getCacheKey($name));
    }

    /**
     * 读取缓存并删除
     * @access public
     * @param  string $name 缓存变量名
     * @return mixed
     */
    public function pull($name)
    {
        $result = $this->get($name, false);

        if ($result) {
            $this->rm($name);
            return $result;
        } else {
            return false;
        }
    }

    /**
     * 清除缓存
     * @access public
     * @return \Redis
     */
    public function clear()
    {
        $keys = $this->redis->keys($this->getCacheKey() . '*');
        return $this->redis->delete($keys);
    }

    /**
     * 获取有效期
     * @access protected
     * @param  integer|\DateTime $expire 有效期
     * @return integer
     */
    protected function getExpireTime($expire)
    {
        if ($expire instanceof \DateTime) {
            $expire = $expire->getTimestamp() - time();
        }

        return $expire;
    }

}