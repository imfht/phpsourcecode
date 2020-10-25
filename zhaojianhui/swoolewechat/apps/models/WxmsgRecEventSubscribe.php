<?php
namespace App\Model;

/**
 * 关注与取关注事件消息记录模型
 * @package App\Model
 */
class WxmsgRecEventSubscribe extends \App\Component\BaseModel
{
    public $primary = 'id';
    /**
     * 表名
     * @var string
     */
    public $table = 'wxmsg_rec_event_subscribe';
}