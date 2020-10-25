<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 小程序公共API服务
 */
namespace app\fastshop\controller\api\v3;
use app\fastshop\controller\api\Base;
use app\fastshop\model\Fare;
use app\fastshop\model\EntrustList;
use app\fastshop\model\Shopping;
use app\fastshop\model\ShoppingCache;
use app\fastshop\model\Config;
use app\fastshop\model\Store as AppStore;
use app\fastshop\model\Bank;
use app\common\facade\WechatPay;
use app\common\model\SystemMemberPayment;
use app\common\model\SystemUserAddress;
use app\common\model\SystemMemberBank;
use think\facade\Request;

class Store extends Base{

    
    public function initialize() {
        parent::initialize();
        $this->isUserAuth();
    }
 
     /**
     * 我的宝贝
     * @return void
     */
    public function index(){
        $param['page']  = Request::param('page/d');
        $param['types'] = Request::param('types/d',0);
        $param['sign']  = Request::param('sign');
        $rel = $this->apiSign($param);
        if($rel['code'] != 200){
            return enjson(204,'签名失败');
        }
        $condition['user_id'] =  $this->user->id;
        switch ($param['types']) {
            case 1:
                $condition['is_under']  = 0;
                $condition['is_rebate'] = 0;
                break;
            case 2:
                $condition['is_under']  = 1;
                $condition['is_rebate'] = 1;
                break;
            default:
                $condition['is_rebate'] = 0;
                $condition['is_under']  = 1;
                break;
        }
        $lists = EntrustList::with(['Item'=> function($query) {
            $query->field('id,name,img');
        }])->where($condition)->order('id desc')->paginate(10,true);
        $data = [];
        foreach ($lists as $key => $value) {
            $data[$key]['id']            = $value->id;
            $data[$key]['item']          = $value->item;
            $data[$key]['status_text']   = EntrustList::status($value);
            $data[$key]['create_time']   = date('Y-m-d H:i',$value->create_time);
            $data[$key]['is_under']      = $value->is_under;
            $data[$key]['is_rebate']     = $value->is_rebate;
            $data[$key]['rebate']        = money($value->rebate/100);
            $data[$key]['entrust_price'] = $value->entrust_price;
        }
        if(empty($data)){
            return enjson(204,'无内容');
        }
        return enjson(200,'成功',$data);
    }

     /**
     * 判断是否开通了小店
     * @return void
     */
    public function isopen(){
        if($this->user){
            $list = model('Store')->where(['uid' => $this->user->id])->find();
            if(empty($list)){
                return json(['code'=>204,'msg'=>'未开通']);
            }
            return json(['code'=>200,'msg'=>'已开通','data' => $list]);   
        }
    }

    /**
     * 上下架宝贝
     * @return void
     */
    public function onUnder(){
        if (request()->isPost()) {
            $param['id']   = Request::param('id/d');
            $param['sign'] = Request::param('sign');
            $rel = $this->apiSign($param);
            if($rel['code'] != 200){
                return enjson(204,'签名失败');
            }
            $rel = EntrustList::where(['user_id' => $this->user->id,'is_rebate' => 0,'id' => $param['id']])->field('id,is_under,create_time')->find();
            if(empty($rel)){
                return json(['code'=>403,'msg'=>'您的宝贝已成交或未找到']);
            }
            if($rel->is_under == 1){
                $config  = Config::where(['member_miniapp_id' => $this->miniapp_id])->field('lock_sale_day')->find();//读取配置
                if($config->lock_sale_day > 0){
                    $days7 = $rel->create_time+$config->lock_sale_day*86400;
                    if(time() < $days7){
                        return json(['code'=>403,'msg'=>'您的商品未到上架时间']);
                    }
                }
            }            
            $msg = empty($rel->is_under) ? '宝贝已下架' : '宝贝已上架';
            EntrustList::where(['id' => $rel->id])->update(['is_under' => empty($rel->is_under) ? 1 : 0]);
            return json(['code'=>200,'msg' => $msg]);   
        }
    }

