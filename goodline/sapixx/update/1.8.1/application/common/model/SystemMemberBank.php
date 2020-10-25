<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 
 * 用户余额
 */
namespace app\common\model;
use think\Model;

class SystemMemberBank extends Model{

    protected $pk = 'id';


    public function member(){
        return $this->hasOne('app\common\model\SystemMember','id','member_id');
    }

    /**
     * 查询账号金额够不够
     *
     * @param int $member_id
     * @param float $sellMoeny
     * @return void
     */
    public static function moneyJudge($member_id,$sellMoeny){
       $bank = self::where(['member_id' => $member_id])->find();
       if($bank){
           if($bank->money >= $sellMoeny){
               return false;
           }else{
               return true;
           }
       }else{
           return true;
       }
    }

    /**
     * 更改应用信息

     * @param int $member_id
     * @param float $money
     * @return void
     */
    public static function moneyUpdate($member_id,$money){
        $bank = self::where(['member_id' => $member_id])->find();
        if ($bank) {
            $bank->money =  ['inc',$money];
            $bank->update_time = time();
            return $bank->save();
        } else {
            return self::create(['member_id' => $member_id,'money' => $money,'update_time' => time()]);
        }
    }
}