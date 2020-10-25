<?php

/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 * Code From:
 *      http://www.sitepoint.com/saving-php-sessions-in-redis/
 */

namespace Cute\Web;

use \Cute\Memory\IMemory;
use \Cute\Cache\MemoryCache;
use \SessionHandlerInterface;

/**
 * Redis会话保存管理器
 * Notice:
 *  传统的文件会话保存管理器，在会话开始的时候会给会话数据文件加锁。
 */
class SessionHandler extends MemoryCache implements SessionHandlerInterface
{

    const PREFIX = 'PHPSESSID:';

    protected $timeout = 0; //失效时间

    public function __construct(IMemory& $memory, $timeout = 1800)
    {
        parent::__construct($memory);
        $this->timeout = intval($timeout);
        if (version_compare(PHP_VERSION, '6.0.0') < 0) {
            session_set_save_handler($this); //PHP7无法使用
        }
        @session_start();
    }

    public function setName($sid)
    {
        return parent::setName(self::PREFIX . $sid);
    }

    public function open($save_path, $session_name)
    {
        // No action necessary because connection is injected
        // in constructor and arguments are not applicable.
    }

    public function read($sid)
    {
        return $this->setName($sid)->readData();
    }

    public function write($sid, $data)
    {
        $this->setName($sid)->writeData($data, $this->timeout);
    }

    public function destroy($sid)
    {
        return $this->setName($sid)->removeData();
    }

    public function gc($max_lifetime)
    {
        // no action necessary because using EXPIRE
    }

}
