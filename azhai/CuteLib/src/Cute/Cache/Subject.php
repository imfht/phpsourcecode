<?php

/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\Cache;

use \SplObserver;
use \SplSubject;

/**
 * 缓存对象
 */
class Subject implements SplSubject
{

    const OP_READ = 0;
    const OP_WRITE = 1;
    const OP_REMOVE = 2;

    protected $observers = [];
    protected $data = null;
    protected $timeout = 0; //失效时间
    protected $operation = 0; //操作

    public function __construct(& $data, $timeout = 0)
    {
        $this->data = & $data;
        $this->timeout = intval($timeout);
    }

    public function attach(SplObserver $observer)
    {
        $this->observers[] = $observer;
    }

    public function detach(SplObserver $observer)
    {
        $key = array_search($observer, $this->observers, true);
        if ($key) {
            unset($this->observers[$key]);
        }
    }

    public function notify()
    {
        foreach ($this->observers as &$observer) {
            $observer->update($this, $this->operation);
        }
    }

    public function get()
    {
        return $this->data;
    }

    public function set($data)
    {
        return $this->data = $data;
    }

    public function ttl()
    {
        return $this->timeout;
    }

    public function read()
    {
        $this->operation = self::OP_READ;
        $this->notify();
    }

    public function forceRead()
    {
        $origin = $this->data;
        $this->data = null;
        $this->read();
        if (is_null($this->data)) {
            $this->data = $origin;
        }
    }

    public function write()
    {
        $this->operation = self::OP_WRITE;
        $this->notify();
    }

    public function delete()
    {
        $this->operation = self::OP_REMOVE;
        $this->notify();
    }

}
