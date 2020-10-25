<?php

/**
 * @author ryan<zer0131@vip.qq.com>
 * @desc memcached缓存类
 */

namespace onefox\caches;

use onefox\Cache;
use onefox\Config;

class CMemcached extends Cache {

    private $_memcached;

    public function __construct() {
        if (!extension_loaded('memcached')) {
            throw new \RuntimeException('The memcached extension must be loaded.');
        }
        $this->options = Config::get('cache.memcache');
        if (!$this->options) {
            $this->options = [
                'expire' => 0,
                'servers' => [
                    [
                        'host' => '127.0.0.1',
                        'port' => 11211,
                        'persistent' => false,
                        'weight' => 100
                    ],
                ]
            ];
        }
        $this->_connect();
    }

    private function _connect() {
        $this->_memcached = new \Memcached();
        foreach ($this->options['servers'] as $val) {
            $this->_memcached->addServer($val['host'], $val['port'], $val['weight']);
        }
    }

    public function get($name) {
        if (!$this->_memcached) {
            $this->_connect();
        }
        return $this->_memcached->get($name);
    }

    public function set($name, $value, $expire = null) {
        if (!$this->_memcached) {
            $this->_connect();
        }
        if (is_null($expire)) {
            $expire = $this->options['expire'];
        }
        if (intval($expire) === 0) {
            return $this->_memcached->set($name, $value, $expire);
        } else {
            return $this->_memcached->set($name, $value, time() + intval($expire));
        }
    }

    public function rm($name, $ttl = 0) {
        if (!$this->_memcached) {
            $this->_connect();
        }
        return $this->_memcached->delete($name, $ttl);
    }

    public function clear() {
        if (!$this->_memcached) {
            $this->_connect();
        }
        return $this->_memcached->flush();
    }

    public function __call($funcName, $arguments) {
        if (!$this->_memcached) {
            $this->_connect();
        }
        $res = call_user_func_array([
            $this->_memcached,
            $funcName
        ], $arguments);
        return $res;
    }

    public function __destruct() {
        if ($this->_memcached) {
            $this->_memcached->quit();
            $this->_memcached = null;
        }
    }
}
