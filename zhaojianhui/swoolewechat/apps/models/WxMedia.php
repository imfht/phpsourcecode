<?php
namespace App\Model;
/**
 * 微信素材模型
 * @package App\Model
 */
class WxMedia extends \App\Component\BaseModel
{
    public $primary = 'mediaId';
    /**
     * 表名
     * @var string
     */
    public $table = 'wx_media';

}