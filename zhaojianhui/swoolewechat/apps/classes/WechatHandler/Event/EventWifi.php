<?php
namespace App\WechatHandler\Event;

use App\WechatHandler\Base;
use App\WechatHandler\InterfaceHandler;

/**
 * WIFI消息推送处理.
 */
class EventWifi extends Base implements InterfaceHandler
{
    /**
     * 主入口方法.
     */
    public function main()
    {
        //触发微信关注取关注消息事件
        \Swoole::$php->event->trigger('WxWifi', ['message' => $this->recMessage]);
        $event = strtolower($this->recMessage->Event);
        switch ($event) {
            case 'wificonnected'://门店wifi连接事件推送
                return '门店wifi连接';
                break;
        }
    }
}