     /**
     * 确认提货
     * @return void
     */
    public function onOrder(){
        if (request()->isPost()) {
            $param['id']      = Request::param('id/d');
            $param['buytype'] = Request::param('buytype/s','wepay');
            $param['sign']    = Request::param('sign');
            $rel = $this->apiSign($param);
            if($rel['code'] != 200){
                return enjson(204,'签名失败');
            }
            $rel = EntrustList::where(['user_id' => $this->user->id,'is_rebate' => 0,'id' => $param['id'],'is_under' => 1])->find();
            if(empty($rel)){
                return json(['code'=>403,'msg'=>'宝贝未找到或未下架']);
            }
            //读取发货地址
            $address = SystemUserAddress::where(['user_id'=>$this->user->id,'is_first' => 1])->find();
            if(empty($address)){
                return json(['code'=>403,'msg'=>'请重新选择收货地址']);
            }
            //支付接口
            $payment = SystemMemberPayment::where(['apiname' => 'wepay','member_miniapp_id'=>$this->miniapp_id])->find();
            if(empty($payment)){
                return json(['code'=>403,'msg'=>'未开通微信支付功能']);
            }
            $order_no = $this->user->invite_code.order_no(); //生成的订单号
            $amount = Fare::realAmount([['weight' =>$rel->item->weight,'amount' => 0,'num' => 1]],$this->miniapp_id);
            $config  = Config::where(['member_miniapp_id' => $this->miniapp_id])->find();           
            if($param['buytype'] == 'point'){
                if($config->payment_type_shop == 0){
                    return json(['code'=>403,'msg'=>"未开通余额支付功能"]);
                }
                $point_fee = money($amount['order_amount']*($config->payment_point_shop/100));  //积分付款
                if ($point_fee <= 0) {
                    $param['buytype'] = 'wepay'; //如果积分付款为零0转换为正常的微信全额支付
                    $order_amount = money($amount['order_amount']);
                }else{
                    $order_amount = money($amount['order_amount'] - $point_fee);
                    $order_amount = $order_amount <= 0 ? 1 : $order_amount; //如果是100%积分设置最低付款金额
                    //判断积分够不够
                    $rel = Bank::isPay($this->user->id,$point_fee,$config->payment_type_shop);
                    if(!$rel){
                        return json(['code'=>403,'msg'=>"余额不足,请选择其它支付渠道"]);
                    }
                }
            }else{
                $order_amount = $amount['order_amount']; 
            }
            $order_amount  = $order_amount <= 0 ? 1 :$order_amount;
            //判断云收银台
            if($config->is_pay_types == 1 && $config->goodpay_tax > 0){
                $goodpay_tax = $param['money']*$config->goodpay_tax/100;
                $bank_rel = SystemMemberBank::moneyJudge($this->miniapp->member_id,$goodpay_tax);
                if($bank_rel){
                    return ['code'=>0,'message'=>'官方帐号余额不足,请联系管理员'];
                }
            }
            $order_no = $this->user->invite_code.order_no(); //生成的订单号
            $order = [
                'payment_id'        => $payment['id'],
                'express_name'      => $address['name'],
                'express_phone'     => $address['telphone'],
                'express_address'   => $address['address'],
                'order_amount'      => $amount['order_amount'],
                'real_amount'       => $amount['real_amount'],
                'real_freight'      => $amount['real_freight'],
                'order_no'          => $order_no,
                'member_miniapp_id' => $this->miniapp_id,
                'user_id'           => $this->user->id,
                'paid_at'           => 0,
                'order_starttime'   => time(),
            ];
            $order_id = Shopping::insert($order); 
            if(empty($order_id)){
                return enjson(403,'创建订单失败');
            }
            //保存订单产品到缓存数据表
            ShoppingCache::insert(['order_no' => $order_no,'item_id' => $rel->item->id,'buy_price' => 0,'buy_nums' => 1,'name' => $rel->item->name,'img' => $rel->item->img]);
            //支付方式
            if($config->is_pay_types == 1){
                $pay_coinfig = json_decode($payment->config);
                $ispay = [
                    'name'       => $this->miniapp->appname.'购买商品',
                    'mchid'      => $pay_coinfig->mch_id,
                    'total_fee'  => $order_amount*100,
                    'order_no'   => $order_no,
                    'note'       => $this->miniapp_id,
                    'notify_url' => $param['buytype'] == 'wepay'? api(3,'fastshop/goodpay/resetSale',$this->miniapp_id) : api(3,'fastshop/goodpay/resetSalePoint',$this->miniapp_id),
                    'publickey'  => uuid(1)
                ];
                $paydata = $this->makeSign($ispay,$pay_coinfig->key);
            }else{
                $payparm = [
                    'openid'     => $this->user->miniapp_uid,
                    'miniapp_id' => $this->miniapp_id,
                    'name'       => $this->miniapp->appname.'购买商品',
                    'order_no'   => $order_no,
                    'total_fee'  => $order_amount*100,
                    'notify_url' => api(3,'fastshop/notify/shop',$this->miniapp_id),
                    'notify_url' => $param['buytype'] == 'wepay'? api(3,'fastshop/notify/resetSale',$this->miniapp_id) : api(3,'fastshop/notify/resetSalePoint',$this->miniapp_id),
                ];
                $ispay = WechatPay::orderPay($payparm);
                if($ispay['code'] == 0){
                    return enjson(403,$ispay['msg']);
                }
                $paydata = $ispay['data'];
            }
            $rel->is_rebate = 1;
            $rel->is_under = 1;
            $rel->save();
            return enjson(200,'成功',['type' => $config->is_pay_types,'order' => $paydata]);
        }
    }   

     /**
     * 开通小店
     * @return void
     */
    public function regStore(){
        if (request()->isPost()) {
            $param['store_name'] = Request::param('store_name/s');
            $param['sign']       = Request::param('sign');
            $rel = $this->apiSign($param);
            if($rel['code'] != 200){
                return enjson(403,'签名失败');
            }
            $store = AppStore::where(['uid' => $this->user->id])->find();
            if(!empty($store)){
                return json(['code'=>403,'msg'=>'已开通小店不用重复申请']);
            }
            $data = [
                'name'        => $param['store_name'],
                'uid'         => $this->user->id,
                'update_time' => time(),
            ];
            if (empty($data['name'])) {
                return json(['code'=>403,'msg'=>'必须输入小店名称']);
            }
            $rel = AppStore::insert($data);
            if($rel){
                return json(['code'=>200,'msg'=>'小店开通成功']);
            }
            return json(403);
        }
    }
}