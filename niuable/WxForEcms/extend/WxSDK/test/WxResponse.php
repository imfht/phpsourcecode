<?php

namespace test;

use WxSDK\core\common\IReply;
use WxSDK\core\model\ReplyMsg;
use WxSDK\core\model\WxMsg;
use WxSDK\core\common\IApp;

/**
 * 配合自动回复系统
 * 自定义回复内容的生成逻辑
 * @author 王维
 * www.51xlxy.com
 *
 */
class WxResponse implements IReply
{
    /**
     * {@inheritDoc}
     * @see \WxSDK\core\common\IReply::getReplyMsg()
     */
    public function getReplyMsg(WxMsg $wxMsg, IApp $accessToken)
    {
        $replyMsg = new ReplyMsg();
        $replyMsg->encrypt = $wxMsg->encrypt;
        $replyMsg->timeStamp = $wxMsg->timeStamp;
        $replyMsg->nonce = $wxMsg->nonce;

        if ($wxMsg->msgType == "event" && $wxMsg->event->event == "LOCATION") {
            //进入公众号时推送地理位置信息
            $replyMsg->msg = "";
        } else {
            $word = "您的信息已收到！";
            $word .= json_encode($wxMsg, JSON_UNESCAPED_UNICODE);
            $replyMsg->msg = ReplyMsg::getTextMsg($wxMsg->fromUserName ? $wxMsg->fromUserName : "",
                $wxMsg->toUserName ? $wxMsg->toUserName : "", $word, time());
        }
        return $replyMsg;
    }

    public function afterReply(WxMsg $wxMsg, ReplyMsg $replyMsg)
    {
    }


}
