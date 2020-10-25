<?php

/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\Cache;

/**
 * 文本文件缓存
 */
class TextCache extends FileCache
{

    protected $ext = '.txt';

    protected function readFile()
    {
        $data = file_get_contents($this->filename);
        return $data ? : '';
    }

    protected function writeFile($data, $timeout = 0)
    {
        $bytes = file_put_contents($this->filename, $data, LOCK_EX);
        return $bytes && $bytes > 0;
    }

}
