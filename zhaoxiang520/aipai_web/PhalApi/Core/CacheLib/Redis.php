<?php

namespace PhalApi\Core\CacheLib;
use PhalApi\Core\Exception\PAException;

/**
 * Redis.php
 * @since   2016-08-29
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 * @link http://www.thinkphp.cn/
 */
class Redis {

    protected $handler = null;
    protected $options = [
        'DATA_CACHE_HOST'        => '127.0.0.1', //缓存服务地址
        'DATA_CACHE_PORT'        => 6379, // 缓存服务端口
        'DATA_CACHE_PASSWORD'    => '', // 缓存密码
        'DATA_CACHE_TIMEOUT'     => 0, // 缓存链接超时时间
        'DATA_CACHE_EXPIRE'      => 0, // 缓存过期时间
        'DATA_CACHE_PERSISTENT'  => false, // 是否强制链接
        'DATA_CACHE_PREFIX'      => '', // 键前缀
    ];

    /**
     * 环境监测以及连接缓存服务
     * @param array $options
     * @throws PAException
     */
    public function __construct($options = []) {
        if (!extension_loaded('redis')) {
            throw new PAException('not support: redis');
        }
        if (!empty($options)) {
            $this->options = array_merge(array_change_key_case($this->options, CASE_UPPER), $options);
        }
        try {
            $func = $this->options['DATA_CACHE_PERSISTENT'] ? 'pconnect' : 'connect';
            $this->handler = new \Redis;
            $this->handler->$func($this->options['DATA_CACHE_HOST'], $this->options['DATA_CACHE_PORT'], $this->options['DATA_CACHE_TIMEOUT']);

            if ('' != $this->options['DATA_CACHE_PASSWORD']) {
                $this->handler->auth($this->options['DATA_CACHE_PASSWORD']);
            }
        }catch ( \Exception $e){
            throw new PAException($e->getMessage());
        }
    }

    /**
     * 判断缓存
     * @access public
     * @param string $name 缓存变量名
     * @return bool
     */
    public function has($name) {
        return $this->handler->get($this->options['DATA_CACHE_PREFIX'] . $name) ? true : false;
    }

    /**
     * 读取缓存
     * @access public
     * @param string $name 缓存变量名
     * @param mixed  $default 默认值
     * @return mixed
     */
    public function get($name, $default = false) {
        $value = $this->handler->get($this->options['DATA_CACHE_PREFIX'] . $name);
        if (is_null($value)) {
            return $default;
        }
        $jsonData = json_decode($value, true);
        // 检测是否为JSON数据 true 返回JSON解析数组, false返回源数据 byron sampson<xiaobo.sun@qq.com>
        return (null === $jsonData) ? $value : $jsonData;
    }

    /**
     * 写入缓存
     * @access public
     * @param string    $name 缓存变量名
     * @param mixed     $value  存储数据
     * @param integer   $expire  有效时间（秒）
     * @return boolean
     */
    public function set($name, $value, $expire = null) {
        if (is_null($expire)) {
            $expire = $this->options['DATA_CACHE_EXPIRE'];
        }
        $name = $this->options['DATA_CACHE_PREFIX'] . $name;
        //对数组/对象数据进行缓存处理，保证数据完整性  byron sampson<xiaobo.sun@qq.com>
        $value = (is_object($value) || is_array($value)) ? json_encode($value) : $value;
        if (is_int($expire) && $expire) {
            $result = $this->handler->setex($name, $expire, $value);
        } else {
            $result = $this->handler->set($name, $value);
        }
        return $result;
    }

    /**
     * 自增缓存（针对数值缓存）
     * @access public
     * @param string    $name 缓存变量名
     * @param int       $step 步长
     * @return false|int
     */
    public function inc($name, $step = 1) {
        $name = $this->options['DATA_CACHE_PREFIX'] . $name;
        return $this->handler->incrBy($name, $step);
    }

    /**
     * 自减缓存（针对数值缓存）
     * @access public
     * @param string    $name 缓存变量名
     * @param int       $step 步长
     * @return false|int
     */
    public function dec($name, $step = 1) {
        $name = $this->options['DATA_CACHE_PREFIX'] . $name;
        return $this->handler->decrBy($name, $step);
    }

    /**
     * 删除缓存
     * @access public
     * @param string $name 缓存变量名
     * @return boolean
     */
    public function rm($name) {
        return $this->handler->del($this->options['DATA_CACHE_PREFIX'] . $name);
    }

    /**
     * 清除缓存
     * @access public
     * @return boolean
     */
    public function clear() {
        return $this->handler->flushDB();
    }

    /**
     * 返回句柄对象，可执行其它高级方法
     *
     * @access public
     * @return object
     */
    public function handler() {
        return $this->handler;
    }
}