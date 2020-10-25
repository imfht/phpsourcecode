<?php
namespace app\green\model;

use think\Model;

class GreenDeviceState extends Model {

    public function operate(){
        return $this->hasOne('GreenOperate','id','operate_id');
    }

    //用户
    public function user(){
        return $this->hasOne('app\common\model\SystemUser','id','uid');
    }
}