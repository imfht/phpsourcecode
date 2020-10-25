<?php

/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\Memory;

\app()->importStrip('\\Predis', VENDOR_ROOT . '/predis/src');

/**
 * Redis基于Predis，用于PHP5.3以上，目前不支持PHP7
 */
class RedisPHP extends \Predis\Client implements IMemory
{

    use TRedis;

    public function __construct($host = '127.0.0.1', $port = 6379)
    {
        parent::__construct([
            'scheme' => 'tcp', 'host' => $host, 'port' => $port,
        ]);
    }

    public function reconnect()
    {
        if (!$this->isConnected()) {
            $this->connect();
        }
        return $this;
    }

    public function close()
    {
        return $this->quit();
    }

    public function set($key, $data, $timeout = 0)
    {
        return parent::set($key, $data, 'ex', $timeout);
    }

    public function del($key)
    {
        return parent::del($key);
    }

    public function get($key)
    {
        return parent::get($key);
    }

}
