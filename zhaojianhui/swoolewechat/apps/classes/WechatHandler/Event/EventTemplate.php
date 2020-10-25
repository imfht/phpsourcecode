<?php

namespace App\WechatHandler\Event;

use App\WechatHandler\Base;
use App\WechatHandler\InterfaceHandler;
use Swoole;

/**
 * 模板消息处理.
 */
class EventTemplate extends Base implements InterfaceHandler
{
    /**
     * 主入口方法.
     */
    public function main()
    {
        $event = strtolower($this->recMessage->Event);
        switch ($event) {
            case 'templatesendjobfinish'://模板消息事件推送
                return '模板消息事件推送';
                break;
        }
    }
}
