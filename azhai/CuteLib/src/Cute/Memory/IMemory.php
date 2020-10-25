<?php

/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\Memory;

/**
 * Memory接口
 */
interface IMemory
{

    public function isConnected();

    public function reconnect();

    public function close();

    public function del($key);

    public function get($key);

    public function set($key, $data, $timeout = 0);

    public function incre($key, $step = 1);

    public function decre($key, $step = 1);
}
