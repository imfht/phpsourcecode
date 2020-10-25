<?php

namespace App\WechatHandler;

/**
 * 事件消息处理.
 */
interface InterfaceHandler
{
    /**
     * 主入口方法.
     *
     * @param $message
     */
    public function main();
}
