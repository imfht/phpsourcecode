<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 商城小程序支付回调
 */
namespace app\fastshop\controller\api\v3;
use app\fastshop\controller\api\Base;
use app\fastshop\model\Shopping;
use app\fastshop\model\Vip;
use app\fastshop\model\Config;
use app\common\model\SystemMemberPayment;
use app\common\model\SystemMemberBankBill;
use app\common\model\SystemMemberBank;
use sign\Sign;
use filter\Filter;

class Goodpay extends Base{

    protected $param = [];
    protected $config;

    public function initialize() {
        parent::initialize();
        if (request()->isPost()) {
            $fileContent = file_get_contents("php://input"); 
            if(empty($fileContent)){
                exit('FAIL');
            }
            $data = Sign::fromXml($fileContent);
            if(empty($data)){
                exit('FAIL');
            }
            if($data['return_code'] != 'SUCCESS'){
                exit('FAIL');
            }
            //支付接口
            $payment = SystemMemberPayment::config($this->miniapp_id,'wepay');
            if(empty($payment)){
                exit('FAIL');
            }
            //签名认证
            $args = [];
            $args['mchid']          = $data['mchid'];
            $args['goodpay_order']  = $data['goodpay_order'];
            $args['transaction_id'] = $data['transaction_id'];
            $args['out_trade_no']   = $data['out_trade_no'];
            $args['cash_fee']       = $data['cash_fee'];
            $args['time_end']       = $data['time_end'];
            $args['nonce_str']      = $data['nonce_str'];
            if($data['sign'] != Sign::makeSign($args,$payment['key'],'md5')){
                exit('FAIL');
            }
            //不参与签名
            $args['attach']        = $data['attach'];
            $args['state_desc']    = $data['state_desc'];
            $this->param = Filter::filter_escape($data);
            $this->config = Config::where(['member_miniapp_id' => $this->miniapp_id])->find();
            if($this->config->is_pay_types == 1 && $this->config->goodpay_tax > 0){
                $goodpay_tax = $this->param['cash_fee']/100*$this->config->goodpay_tax/100;
                SystemMemberBankBill::create(['state' => 1,'money' => $goodpay_tax,'member_id' => $this->miniapp->member_id,'message'=> '云收银台','update_time' => strtotime($this->param['time_end'])]);
                SystemMemberBank::moneyUpdate($this->miniapp->member_id,-$goodpay_tax);
            }
        }else{
            exit('FAIL');
        }
    }
 
    /**
     * 商城购买微信支付
     * @return void
     */
    public function shop(){
        $order = Shopping::where(['paid_at' => 0,'order_no' => $this->param['out_trade_no']])->find();
        if(empty($order)){
            return 'SUCCESS';
        } 
        $order->paid_at   = 1;
        $order->paid_time = strtotime($this->param['time_end']);
        $order->paid_no   = $this->param['goodpay_order'];
        $order->save();
        return 'SUCCESS';
    }

    /**
     * 商城购买积分支付
     * @return void
     */
    public function shopPoint(){
        $order = Shopping::where(['paid_at' => 0,'order_no' => $this->param['out_trade_no']])->find();
        if(empty($order)){
            return 'SUCCESS';
        } 
        $rel = widget('order/shopPointPay', ['miniapp_id' => $this->miniapp_id,'cash_fee'=> $order->order_amount,'uid' => $order->user_id]);   //减积分
        if ($rel) {
            $order->paid_at   = 1;
            $order->paid_time = strtotime($this->param['time_end']);
            $order->paid_no   = $this->param['goodpay_order'];
            $order->save();
        }
        return 'SUCCESS';
    }

    /**
     * 活动商品微信支付
     * @return void
     */
    public function sale(){
         $order = model('Order')->where(['order_no' => $this->param['out_trade_no'],'paid_at' => 0])->find();
         if (empty($order)){
            return 'SUCCESS';
         }
         $order->paid_at   = 1;
         $order->paid_time = strtotime($this->param['time_end']);
         $order->paid_no   = $this->param['goodpay_order'];
         $order->save();
        //减库存
        model('Sale')->where(['id' => $order->sale_id])->setDec('sale_nums', 1);
        //计算委托人收益
        widget('order/rebate', ['miniapp_id' =>$this->miniapp_id,'order_no' => $order->order_no,'item_id' => 0,'uid' => $order->user_id,'config' => $this->config]);
        //奖励计算
        $param = ['miniapp_id' =>$this->miniapp_id,'order'=> $order,'uid' => $order->user_id,'config' => $this->config];
        if ($this->config->reward_types) {
            widget('Reward/performance', $param);
            widget('Reward/range',$param);
        } else {
            widget('Reward/level',$param);
        }
        widget('Reward/agent',$param); //计算代理
        model('BankLogs')->add($this->miniapp_id, $order->user_id, -($order->order_amount*100), '￥'.money(-$order->order_amount).'微信支付,单号('.$order->order_no.')', $order->user_id, $order->order_no);
        return 'SUCCESS';
    }

