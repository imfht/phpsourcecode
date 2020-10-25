<?php
namespace App\Model;
/**
 * 微信模板消息模型
 * @package App\Model
 */
class WxTemplate extends \App\Component\BaseModel
{
    public $primary = 'templateId';
    /**
     * 表名.
     *
     * @var string
     */
    public $table = 'wx_template';
}