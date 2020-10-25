<?php

/**
 * @author ryan<zer0131@vip.qq.com>
 * @desc redis缓存类
 */

namespace onefox\caches;

use onefox\Cache;
use onefox\Config;

class CRedis extends Cache {

    private $_redis;

    public function __construct() {
        if (!extension_loaded('redis')) {
            throw new \RuntimeException('The redis extension must be loaded.');
        }
        $this->options = Config::get('cache.redis');
        if (!$this->options) {
            $this->options = [
                'expire' => 0,
                'server' => [
                    'host' => '127.0.0.1',
                    'port' => 6379
                ]
            ];
        }
        $this->_connect();
    }

    private function _connect() {
        $this->_redis = new \Redis();
        $this->_redis->connect($this->options['server']['host'], $this->options['server']['port']);
    }

    public function get($name) {
        if (!$this->_redis) {
            $this->_connect();
        }
        return $this->_redis->get($name);
    }

    public function set($name, $value, $expire = null) {
        if ($this->_redis) {
            $this->_connect();
        }
        if (is_null($expire)) {
            $expire = $this->options['expire'];
        }
        if (intval($expire) === 0) {
            return $this->_redis->set($name, $value);
        } else {
            return $this->_redis->setEx($name, intval($expire), $value);
        }
    }

    public function rm($name, $ttl = 0) {
        if (!$this->_redis) {
            $this->_connect();
        }
        return $this->_redis->delete($name);
    }

    public function clear() {
        if (!$this->_redis) {
            $this->_connect();
        }
        return $this->_redis->flushAll();
    }

    public function __call($funcName, $arguments) {
        if (!$this->_redis) {
            $this->_connect();
        }
        $res = call_user_func_array([
            $this->_redis,
            $funcName
        ], $arguments);
        return $res;
    }

    public function __destruct() {
        if ($this->_redis) {
            $this->_redis_>close();
            $this->_redis = null;
        }
    }
}
