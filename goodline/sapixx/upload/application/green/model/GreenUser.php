<?php
namespace app\green\model;

use think\Model;

class GreenUser extends Model {

    //用户
    public function user(){
        return $this->hasOne('app\common\model\SystemUser','id','uid');
    }

    /**
     * 提现
     *
     * @param integer $miniapp_id
     * @param integer $uid
     * @param float $money
     * @return void
     */
    public static function cash(int $miniapp_id,int $uid,float $money){
        $info = self::where(['member_miniapp_id' => $miniapp_id, 'uid' => $uid])->find();
        if (empty($info)) {
            return;
        }
        $money = $money * 1000;
        if ($info->points < $money) {
            return;
        }
        $info->points      = ['dec', $money];
        $info->update_time = time();
        return $info->save();
    }
}