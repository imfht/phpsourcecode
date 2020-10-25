<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 用户银行表 Table<ai_allwin_bank_cash>
 */
namespace app\green\model;
use app\common\facade\Inform;
use app\common\facade\WechatPay;
use think\Model;

class GreenBankCash extends Model{
    
    protected $pk     = 'id';

    //用户
    public function user(){
        return $this->hasOne('app\common\model\SystemUser','id','user_id');
    }

    /**
     * 提现申请审核操作
     * @param array $param
     * @return boolean
     */
    public static function isPass(array $param,$member_miniapp_id){
        $id         = intval($param['id']);
        $ispass     = intval($param['ispass']);
        $miniapp_id = intval($param['miniapp_id']);
        $cash       = self::where(['member_miniapp_id' =>$miniapp_id,'id'=> $id,'state' => 0])->find();
        if($cash){
            $setting = GreenConfig::where(['member_miniapp_id' =>$miniapp_id])->find();
            $realmoney = $cash->money;
            //判断是否成功
            $is_success = false;
            $is_diy     = false;
            $trade_no = $cash->user->invite_code.order_no().'WE';
            if($ispass){
                if($setting->is_wechat_touser && (!empty($cash->user->official_uid) || $realmoney < 5000)){
                    $trade_no .= 'WE';
                    $app = WechatPay::doPay($miniapp_id,true);
                    $rel = $app->transfer->toBalance(['partner_trade_no' => $trade_no,'openid' => $cash->user->official_uid,'check_name' => 'NO_CHECK','amount' => $realmoney*100,'desc' => '提现']);
                    if ($rel['return_code'] === 'SUCCESS') {
                        if ($rel['result_code'] === 'SUCCESS') {
                            $state      = 1;
                            $is_success = true;
                            $is_diy     = true;
                            $message    = "[通过]申请转出已结算到微信钱包";
                        }else{
                            $message    = $rel['err_code_des'];
                        }
                    }else{
                        $message    = $rel['return_msg'];
                    }
                }else{
                    $trade_no .= 'BANK';
                    $is_diy = true;
                    $message    = "[通过]申请转出已结算到您填写的账户";
                }
                if($is_diy){
                    $bank = self::isPassCash($cash->user_id,$cash->money);
                    if($bank){
                        $state      = 1;
                        $is_success = true;
                    }
                }
            }else{
                $trade_no .= 'BANK';
                $bank = self::isPassCash($cash->user_id,$cash->money,false);
                if($bank){
                    $state      = -1;
                    $is_success = true;
                    $message    = "[失败]申请转出,已退回账户";
                }
            }
            if($is_success){
                $data['state']      = $state;
                $data['realmoney']  = $realmoney;
                $data['audit_time'] = time();
                $data['trade_no']   = $trade_no;
                $data['msg']        = $message;
                self::where(['member_miniapp_id' =>$miniapp_id,'id'=> $id])->update($data);
                //通知申请者到微信
                if($state){
                    Inform::sms($cash->user_id,$member_miniapp_id,['title' =>'您的账户进行了一笔转账交易','type' => '个人转出','content' =>$cash->money.'元','state' => '成功']);
                }else{
                    Inform::sms($cash->user_id,$member_miniapp_id,['title' =>'您的账户进行了一笔转账交易','type' => '个人转出','content' =>$cash->money.'元','state' => '失败']);
                }
                return ['code'=>200,'msg' => $message,'url' => url('green/bank/cashpass',['id' => $id])];
            }else{
                return ['code'=>0,'msg'=>$message,'url' => url('green/bank/cashpass',['id' => $id])];
            }
        }else{
            return ['code'=>0,'msg'=>'操作失败,为找到申请提现记录.'];
        }
    }

    protected static function isPassCash(int $uid,float $money,$is_success = true){
        if($is_success == false){
            $info  = GreenUser::where(['uid' => $uid])->find();
            $money = $money*1000;
            $info->points  = ['inc',$money];
            $info->update_time    = time();
            return $info->save();
        }
        return true;
    }
}