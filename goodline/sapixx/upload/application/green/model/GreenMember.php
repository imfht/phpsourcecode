<?php
namespace app\green\model;
use think\Model;

class GreenMember extends Model {


    /**
     * 用户信息关联
     * @return void
     */
    public function member(){
        return $this->hasOne('app\common\model\SystemMember','id','member_id');
    }

    /**
     * 用户信息关联
     * @return void
     */
    public function operate(){
        return $this->hasOne('GreenOperate','id','operate_id');
    }

    /**
     * 获取运营商ID
     * @param ind $id
     * @return void
     */
    public static function getOperate(int $id){
        return self::where(['member_id' => $id])->find();
    }
}