<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 用户银行表 Table<ai_fastshop_bank_cash>
 */
namespace app\fastshop\model;
use think\Model;
use think\facade\Validate;

class BankCash extends Model{
    
    protected $pk     = 'id';
    protected $table  = 'ai_fastshop_bank_cash';
    protected $re_table  = 'fastshop_bank_recharge';
    protected $autoWriteTimestamp = true;
    protected $createTime = false;

    /**
     * 充值表
     */
    public function re_table(){
        return self::table('ai_'.$this->re_table);
    }

    /**
     * 提醒信息
     */
    public function info(){
        return $this->hasOne('BankInfo','user_id','user_id');
    } 

     /**
     * 申请提现列表
     */
    public function lists($miniapp_id,$condition){
        return self::view('fastshop_bank_cash','*')
        ->view('system_user','nickname,phone_uid','fastshop_bank_cash.user_id = system_user.id')
        ->where(['fastshop_bank_cash.member_miniapp_id' => $miniapp_id])
        ->where($condition)
        ->order('id desc');
    }

     /**
     * 查看申请
     */
    public function finds($where){
        return self::view('fastshop_bank_cash','*')
        ->view('system_user','nickname,face','fastshop_bank_cash.user_id = system_user.id')
        ->where($where)->find();
    } 
}