<?php

/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\Cache;

use \Cute\Memory\IMemory;

/**
 * 内存缓存
 */
class MemoryCache extends BaseCache
{

    use \Cute\Base\Deferring;

    protected $memory = null;
    protected $name = '';

    public function __construct(IMemory& $memory, $name = '')
    {
        $this->memory = $memory;
        $this->setName($name);
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function prepare()
    {
        $this->memory->reconnect();
        return true;
    }

    public function close()
    {
        if ($this->memory && $this->memory->isConnected()) {
            $this->memory->close();
        }
    }

    protected function doRead()
    {
        return $this->memory->get($this->name);
    }

    protected function doWrite($data, $timeout = 0)
    {
        return $this->memory->set($this->name, $data, $timeout);
    }

    public function readData()
    {
        $this->prepare();
        $data = $this->doRead();
        if ($data !== false) {
            return $data;
        }
    }

    public function writeData($data, $timeout = 0)
    {
        $this->prepare();
        $result = $this->doWrite($data, $timeout);
        return $result;
    }

    public function removeData()
    {
        $this->prepare();
        return $this->memory->del($this->name);
    }

}
