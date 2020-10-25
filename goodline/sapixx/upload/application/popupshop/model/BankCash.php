<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 用户银行表 Table<ai_popupshop_bank_cash>
 */
namespace app\popupshop\model;
use think\Model;


class BankCash extends Model{
    
    protected $pk     = 'id';
    protected $table  = 'ai_popupshop_bank_cash';

    /**
     * 绑定的用户
     * @return void
     */
    public function User(){
        return $this->hasOne('app\common\model\SystemUser','id','user_id');
    }

    /**
     * 提醒信息
     */
    public function info(){
        return $this->hasOne('BankInfo','user_id','user_id');
    }
}