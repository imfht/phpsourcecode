<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 
 * 微信模板ID设置
 */
namespace app\common\model;
use think\Model;

class SystemMemberWechatTpl extends Model{
    
    protected $pk      = 'id';

    //微信模板ID配置表
    public static function getConfig(int $miniapp_id){
        return self::where(['member_miniapp_id' => $miniapp_id])->find();
    }
    
}