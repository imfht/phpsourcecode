<?php
namespace App\Model;

/**
 * 上报地理位置事件记录模型
 * @package App\Model
 */
class WxmsgRecEventLocation extends \App\Component\BaseModel
{
    public $primary = 'id';
    /**
     * 表名
     * @var string
     */
    public $table = 'wxmsg_rec_event_location';
}