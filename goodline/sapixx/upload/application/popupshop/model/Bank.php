<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 用户银行表 Table<ai_popupshop_bank>
 */
namespace app\popupshop\model;
use think\Model;
use app\popupshop\model\BankBill;
use app\popupshop\model\BankCash;
use app\popupshop\model\Config;
use app\common\facade\WechatPay;
use Exception;

class Bank extends Model{
    
    protected $pk     = 'id';
    protected $table  = 'ai_popupshop_bank';
    protected $autoWriteTimestamp = true;
    protected $createTime = false;

    //用户
    public function user(){
        return $this->hasOne('app\common\model\SystemUser','id','user_id');
    }

    /**
     * 提现申请审核操作
     *
     * @param array $param
     * @return boolean
     */
    public static function isPass(array $param){
        $id         = (int)$param['id'];
        $ispass     = (int)$param['ispass'];
        $miniapp_id = (int)$param['miniapp_id'];
        $cash  = BankCash::where(['member_miniapp_id' =>$miniapp_id,'id'=> $id,'state' => 0])->find();
        if($cash){
            $is_rel    = false;  //操作是否成功
            $is_diy    = false;
            $trade_no  = $cash->user->invite_code.order_no(); 
            $config    = Config::where(['member_miniapp_id' => $miniapp_id])->field('tax,is_wechat_touser')->find();
            $realmoney = $cash->money - $cash->money * ($config->tax/100);
            if($ispass){
                if($config->is_wechat_touser && (!empty($cash->user->official_uid) || $realmoney < 5000)){
                    $trade_no .= 'WX';
                    try {
                        $rel = WechatPay::doPay($miniapp_id)->transfer->toBalance(['partner_trade_no' => $trade_no,'openid' => $cash->user->miniapp_uid,'check_name' => 'NO_CHECK','amount' => $realmoney*100,'desc' => '酬劳与收入']);
                        if ($rel['result_code'] === 'SUCCESS') {
                            if ($rel['result_code'] === 'SUCCESS') {
                                $is_diy  = true;
                                $message = "[通过]申请转出已结算到微信钱包";
                            }else{
                                $message = $rel['return_msg'];
                            }
                        }else{
                            $message    = $rel['return_msg'];
                        }
                    }catch (Exception $e) {
                        $message = $e->getMessage();
                    }
                }else{
                    $trade_no .= 'CD';
                    $is_diy    = true;
                    $message   = "[通过]申请转出已结算到您填写银行账户";
                }
                if($is_diy){
                    $bank = self::isPassCash($cash->user_id,$cash->money);
                    $state   = 1;
                    $is_rel  = true;
                }
            }else{
                $bank = self::isPassCash($cash->user_id,$cash->money,false);
                if($bank){
                    $message = "[失败]申请转出(".money($cash->money)."),退回账户";
                    $state   = -1;
                    $is_rel  = true;
                }else{
                    $message = '操作失败~申请提交帐号异常';
                }
            }
            if($is_rel){
                BankBill::add($miniapp_id,$cash->user_id,$cash->money,$message);
                $cash->state      = $state;
                $cash->realmoney  = $realmoney;
                $cash->audit_time = time();
                $cash->trade_no   = $trade_no;
                $cash->msg        = $message;
                $cash->save();    //更新提现状态
                return ['code'=>200,'msg' => $message,'url' => url('popupshop/bank/cashpass',['id' => $id])];
            }else{
                return ['code'=>0,'msg'=> $message ,'url' => url('popupshop/bank/cashpass',['id' => $id])];
            }
        }else{
            return ['code'=>0,'msg'=>'操作失败,为找到申请提现记录.'];
        }  
    }

    //申请提现是否成功
    protected static function isPassCash(int $uid,float $money,$isSuccess = true){
        $info = self::where(['user_id' => $uid])->find();
        if(empty($info)){
            return;
        }
        if ($info->lack_money < $money) {
            return;
        }
        $info->lack_money = ['dec',money($money)];
        if($isSuccess == false){
            $info->due_money  = ['inc',money($money)];
        }       
        return $info->save();
    }

