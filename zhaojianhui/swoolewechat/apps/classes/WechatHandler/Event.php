<?php

namespace App\WechatHandler;

/**
 * 事件消息处理.
 */
class Event extends Base implements InterfaceHandler
{
    /**
     * 主入口方法.
     */
    public function main()
    {
        $sendMessage = '接收到事件消息';
        if ($this->recMessageType) {
            switch ($this->recMessageType) {
                case self::RECMSG_EVENT_SUBSCRIBE://关注事件推送
                    //异步触发用户关注取关注事件
                    \Swoole::$php->event->trigger('WxUserSubscribe', [
                        'openid'    => $this->recMessage->FromUserName,
                        'subscribe' => $this->recMessage->Event == 'subscribe' ? 1 : 0,
                    ]);
                    $sendMessage = (new \App\WechatHandler\Event\EventSubscribe($this->recMessage))->main();
                    break;
                case self::RECMSG_EVENT_SCAN://扫码事件
                    if ($this->recMessage->Event == 'subscribe') {
                        //异步触发用户关注取关注事件
                        \Swoole::$php->event->trigger('WxUserSubscribe', [
                            'openid'    => $this->recMessage->FromUserName,
                            'subscribe' => $this->recMessage->Event == 'subscribe' ? 1 : 0,
                        ]);
                    }
                    $sendMessage = (new \App\WechatHandler\Event\EventScan($this->recMessage))->main();
                    break;
                case self::RECMSG_EVENT_LOCATION://上报地理位置事件
                    $sendMessage = (new \App\WechatHandler\Event\EventLocation($this->recMessage))->main();
                    break;
                case self::RECMSG_EVENT_MENU://自定义菜单事件
                    $sendMessage = (new \App\WechatHandler\Event\EventMenu($this->recMessage))->main();
                    break;
                case self::RECMSG_EVENT_TEMPLATE://模板消息推送事件
                    $sendMessage = (new \App\WechatHandler\Event\EventTemplate($this->recMessage))->main();
                    break;
                case self::RECMSG_EVENT_KEFU://客服消息推送事件
                    $sendMessage = (new \App\WechatHandler\Event\EventKf($this->recMessage))->main();
                    break;
                case self::RECMSG_EVENT_MASS://群发消息推送事件
                    $sendMessage = (new \App\WechatHandler\Event\EventMass($this->recMessage))->main();
                    break;
                case self::RECMSG_EVENT_CARD://卡券消息推送事件
                    $sendMessage = (new \App\WechatHandler\Event\EventCard($this->recMessage))->main();
                    break;
                case self::RECMSG_EVENT_WIFI://WITI消息推送事件
                    $sendMessage = (new \App\WechatHandler\Event\EventWifi($this->recMessage))->main();
                    break;
                case self::RECMSG_EVENT_POI://微信门店事件推送
                    $sendMessage = (new \App\WechatHandler\Event\EventPoi($this->recMessage))->main();
                    break;
                case self::RECMSG_EVENT_SHAKEAROUND://摇一摇事件推送
                    $sendMessage = (new \App\WechatHandler\Event\EventShakearound($this->recMessage))->main();
                    break;
                case self::RECMSG_EVENT_USERSCAN://扫一扫事件推送处理.
                    $sendMessage = (new \App\WechatHandler\Event\EventUserscan($this->recMessage))->main();
                    break;
            }
        }

        return $sendMessage;
    }
}
