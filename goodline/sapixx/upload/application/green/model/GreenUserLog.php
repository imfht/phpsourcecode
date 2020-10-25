<?php
namespace app\green\model;

use think\Model;

class GreenUserLog extends Model {

    protected $autoWriteTimestamp = true;
    
    public function operate(){
        return $this->hasOne('GreenOperate','id','operate_id');
    }

    //用户
    public function user(){
        return $this->hasOne('app\common\model\SystemUser','id','uid');
    }


    //格式化日期
    public function getCreate_timeAttr($value)
    {
        return date('Y-m-d H:i:s',$value);
    }
}