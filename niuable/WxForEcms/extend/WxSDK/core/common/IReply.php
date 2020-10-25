<?php
namespace WxSDK\core\common;

use WxSDK\core\model\ReplyMsg;
use WxSDK\core\model\WxMsg;


interface IReply
{
    /**
     * 获取回复内容
     * @param WxMsg $wxMsg
     * @return ReplyMsg
     */
    function getReplyMsg(WxMsg $wxMsg = null, IApp $app = null);
    /**
     * 回复后的操作，如写入记录，发送通知等
     * @param WxMsg $wxMsg
     * @param ReplyMsg $replyMsg
     */
    function afterReply(WxMsg $wxMsg = null, ReplyMsg $replyMsg = null);
}

