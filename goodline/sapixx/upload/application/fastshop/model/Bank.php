<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 用户银行表 Table<ai_fastshop_bank>
 */
namespace app\fastshop\model;
use think\Model;
use think\facade\Validate;

class Bank extends Model{
    
    protected $pk     = 'id';
    protected $table  = 'ai_fastshop_bank';
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
    public function ispass(array $param){
        $id         = (int)$param['id'];
        $ispass     = (int)$param['ispass'];
        $miniapp_id = (int)$param['miniapp_id'];
        $cash  = model('BankCash')->where(['member_miniapp_id' =>$miniapp_id,'id'=> $id,'state' => 0])->find();
        if($cash){
            $config    = model('Config')->field('tax')->get(['member_miniapp_id' => $miniapp_id]);
            $realmoney = intval($cash->money - $cash->money * ($config->tax/100)); //分
            $rel = false;
            if($ispass){
                $bank = self::isPassCash($cash->user_id,$cash->money);
                if($bank){
                    $message       = "[通过]申请提取(".money($cash->money/100)."),结算(".money($realmoney/100)."),解锁积分.";
                    $rel           = true;
                    $data['state'] = 1;
                }
            }else{
                $bank = self::isPassCash($cash->user_id,$cash->money,false);
                if($bank){
                    $message       = "[失败]申请提取(".money($cash->money/100)."),解锁积分并退回账户.";
                    $rel           = true;
                    $data['state'] = -1;
                }
            }
            if($rel){
                $data['realmoney'] = $realmoney;
                model('BankCash')->where(['member_miniapp_id' =>$miniapp_id,'id'=> $id])->update($data);  //更新提现状态
                model('BankLogs')->add($miniapp_id,$cash->user_id,$cash->money,$message);    //增加财务日志
                return ['code'=>200,'msg' => $message,'url' => url('fastshop/bank/cashpass',['id' => $id])];
            }else{
                return ['code'=>0,'msg'=>'操作失败,锁定金额和提现金额不区配','url' => url('fastshop/bank/cashpass',['id' => $id])];
            }
        }else{
            return ['code'=>0,'msg'=>'操作失败,为找到申请提现记录.'];
        }  
    }

    //申请提现是否成功
    protected function isPassCash(int $uid,int $money,$isSuccess = true){
        $info = self::get(['user_id' => $uid]);
        if ($info->lack_money < $money) {
            return;
        }
        $data['lack_money'] = $info->lack_money - $money;
        if($isSuccess == false){
            $data['due_money'] = $info->due_money + $money;
            $data['money']     = $info->money + $money;
        }
        return self::where(['user_id' => $uid])->update($data);
    }

    //应付积分和购物积分增加
    public function due_up(int $miniapp_id,int $uid,int $due_money,int $shop_money){
        $data['update_time'] = time();
        $info = self::get(['user_id' => $uid]);
        $due_money  = abs($due_money);
        $shop_money = abs($shop_money);
        if(empty($info)){
            $data['member_miniapp_id'] = $miniapp_id;
            $data['user_id']           = $uid;
            $data['due_money']         = $due_money;
            $data['shop_money']        = $shop_money;
            $data['income_money']     = $due_money+$shop_money;
            $data['money']             = $due_money+$shop_money;
            return self::insert($data);
        }
        $data['income_money'] = $info->income_money + ($due_money+$shop_money);
        $data['money']         = $info->money + ($due_money+$shop_money);
        $data['due_money']     = $info->due_money + $due_money;
        $data['shop_money']    = $info->shop_money + $shop_money;
        return self::where(['user_id' => $uid])->update($data);
    }

    /**
     * 应付帐号充值
     * @param integer $miniapp_id
     * @param integer $uid
     * @param integer $money（元）
     * @return void
     */
    public function recharge(int $miniapp_id,int $uid,float $money){
        $info = self::get(['member_miniapp_id'=>$miniapp_id,'user_id' => $uid]);
        $money = $money * 100;
        $data['update_time'] = time();
        if(empty($info)){
            $amout = $money <= 0 ? 0 : $money;
            $data['member_miniapp_id'] = $miniapp_id;
            $data['user_id']           = $uid;
            $data['shop_money']        = $amout;
            $data['due_money']         = $amout;
            $data['money']             = $amout;
            return self::insert($data);
        }else{
            $due_money = $info->due_money + $money;
            $amout     = $info->money + $money;
            if($money < 0){
                $due_money = $due_money < 0 ? 0 : $due_money;
                $amout     = $amout < 0 ? 0 : $amout;
            }
            $data['due_money']   = $due_money;
            $data['money']       = $amout;
            return self::where(['user_id' => $uid])->update($data);
        }
    }

