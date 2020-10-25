<?php
namespace App\WechatHandler\Event;

use Swoole;
use App\WechatHandler\Base;
use App\WechatHandler\InterfaceHandler;

/**
 * 自定义菜单事件消息处理.
 */
class EventMenu extends Base implements InterfaceHandler
{
    /**
     * 主入口方法.
     */
    public function main()
    {
        $event = strtolower($this->recMessage->Event);
        switch ($event) {
            case 'click'://点击菜单拉取消息时的事件推送
                return '点击菜单拉取消息时的事件';

                break;
            case 'view'://点击菜单跳转链接时的事件推送
                return '点击菜单跳转链接时的事件';
                break;
        }
    }
}