    /**
     * 活动商品积分支付
     * @return void
     */
    public function salePoint(){
         $order = model('Order')->where(['order_no' => $this->param['out_trade_no'],'paid_at' => 0])->find();
         if (empty($order)){
            return 'SUCCESS';
         }
        //读取配置
        $rel = widget('order/pointPay', ['miniapp_id' => $this->miniapp_id,'cash_fee'=> money($order->order_amount),'uid' => $order->user_id,'config' => $this->config]);
        if ($rel) {
            $order->paid_at   = 1;
            $order->is_point  = 1;
            $order->paid_time = strtotime($this->param['time_end']);
            $order->paid_no   = $this->param['goodpay_order'];
            $order->save();
            model('Sale')->where(['id' => $order['sale_id']])->setDec('sale_nums', 1);
            //计算委托人收益
            widget('order/rebate', ['miniapp_id' => $this->miniapp_id,'order_no' => $order->order_no,'item_id' => 0,'uid' => $order->user_id,'config' => $this->config]);
            //计算其他人的收益
            $param = ['miniapp_id' =>$this->miniapp_id,'order' => $order,'uid' => $order->user_id,'config' => $this->config];
            if ($this->config->reward_types) {
                widget('Reward/performance',$param);
                widget('Reward/range',$param);
            } else {
                widget('Reward/level',$param);
            }
            widget('Reward/agent', $param);
            model('BankLogs')->add($this->miniapp_id, $order->user_id,-($order->order_amount*100),'￥'.money(-$order->order_amount).'余额支付,单号('.$order->order_no.')', $order->user_id, $order->order_no);
            return 'SUCCESS';
        }
        return 'FAIL';
    }

    /**
     * 抢购微信支付(提货)
     * @return void
     */
    public function resetSale(){
        $order = Shopping::where(['paid_at' => 0,'order_no' => $this->param['out_trade_no']])->find();
        if(empty($order)){
            return 'SUCCESS';
        } 
        model('EntrustList')->where(['item_id' => $order->orderItem->item_id,'user_id' => $order->user_id,'is_rebate' => 0,'is_under' => 0])->update(['is_rebate' => 1,'is_under' => 1,'rebate' => 0,'update_time' => $order->paid_time]);
        $order->paid_at   = 1;
        $order->paid_time = strtotime($this->param['time_end']);
        $order->paid_no   = $this->param['goodpay_order'];
        $order->save();
        return 'SUCCESS';
    }

    /**
     * 抢购微信支付(提货)
     * @return void
     */
    public function resetSalePoint(){
        $order = Shopping::where(['paid_at' => 0,'order_no' => $this->param['out_trade_no']])->find();
        if(empty($order)){
            return 'SUCCESS';
        } 
        $rel = widget('order/shopPointPay', ['miniapp_id' => $this->miniapp_id,'cash_fee'=> $order->order_amount,'uid' => $order->user_id]);   //减积分
        if ($rel) {
            model('EntrustList')->where(['item_id' => $order->orderItem->item_id,'user_id' => $order->user_id,'is_rebate' => 0,'is_under' => 0])->update(['is_rebate' => 1,'is_under' => 1,'rebate' => 0,'update_time' => $order->paid_time]);
            $order->paid_at   = 1;
            $order->paid_time = strtotime($this->param['time_end']);
            $order->paid_no   = $this->param['goodpay_order'];
            $order->save();
            return 'SUCCESS';
        }
        return 'FAIL';
    }

    /**
     * 充值
     * @return void
     */
    public function recharge(){
        $order = model('BankCash')->re_table()->where(['order_no' => $this->param['out_trade_no'],'state' => 0])->find();
        if (empty($order)){
            return 'SUCCESS';
        }
        $order->state     = 1;
        $order->paid_time = strtotime($this->param['time_end']);
        $order->paid_no   = $this->param['goodpay_order'];
        $order->save();
        model('Bank')->recharge($this->miniapp_id,$order->user_id,$order->money);
        model('BankLogs')->add($this->miniapp_id,$order->user_id,intval($order->money*100),'充值￥'.money($order->money).'('.$order->order_no.')');
        return 'SUCCESS';
    }  

    /**
     * 开通会员
     * @return void
     */
    public function openVip(){
        $order = Vip::where(['state' => 0,'order_no' => $this->param['out_trade_no']])->find();
        if (empty($order)){
            return 'SUCCESS';
        }
        $order->state     = 1;
        $order->paid_time = strtotime($this->param['time_end']);
        $order->paid_no   = $this->param['goodpay_order'];
        $order->save();
        widget('vip/level', ['miniapp_id' =>$this->miniapp_id,'cash_fee'=> $this->param['cash_fee'],'uid' => $order->user_id]);
        return 'SUCCESS';
    }    
}