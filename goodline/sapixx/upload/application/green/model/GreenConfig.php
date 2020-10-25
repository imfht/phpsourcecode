<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 配置
 */
namespace app\green\model;
use think\Model;

class GreenConfig extends Model{

    protected $pk = 'id';
  
    //配置表
    public static function getConfig(int $miniapp_id){
        return self::where(['member_miniapp_id' => $miniapp_id])->cache(true)->find();
    }
    
    //编辑
    public static function configs(array $param,int $miniapp_id){
        $rel = self::where(['member_miniapp_id' => $miniapp_id])->find();
        if(empty($rel)){
            $param['member_miniapp_id'] = $miniapp_id;
            return self::insert($param);
        }else{
            return self::where(['member_miniapp_id' => $miniapp_id])->update($param);
        }
    }
}