<?php
namespace app\green\model;
use think\Model;

class GreenRetrieve extends Model {

    //用户
    public function user(){
        return $this->hasOne('app\common\model\SystemUser','id','uid');
    }
}