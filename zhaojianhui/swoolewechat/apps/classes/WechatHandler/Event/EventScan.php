<?php
namespace App\WechatHandler\Event;

use Swoole;
use App\WechatHandler\Base;
use App\WechatHandler\InterfaceHandler;

/**
 * 扫描带参数二维码事件消息处理.
 */
class EventScan extends Base implements InterfaceHandler
{
    /**
     * 创建二维码时的二维码
     *
     * @var
     */
    private $sceneId;

    /**
     * 主入口方法.
     */
    public function main()
    {
        //二维码携带的场景ID
        if (strtolower($this->recMessage->Event) == 'subscribe') {
            $this->sceneId = (int) str_replace('qrscene_', '', $this->recMessage->EventKey);
        } else {
            $this->sceneId = (int) $this->recMessage->EventKey;
        }

        return '扫描带参数二维码事件';
    }
}