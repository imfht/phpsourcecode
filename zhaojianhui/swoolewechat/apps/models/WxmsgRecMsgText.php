<?php
namespace App\Model;

/**
 * 文本接收消息记录模型
 * @package App\Model
 */
class WxmsgRecMsgText extends \App\Component\BaseModel
{
    public $primary = 'id';
    /**
     * 表名
     * @var string
     */
    public $table = 'wxmsg_rec_msg_text';
}