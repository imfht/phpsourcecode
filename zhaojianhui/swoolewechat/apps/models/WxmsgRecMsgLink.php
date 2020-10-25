<?php
namespace App\Model;

/**
 * 链接接收消息记录模型
 * @package App\Model
 */
class WxmsgRecMsgLink extends \App\Component\BaseModel
{
    public $primary = 'id';
    /**
     * 表名
     * @var string
     */
    public $table = 'wxmsg_rec_msg_link';
}