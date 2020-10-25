<?php
/**
 * 微信模板ID设置
 */
namespace app\common\model;
use think\Model;

class MemberWechatTpl extends Model{
    
    protected $pk      = 'id';

    //微信模板ID配置表
    public static function getConfig(int $miniapp_id){
        return self::where(['member_miniapp_id' => $miniapp_id])->find();
    }
    
}