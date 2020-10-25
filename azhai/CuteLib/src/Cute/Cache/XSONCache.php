<?php

/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\Cache;

/**
 * JSON/BSON文件缓存
 */
class XSONCache extends FileCache
{

    protected $ext = '.json';

    protected function readFile()
    {
        $data = file_get_contents($this->filename);
        return $data ? json_decode($data, true) : null;
    }

    protected function writeFile($data, $timeout = 0)
    {
        $data = json_encode($data);
        $bytes = file_put_contents($this->filename, $data, LOCK_EX);
        return $bytes && $bytes > 0;
    }

}
