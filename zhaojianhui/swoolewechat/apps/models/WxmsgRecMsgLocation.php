<?php
namespace App\Model;

/**
 * 上报地理位置记录模型
 * @package App\Model
 */
class WxmsgRecMsgLocation extends \App\Component\BaseModel
{
    public $primary = 'id';
    /**
     * 表名
     * @var string
     */
    public $table = 'wxmsg_rec_msg_location';
}