<?php
namespace App\Model;

/**
 * 微信用户模型
 * @package App\Model
 */
class WxUserSubscribeLog extends \App\Component\BaseModel
{
    public $primary = 'id';
    /**
     * 表名
     * @var string
     */
    public $table = 'wx_user_subscribe_log';
}