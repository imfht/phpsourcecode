<?php

/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\Cache;

/**
 * Redis缓存
 */
class RedisDictCache extends MemoryCache
{

    protected function doRead()
    {
        return $this->memory->hGetAll($this->name);
    }

    protected function doWrite($data, $timeout = 0)
    {
        $result = null;
        foreach ($data as $key => $value) {
            $result = $this->memory->hSet($this->name, $key, $value);
            if ($result === false) {
                break;
            }
        }
        if ($timeout > 0) {
            $this->memory->expire($this->name, $timeout);
        }
        return $result;
    }

}
