<?php

/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\Memory;

/**
 * Memcache基于memcache扩展
 */
class Memcache extends \Memcache implements IMemory
{

    const MEMCACHE_WEIGHT_UNIT = 12;

    protected $host = '';
    protected $port = 0;
    protected $persistent = false;
    protected $weight = 1;
    protected $is_connected = false;

    public function __construct($host = '127.0.0.1', $port = 11211, $persistent = false, $weight = 1)
    {
        $this->host = $host;
        $this->port = intval($port);
        $this->persistent = $persistent;
        $this->weight = intval($weight);
    }

    public function isConnected()
    {
        return $this->is_connected;
    }

    public function reconnect()
    {
        if (!$this->isConnected()) {
            $weight = intval($this->weight) * self::MEMCACHE_WEIGHT_UNIT;
            $this->addServer($this->host, $this->port, $this->persistent, $weight);
            $this->is_connected = true;
        }
        return $this;
    }

    public function close()
    {
        $this->is_connected = false;
        return parent::close();
    }

    public function set($key, $data, $timeout = 0)
    {
        return parent::set($key, $data, MEMCACHE_COMPRESSED, $timeout);
    }

    public function del($key)
    {
        return $this->delete($key);
    }

    public function incre($key, $step = 1)
    {
        $step = abs($step);
        if (false === $this->get($key)) {
            $this->set($key, 0);
            return 0;
        } else {
            return $this->increment($key, $step);
        }
    }

    public function decre($key, $step = 1)
    {
        $step = abs($step);
        if (false === $this->get($key)) {
            $this->set($key, 0);
            return 0;
        } else {
            return $this->decrement($key, $step);
        }
    }

}
