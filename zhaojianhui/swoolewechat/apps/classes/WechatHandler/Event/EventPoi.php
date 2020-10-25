<?php
namespace App\WechatHandler\Event;

use App\WechatHandler\Base;
use App\WechatHandler\InterfaceHandler;

/**
 * 门店事件推送处理.
 */
class EventPoi extends Base implements InterfaceHandler
{
    /**
     * 主入口方法.
     */
    public function main()
    {
        $event = strtolower($this->recMessage->Event);
        switch ($event){
            case 'poi_check_notify'://门店审核事件推送
                return '门店审核事件';
                break;
        }
        return false;
    }
}