<?php
namespace App\WechatHandler\Event;

use Swoole;
use App\WechatHandler\Base;
use App\WechatHandler\InterfaceHandler;

/**
 * 关注、取关注事件消息处理.
 */
class EventSubscribe extends Base implements InterfaceHandler
{
    /**
     * 主入口方法.
     */
    public function main()
    {
        //触发微信关注取关注消息事件
        \Swoole::$php->event->trigger('WxUserSubscribe', ['message' => $this->recMessage]);
        $event = strtolower($this->recMessage->Event);
        switch ($event) {
            case 'subscribe':
                break;
            case 'unsubscribe':
                $configData = ['type' => 'text', 'content' => '取消关注成功'];
                break;
        }

        return $this->formatMessage($configData);
    }
}