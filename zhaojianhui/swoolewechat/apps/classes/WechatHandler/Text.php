<?php
namespace App\WechatHandler;

/**
 * 链接消息处理
 * @package App\WechatHandler
 */
class Text extends Base implements InterfaceHandler
{
    /**
     * 主入口方法
     */
    public function main()
    {
        $materialData = [];
        //发送消息的用户openId
        $openId = $this->recMessage->FromUserName;
        //文本消息内容
        $content = $this->recMessage->Content;
        return $content;
    }
}