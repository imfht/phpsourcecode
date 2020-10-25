<?php
namespace App\WechatHandler\Event;

use App\WechatHandler\Base;
use App\WechatHandler\InterfaceHandler;

/**
 * 群发消息推送处理.
 */
class EventMass extends Base implements InterfaceHandler
{
    /**
     * 主入口方法.
     */
    public function main()
    {
        $event = strtolower($this->recMessage->Event);
        switch ($event){
            case 'masssendjobfinish'://群发结果推送
                return '群发结果推送';
                break;
        }
        return false;
    }
}