   /**
     * 购物积分帐号充值
     * @param integer $miniapp_id
     * @param integer $uid
     * @param integer $money（元）
     * @return void
     */
    public function rechargeShop(int $miniapp_id,int $uid,float $money){
        $info = self::get(['member_miniapp_id' => $miniapp_id,'user_id' => $uid]);
        $money = $money * 100;
        $data['update_time'] = time();
        if(empty($info)){
            $amout = $money <= 0 ? 0 : $money;
            $data['member_miniapp_id'] = $miniapp_id;
            $data['user_id']           = $uid;
            $data['shop_money']        = $amout;
            $data['due_money']         = $amout;
            $data['money']             = $amout;
            return self::insert($data);
        }else{
            $shop_money = $info->shop_money + $money;
            $amout     = $info->money + $money;
            if($money < 0){
                $shop_money = $shop_money < 0 ? 0 : $shop_money;
                $amout     = $amout < 0 ? 0 : $amout;
            }
            $data['shop_money']  = $shop_money;
            $data['money']       = $amout;
            return self::where(['user_id' => $uid])->update($data);
        }
    }

    /**
     * 提现申请(小程序API)
     * @param integer $miniapp_id
     * @param integer $uid
     * @param integer $money（元）
     * @return void
     */
    public static function cash(int $miniapp_id,int $uid,float $money){
        $info = self::get(['member_miniapp_id'=>$miniapp_id,'user_id' => $uid]);
        if(empty($info)){
            return;
        }
        $money = $money * 100;
        if($info['due_money'] < $money || $info['money'] < $money ){
            return;
        }
        $data['due_money']   = (int)$info['due_money'] - $money;
        $data['money']       = (int)$info['money'] - $money;
        $data['lack_money']  = (int)$info['lack_money'] + $money;
        $data['update_time'] = time();
        return self::where(['user_id' => $uid])->update($data);
    }


    /**
     * 转账小程序API
     * @param integer $miniapp_id
     * @param integer $uid
     * @param integer $money（元）
     * @param bool    $recipient  (是否接收方)
     * @return void
     */
    public function transfer(int $miniapp_id,int $uid,float $money,bool $recipient = false){
        if($money <= 0){
            return;
        }
        $money = $money * 100;
        $info = self::get(['member_miniapp_id' => $miniapp_id,'user_id' => $uid]);
        if($recipient){
            if(empty($info)){
                $data['member_miniapp_id'] = $miniapp_id;
                $data['user_id']           = $uid;
                $data['due_money']         = 0;
                $data['money']             = 0;
                $data['shop_money']        = $money;
                return self::insert($data);
            }else{
                $info->shop_money = (int)$info->shop_money + $money;
                return $info->save();
            } 
        }else{
            if(empty($info)){
                return;
            }
            if($info->shop_money < $money){
                return;
            }
            $info->shop_money  = (int)$info->shop_money - $money;
            $info->update_time = time();
            return $info->save();
        }
    }

    /**
     * 积分支付(小程序API)
     * @param integer $miniapp_id
     * @param integer $uid
     * @param integer $money
     * @param integer $payment_type  0 应付积分  1 购物积分
     * @return void
     */
    public function payment(int $miniapp_id,int $uid,float $money,int $payment_type = 0){
        $info = self::get(['member_miniapp_id'=>$miniapp_id,'user_id' => $uid]);
        if(empty($info)){
            return;
        }
        $money = $money * 100;
        if($payment_type == 1){
            if($info['due_money'] <= $money || $info['money'] <= $money ){
                return;
            }
            $data['due_money'] = (int)$info['due_money'] - $money;
        }else{
            if($info['shop_money'] <= $money || $info['money'] <= $money ){
                return;
            }
            $data['shop_money'] = (int)$info['shop_money'] - $money;
        }
        $data['money']       = (int)$info['money'] - $money;
        $data['update_time'] = time();
        return self::where(['user_id' => $uid])->update($data);
    }

    /**
     * 判断积分金额够不够
     * @param integer $miniapp_id
     * @param integer $uid
     * @param integer $money
     * @param integer $payment_type  0 应付积分  1 购物积分
     * @return void
     */
    public static function isPay(int $uid,$money,int $payment_type = 0){
        $info = self::where(['user_id' => $uid])->find();
        if(empty($info)){
            return;
        }
        $money = $money * 100;
        if($payment_type == 1){
            if($info->due_money <= $money || $info->money <= $money ){
                return;
            }
        }else{
            if($info->shop_money <= $money || $info->money <= $money ){
                return;
            }
        }
        return true;
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
            $money = intval($money*100);
            $info->profit = ['inc',$money];
        }else{
            $info->profit = 0;
        }
        return $info->save();
    }
}