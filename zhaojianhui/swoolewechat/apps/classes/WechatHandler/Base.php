<?php

namespace App\WechatHandler;

/**
 * 消息处理基础类.
 */
class Base extends WxMsg
{
    public function __construct($message)
    {
        $this->setRecMessageType($message);
    }
}
