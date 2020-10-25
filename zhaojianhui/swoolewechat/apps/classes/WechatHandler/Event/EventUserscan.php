<?php
namespace App\WechatHandler\Event;

use App\WechatHandler\Base;
use App\WechatHandler\InterfaceHandler;

/**
 * 扫一扫事件推送处理.
 */
class EventUserscan extends Base implements InterfaceHandler
{
    /**
     * 主入口方法.
     */
    public function main()
    {
        $event = strtolower($this->recMessage->Event);
        switch ($event) {
            case 'subscribe'://关注公众号事件推送
                break;
            case 'user_scan_product'://打开商品主页事件推送
                break;
            case 'user_scan_product_enter_session'://进入公众号事件推送
                break;
            case 'user_scan_product_async'://地理位置信息异步推送
                break;
            case 'user_scan_product_verify_action'://商品审核结果推送
                break;
        }
    }
}