    /**
     * 提现申请(小程序API)
     * @param integer $miniapp_id
     * @param integer $uid
     * @param integer $money（元）
     * @return void
     */
    public static function cash(int $miniapp_id,int $uid,float $money){
        $info = self::where(['member_miniapp_id'=>$miniapp_id,'user_id' => $uid])->find();
        if(empty($info)){
            return;
        }
        if($info->due_money < $money){
            return;
        }
        $info->due_money   = ['dec',money($money)];
        $info->lack_money  = ['inc',money($money)];
        $info->update_time = time();
        return $info->save();
    }

    /**
     * 收入增加
     * @param integer $miniapp_id
     * @param integer $uid
     * @param integer $due_money
     * @param integer $shop_money
     * @return void
     */
    public static function setDueMoney(int $miniapp_id,int $uid,float $due_money,int $shop_money = 0){
        $info = self::where(['user_id' => $uid])->find();
        $due_money  = abs($due_money);
        $shop_money = abs($shop_money);
        if(empty($info)){
            $data['member_miniapp_id'] = $miniapp_id;
            $data['user_id']           = $uid;
            $data['due_money']         = $due_money;
            $data['income_money']     = $due_money;
            $data['shop_money']        = $shop_money;
            $data['update_time']       = time();
            return self::insert($data);
        }
        $info->income_money  = ['inc',money($due_money)];
        $info->due_money      = ['inc',money($due_money)];
        $info->shop_money     = ['inc',money($shop_money)];
        $info->update_time    = time();
        return $info->save();
    }

    /**
     * 应付帐号充值
     * @param integer $miniapp_id
     * @param integer $uid
     * @param integer $money（元）
     * @return void
     */
    public static function moneyChange(int $miniapp_id,int $uid,float $money){
        $info = self::where(['member_miniapp_id'=>$miniapp_id,'user_id' => $uid])->find();
        if(empty($info)){
            $data['member_miniapp_id'] = $miniapp_id;
            $data['user_id']           = $uid;
            $data['due_money']         = $money <= 0 ? 0 : money($money);
            $data['update_time']       = time();
            return self::insert($data);
        }else{
            $info->due_money   = $money > 0 ? ['inc',money($money)] : ['dec',money($money)];
            $info->update_time = time();
            return $info->save();
        }
    }

   /**
     * 购物积分帐号充值
     * @param integer $miniapp_id
     * @param integer $uid
     * @param integer $money（元）
     * @return void
     */
    public static function shopChange(int $miniapp_id,int $uid,float $money){
        $info = self::where(['member_miniapp_id'=>$miniapp_id,'user_id' => $uid])->find();
        if(empty($info)){
            $data['member_miniapp_id'] = $miniapp_id;
            $data['user_id']           = $uid;
            $data['shop_money']        = $money <= 0 ? 0 : money($money);
            $data['update_time']       = time();
            return self::insert($data);
        }else{
            $info->shop_money  = $money > 0 ? ['inc',money($money)] : ['dec',money($money)];
            $info->update_time = time();
            return $info->save();
        }
    }


    /**
     * 转账小程序API
     * @param integer $miniapp_id
     * @param integer $uid
     * @param integer $money（元）
     * @param bool    $recipient  (是否接收方)
     * @return void
     */
    public static function transfer(int $miniapp_id,int $uid,float $money,bool $recipient = false){
        if($money <= 0){
            return;
        }
        $info = self::where(['member_miniapp_id' => $miniapp_id,'user_id' => $uid])->find();
        if($recipient){
            if(empty($info)){
                $data['member_miniapp_id'] = $miniapp_id;
                $data['user_id']           = $uid;
                $data['due_money']         = money($money);
                return self::insert($data);
            }else{
                $info->due_money = ['inc',money($money)];
                return $info->save();
            }
        }else{
            if(empty($info)){
                return;
            }
            if($info->due_money < $money){
                return;
            }
            $info->due_money   = ['dec',money($money)];
            $info->update_time = time();
            return $info->save();
        }
    }

    /**
     * 增加净收入
     * @param integer $uid
     * @param integer $money
     * @return void
     */
    public function isProfit(int $uid,float $money){
        $info = self::where(['user_id' => $uid])->find();
        if(empty($info)){
            return;
        }
        if($money > 0){
            $info->profit = ['inc',$money];
        }else{
            $info->profit = 0;
        }
        return $info->save();
    }
}