<?php
namespace App\WechatHandler\Event;

use App\WechatHandler\Base;
use App\WechatHandler\InterfaceHandler;

/**
 * 摇一摇事件推送处理.
 */
class EventShakearound extends Base implements InterfaceHandler
{
    /**
     * 主入口方法.
     */
    public function main()
    {
        $event = strtolower($this->recMessage->Event);
        switch ($event) {
            case 'shakearoundusershake'://摇一摇事件通知
                return '摇一摇事件通知';
                break;
            case 'shakearoundlotterybind'://红包绑定用户事件通知
                return '红包绑定用户事件通知';
                break;
        }
    }
}