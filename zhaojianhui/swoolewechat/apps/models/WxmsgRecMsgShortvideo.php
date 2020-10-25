<?php
namespace App\Model;

/**
 * 短视频接收消息记录模型
 * @package App\Model
 */
class WxmsgRecMsgShortvideo extends \App\Component\BaseModel
{
    public $primary = 'id';
    /**
     * 表名
     * @var string
     */
    public $table = 'wxmsg_rec_msg_shortvideo';
}