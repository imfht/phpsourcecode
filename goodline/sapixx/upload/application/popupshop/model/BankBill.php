<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 收益记录表 Table<ai_popupshop_bank_logs>
 */
namespace app\popupshop\model;
use think\Model;
use think\facade\Validate;

class BankBill extends Model{
    
    protected $pk     = 'id';
    protected $table  = 'ai_popupshop_bank_bill';

    /**
     * 当前所属用户
     */
    public function user(){
        return $this->hasOne('app\common\model\SystemUser','id','user_id');
    } 

    /**
     * 来源用户
     */
    public function formuser(){
        return $this->hasOne('app\common\model\SystemUser','id','from_uid');
    } 

   /**
     * [log 增加财务日志]
     * @param  [int]     $uid [用户ID]
     * @param  [float]   $money   [变动金额]
     * @param  [str]     $message [日志内容]
     * @param  [boolean] $is_money[是积分还是钱]
     * @return [boolean]          [增加成功ID]
     */
    public static function add(int $miniapp_id,int $uid,float $money,$message,int $from_uid = 0,string $order_no = ''){
        $data['member_miniapp_id'] = $miniapp_id;
        $data['user_id']           = $uid;
        $data['money']             = $money;
        $data['message']           = $message;
        $data['from_uid']          = $from_uid;
        $data['order_no']          = $order_no;
        $data['update_time']       = time();
        return self::insert($data);
    }
}