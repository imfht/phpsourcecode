<?php

namespace App\WechatHandler\Event;

use App\WechatHandler\Base;
use App\WechatHandler\InterfaceHandler;

/**
 * 上报地理位置事件消息处理.
 */
class EventLocation extends Base implements InterfaceHandler
{
    /**
     * 主入口方法.
     */
    public function main()
    {
        $event = strtolower($this->recMessage->Event);
        switch ($event) {
            case 'location':
                return '上报地理位置事件消息';
                break;
        }
    }
}
