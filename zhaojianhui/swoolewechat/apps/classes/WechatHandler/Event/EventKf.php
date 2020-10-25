<?php
namespace App\WechatHandler\Event;

use App\WechatHandler\Base;
use App\WechatHandler\InterfaceHandler;

/**
 * 客服消息推送处理.
 */
class EventKf extends Base implements InterfaceHandler
{
    /**
     * 主入口方法.
     */
    public function main()
    {
        $event = strtolower($this->recMessage->Event);
        switch ($event){
            case 'kf_create_session'://接入会话
                return '接入会话';
                break;
            case 'kf_close_session'://关闭会话
                return '关闭会话';
                break;
            case 'kf_switch_session'://转接会话
                return '转接会话';
                break;
        }
        return false;
    }
}