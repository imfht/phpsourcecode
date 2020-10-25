<?php

/**
 * @author ryan<zer0131@vip.qq.com>
 * @desc memcache缓存类
 */

namespace onefox\caches;

use onefox\Cache;
use onefox\Config;

class CMemcache extends Cache {

    private $_memcache;

    public function __construct() {
        if (!extension_loaded('memcache')) {
            throw new \RuntimeException('The memcache extension must be loaded.');
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
        $this->_memcache = new \Memcache();
        foreach ($this->options['servers'] as $val) {
            $this->_memcache->addServer($val['host'], $val['port'], $val['persistent'], $val['weight']);
        }
    }

    public function get($name) {
        if (!$this->_memcache) {
            $this->_connect();
        }
        return $this->_memcache->get($name);
    }

    public function set($name, $value, $expire = null) {
        if (!$this->_memcache) {
            $this->_connect();
        }
        if (is_null($expire)) {
            $expire = $this->options['expire'];
        }
        return $this->_memcache->set($name, $value, 0, $expire);
    }

    public function rm($name, $ttl = 0) {
        if (!$this->_memcache) {
            $this->_connect();
        }
        return $this->_memcache->delete($name, $ttl);
    }

    public function clear() {
        if (!$this->_memcache) {
            $this->_connect();
        }
        return $this->_memcache->flush();
    }

    public function __call($funcName, $arguments) {
        if (!$this->_memcache) {
            $this->_connect();
        }
        $res = call_user_func_array([
            $this->_memcache,
            $funcName
        ], $arguments);
        return $res;
    }

    public function __destruct() {
        if ($this->_memcache) {
            $this->_memcache->close();
            $this->_memcache = null;
        }
    }
}
