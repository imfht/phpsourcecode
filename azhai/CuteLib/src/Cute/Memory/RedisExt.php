<?php

/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\Memory;

/**
 * Redis基于phpredis扩展，主分支用于PHP5，另有分支支持PHP7
 */
class RedisExt extends \Redis implements IMemory
{

    use TRedis;

    protected $host = '';
    protected $port = 0;
    protected $options = [
        'persistent' => false,
        'socket' => null,
        'serializer' => null,
    ];

    public function __construct($host = '127.0.0.1', $port = 6379, array $options = [])
    {
        $this->host = $host;
        $this->port = intval($port);
        $this->setup($options);
    }

    public function setup(array $options)
    {
        $this->options = array_merge($this->options, $options);
        if (isset($this->options['socket']) && is_string($this->options['socket'])) {
            if (starts_with($this->options['socket'], 'unix://')) {
                $this->options['socket'] = substr($this->options['socket'], 7);
            }
        }
        if ($serializer = $this->options['serializer']) {
            $serializer = constant('\\Redis::SERIALIZER_' . $serializer);
            $this->setOption(\Redis::OPT_SERIALIZER, $serializer);
        }
    }

    public function isConnected()
    {
        try {
            $this->ping();
            return true;
        } catch (\RedisException $e) {
            return false;
        }
    }

    public function reconnect()
    {
        if (!$this->isConnected()) {
            $connect = $this->options['persistent'] ? 'pconnect' : 'connect';
            if (isset($this->options['socket'])) {
                $args = [$this->options['socket']];
            } else {
                $args = [$this->host, $this->port];
            }
            exec_method_array($this, $connect, $args);
        }
        return $this;
    }

    public function del($key)
    {
        return parent::del($key);
    }

    public function get($key)
    {
        return parent::get($key);
    }

    public function set($key, $data, $timeout = 0)
    {
        return parent::set($key, $data, $timeout);
    }

}
