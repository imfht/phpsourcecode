<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nb\cache;


class Memcached extends Driver {

    protected $options = [
        'host' => '127.0.0.1',
        'port' => 11211,
        'expire' => 0,
        'timeout' => 0, // 超时时间（单位：毫秒）
        'prefix' => '',
        'username' => '', //账号
        'password' => '', //密码
        'option' => [],
    ];

    /**
     * @var \Memcached
     */
    protected $handler;

    /**
     * 构造函数
     * @param array $options 缓存参数
     * @access public
     */
    public function __construct($options = []) {
        if (!extension_loaded('memcached')) {
            throw new \BadFunctionCallException('not support: memcached');
        }
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }
        $this->handler = new \Memcached;
        if (!empty($this->options['option'])) {
            $this->handler->setOptions($this->options['option']);
        }
        // 设置连接超时时间（单位：毫秒）
        if ($this->options['timeout'] > 0) {
            $this->handler->setOption(\Memcached::OPT_CONNECT_TIMEOUT, $this->options['timeout']);
        }
        // 支持集群
        $hosts = explode(',', $this->options['host']);
        $ports = explode(',', $this->options['port']);
        if (empty($ports[0])) {
            $ports[0] = 11211;
        }
        // 建立连接
        $servers = [];
        foreach ((array)$hosts as $i => $host) {
            $servers[] = [$host, (isset($ports[$i]) ? $ports[$i] : $ports[0]), 1];
        }
        $this->handler->addServers($servers);
        if ('' != $this->options['username']) {
            $this->handler->setOption(\Memcached::OPT_BINARY_PROTOCOL, true);
            $this->handler->setSaslAuthData($this->options['username'], $this->options['password']);
        }
    }

    /**
     * 判断缓存
     * @access public
     * @param string $name 缓存变量名
     * @return bool
     */
    public function has($name) {
        return $this->handler->get($name) ? true : false;
    }

    /**
     * 读取缓存
     * @access public
     * @param string $name 缓存变量名
     * @param mixed $default 默认值
     * @return mixed
     */
    public function get($name, $default = false) {
        $result = $this->handler->get($name);
        return false !== $result ? $result : $default;
    }

    /**
     * 写入缓存
     * @access public
     * @param string $name 缓存变量名
     * @param mixed $value 存储数据
     * @param integer $expire 有效时间（秒）
     * @return bool
     */
    public function set($name, $value, $expire = null) {
        if (is_null($expire)) {
            $expire = $this->options['expire'];
        }
        //if ($this->tag && !$this->has($name)) {
        //    $first = true;
        //}
        //$key = $this->getCacheKey($name);
        $expire = 0 == $expire ? 0 : $_SERVER['REQUEST_TIME'] + $expire;
        if ($this->handler->set($name, $value, $expire)) {
            //isset($first) && $this->setTagItem($name);
            return true;
        }
        return false;
    }

    /**
     * 自增缓存（针对数值缓存）
     * @access public
     * @param string $name 缓存变量名
     * @param int $step 步长
     * @return false|int
     */
    public function inc($name, $step = 1) {
        if ($this->handler->get($name)) {
            return $this->handler->increment($name, $step);
        }
        return $this->handler->set($name, $step);
    }

    /**
     * 自减缓存（针对数值缓存）
     * @access public
     * @param string $name 缓存变量名
     * @param int $step 步长
     * @return false|int
     */
    public function dec($name, $step = 1) {
        $value = $this->handler->get($name) - $step;
        $res = $this->handler->set($name, $value);
        if (!$res) {
            return false;
        }
        else {
            return $value;
        }
    }

    /**
     * 删除缓存
     * @param string $name 缓存变量名
     * @param bool|false $ttl
     * @return bool
     */
    public function delete($name, $ttl = false) {
        return false === $ttl ?
            $this->handler->delete($name) :
            $this->handler->delete($name, $ttl);
    }

    /**
     * 清除缓存
     * @access public
     * @param string $pattern 匹配符
     * @return bool
     *
     * Memcache 暂不支持模糊删除，批量删除，可以传入一个数组key
     */
    public function rm($pattern = null) {
        if ($pattern) {
            // 指定标签清除
            //$keys = $this->getTagItem($tag);
            $flag = is_array($pattern) && $this->handler->deleteMulti($pattern);
            //$this->rm('tag_' . md5($tag));
            return $flag;
        }
        return $this->handler->flush();
    }

    public function __call($name, $arguments) {
        // TODO: Implement __call() method.
        return call_user_func_array([$this->handler,$name],$arguments);
    }

    public function __set($name, $value) {
        // TODO: Implement __set() method.
        return $this->handler->$name = $value;
    }

    public function __get($name) {
        // TODO: Implement __get() method.
        return $this->handler->$name;
    }
}
