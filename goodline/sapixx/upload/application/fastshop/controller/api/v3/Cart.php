<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 商城购物车
 */
namespace app\fastshop\controller\api\v3;
use app\fastshop\controller\api\Base;
use app\fastshop\model\Item;
use app\fastshop\model\Fare;
use app\fastshop\model\Config;
use app\fastshop\model\Shopping;
use app\fastshop\model\ShoppingCache;
use app\common\facade\WechatPay;
use app\common\model\SystemMemberPayment;
use app\common\model\SystemUserAddress;
use app\common\model\SystemMemberBank;
use think\facade\Request;
use filter\Filter;

class Cart extends Base{


    public function initialize() {
        parent::initialize();
        $this->isUserAuth();
    }
 
    /**
     * 购物车编辑
     */
    public function edit(){
        $data['item_id'] = Request::param('item_id/d',0);
        $data['buy_num'] = Request::param('buy_num/d',1);
        $data['sign']    = Request::param('sign');
        $rel = $this->apiSign($data);
        if($rel['code'] == 200){
            if($data['buy_num'] > 5){
                return enjson(403,'超过最大购买商品数');
            }
            //检测SPU是否存在或下架
            $spu = Item::where(['id' => $data['item_id'],'is_sale'=>2])->find();
            if(empty($spu)){
                return  enjson(403,'商品已下降');
            }
            $cart[$data['item_id']] = $data['buy_num'];
            return  enjson(200,'成功',$cart);
        }
        return enjson($rel['code'],'签名验证失败');
    }

     /**
     * 读取购物车产品列表
     */
    public function cartItem(){
        if(request()->isPost()){
            $cart = Request::param('cart/a',[]);
            if (empty($cart)) {
                return enjson(204,'没有宝贝');
            }
            $ids = ids(array_keys($cart),true); 
            //查找商品SPU
            $item = Item::where(['is_sale'=>2,'id' => $ids,'member_miniapp_id' => $this->miniapp_id])
                    ->field('id,category_id,name,sell_price,market_price,points,repoints,weight,img')
                    ->select();
            if(empty($item)){
                return enjson(403,'没有宝贝');
            }else{
                $data = [];
                foreach ($item as $value) {
                    $num = abs(intval($cart[$value['id']]));
                    $num = $num <= 0 ? 1: $num;
                    $data[$value['id']] = [
                        'id'           => $value['id'],
                        'num'          => $num,
                        'amount'       => money($value['sell_price'] * $num),  //单个商品价格的总价
                        'market_price' => money($value['market_price']),
                        'sell_price'   => money($value['sell_price']),
                        'weight'       => $value['weight'],
                        'points'       => $value['points'],
                        'repoints'     => $value['repoints'],
                        'name'         => $value['name'],
                        'img'          => $value['img']
                    ];
                }
                $amount = Fare::realAmount($data,$this->miniapp_id);
                return enjson(200,'成功',['item' => $data,'amount' => $amount]);
            }
        }
    }

    /**
     * ################################
     * 判断是否支付了
     */
    public function isPay($order_no){
        $order_no = Filter::filter_escape($order_no);
        $order = Shopping::where(['order_no' => $order_no,'paid_at' => 0])->field('id,order_no,paid_no,order_amount')->find();
        if(empty($order)){
            return enjson(204);
        }
        return enjson();
    } 

    /**
     * 微信商城支付
     * @param string $no
     * @return void
     */
    public function doPay(){
        $param = [
            'address' => Request::param('address/d',0),
            'ids'     => Request::param('ids/s','','htmlspecialchars_decode'),
            'ucode'   => Request::param('ucode'),
            'buytype' => Request::param('buytype','wepay'),
            'sign'    => Request::param('sign')
        ];
        $validate = $this->validate($param,'Cart.add_order');
        if(true !== $validate){
            return enjson(403,$validate);
        }
        $rel = $this->apiSign($param);
        if($rel['code'] != 200){
            return enjson(403,'签名失败');
        }
        $cart = (array)ids(json_decode($param['ids'],true),true); //购买的产品ID和数量
        $ids  = ids(array_keys($cart),true);  //购买的产品ID
        if (empty($ids)){
            return enjson(403,'购物车是空的');
        }
        if (empty($ids[0])){
            return enjson(403,'购物车是空的');
        }
        //读取发货地址
        $address = SystemUserAddress::where(['user_id'=>$this->user->id,'id' =>$param['address']])->find();
        if(empty($address)){
            return enjson(403,'请选择收货地址');
        }
        //支付接口
        $payment = SystemMemberPayment::where(['apiname'=>'wepay','member_miniapp_id'=>$this->miniapp_id])->find();
        if(empty($payment)){
            return enjson(403,'未开通微信支付功能');
        }
        //读取订单 
        $item = Item::where(['is_sale'=>2,'id' => $ids,'member_miniapp_id' => $this->miniapp_id])->field('id,sell_price,market_price,points,repoints,weight,name,img')->select();
        if($item->isEmpty()){
            return enjson(403,'没有宝贝');
        }
        $data = [];
        foreach ($item as $value) {
            $num = abs(intval($cart[$value['id']]));
            $num = $num <= 0 ? 1: $num;
            $data[$value['id']] = [
                'id'           => $value['id'],
                'num'          => $num,
                'amount'       => money($value['sell_price'] * $num),  //单个商品价格的总价
                'market_price' => money($value['market_price']),
                'sell_price'   => money($value['sell_price']),
                'weight'       => $value['weight'],
                'points'       => $value['points'],
                'repoints'     => $value['repoints'],
                'name'         => $value['name'],
                'img'          => $value['img'],
            ];
        }
        $amount = Fare::realAmount($data,$this->miniapp_id);
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
                $rel = model('Bank')->isPay($this->user->id,$point_fee,$config->payment_type_shop);
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
            $goodpay_tax = $order_amount*$config->goodpay_tax/100;
            $bank_rel = SystemMemberBank::moneyJudge($this->miniapp->member_id,$goodpay_tax);
            if($bank_rel){
                return ['code'=>0,'message'=>'官方帐号余额不足,请联系管理员'];
            }
        }
        $order_no = $this->user->invite_code.order_no(); //生成的订单号
        $order['payment_id']        = $payment['id']; //支付ID
        $order['express_name']      = $address['name'];
        $order['express_phone']     = $address['telphone'];
        $order['express_address']   = $address['address'];
        $order['order_amount']      = $amount['order_amount'];
        $order['real_amount']       = $amount['real_amount'];
        $order['real_freight']      = $amount['real_freight'];
        $order['order_no']          = $order_no;
        $order['member_miniapp_id'] = $this->miniapp_id;
        $order['user_id']           = $this->user->id;
        $order['order_starttime']   = time();
        $order_id = Shopping::insertGetId($order); 
        if(empty($order_id)){
            return enjson(403,'创建订单失败');
        }
        //保存订单产品到缓存数据表
        foreach ($data as $key => $value) {
            $item_data[$key]['order_no']  = $order_no;
            $item_data[$key]['item_id']   = $value['id'];
            $item_data[$key]['buy_price'] = $value['sell_price'];
            $item_data[$key]['buy_nums']  = $value['num'];
            $item_data[$key]['name']      = $value['name'];
            $item_data[$key]['img']       = $value['img'];
        }
        ShoppingCache::insertAll($item_data);
        //支付方式
        if($config->is_pay_types == 1){
            //云收银台
            $pay_coinfig = json_decode($payment->config);
            $ispay = [
                'name'       => $this->miniapp->appname.'购买商品',
                'mchid'      => $pay_coinfig->mch_id,
                'total_fee'  => $order_amount*100,
                'order_no'   => $order_no,
                'note'       => $this->miniapp_id,
                'notify_url' => $param['buytype'] == 'wepay'? api(3,'fastshop/goodpay/shop',$this->miniapp_id) : api(3,'fastshop/goodpay/shopPoint',$this->miniapp_id),
                'publickey'  => uuid(1)
            ];
            $paydata = $this->makeSign($ispay,$pay_coinfig->key);
        }else{
            //去请求微信支付接口
            $payparm = [
                'openid'     => $this->user->miniapp_uid,
                'miniapp_id' => $this->miniapp_id,
                'name'       => $this->miniapp->appname.'购买商品',
                'order_no'   => $order_no,
                'total_fee'  => $order_amount*100,
                'notify_url' => $param['buytype'] == 'wepay'? api(3,'fastshop/notify/shop',$this->miniapp_id) : api(3,'fastshop/notify/shopPoint',$this->miniapp_id),
            ];
            $ispay = WechatPay::orderPay($payparm);
            if($ispay['code'] == 0){
                return enjson(403,$ispay['msg']);
            }
            $paydata = $ispay['data'];
        }
        return enjson(200,'成功',['type' => $config->is_pay_types,'order' => $paydata]);
    }

 
    /**
     * 重新支付
     * @param string $no
     * @return void
     */
    public function retryPay(){
        $param = [
            'order_no' => Request::param('order_no'),
            'buytype'  => Request::param('buytype','wepay'),
            'sign'     => Request::param('sign')
        ];
        $rel = $this->apiSign($param);
        if($rel['code'] != 200){
            return enjson(403,'签名失败');
        }
        $order = Shopping::where(['order_no' => $param['order_no'],'member_miniapp_id' => $this->miniapp_id,'paid_at' => 0])->find();
        if(empty($order)){
            return enjson(403,'未找到当前订单信息');
        }
        //支付接口
        $payment = SystemMemberPayment::where(['apiname'=>'wepay','member_miniapp_id'=>$this->miniapp_id])->find();
        if(empty($payment)){
            return enjson(403,'未开通微信支付功能');
        }
        //支付方式
        $config = Config::where(['member_miniapp_id' => $this->miniapp_id])->find();
        if($param['buytype'] == 'point'){
            $config  = Config::where(['member_miniapp_id' => $this->miniapp_id])->find();
            if($config->payment_type_shop == 0){
                return json(['code'=>403,'msg'=>"未开通余额支付功能"]);
            }
            $point_fee = money($order->order_amount*($config->payment_point_shop/100));  //积分付款
            if ($point_fee <= 0) {
                $param['buytype'] = 'wepay'; //如果积分付款为零0转换为正常的微信全额支付
                $order_amount = money($order->order_amount);
            }else{
                $order_amount = money($order->order_amount - $point_fee);
                $order_amount = $order_amount <= 0 ? 1 : $order_amount; //如果是100%积分设置最低付款金额
                //判断积分够不够
                $rel = model('Bank')->isPay($this->user->id,$point_fee,$config->payment_type_shop);
                if(!$rel){
                    return json(['code'=>403,'msg'=>"余额不足,请选择其它支付渠道"]);
                }
            }
        }else{
            $order_amount = $order->order_amount; 
        }
        $order_amount  = $order_amount <= 0 ? 1 :$order_amount;
        if($config->is_pay_types == 1){
            if($config->goodpay_tax > 0){
                $goodpay_tax = $order_amount*$config->goodpay_tax/100;
                $bank_rel = SystemMemberBank::moneyJudge($this->miniapp->member_id,$goodpay_tax);
                if($bank_rel){
                    return ['code'=>0,'message'=>'官方帐号余额不足,请联系管理员'];
                }
            }
            $pay_coinfig = json_decode($payment->config);
            //云收银台
            $ispay = [
                'name'       => $this->miniapp->appname.'购买商品',
                'mchid'      => $pay_coinfig->mch_id,
                'total_fee'  => $order_amount*100,
                'order_no'   => $order->order_no,
                'note'       => $this->miniapp_id,
                'notify_url' => $param['buytype'] == 'wepay'? api(3,'fastshop/goodpay/shop',$this->miniapp_id) : api(3,'fastshop/goodpay/shopPoint',$this->miniapp_id),
                'publickey'  => uuid(1)
            ];
            $paydata = $this->makeSign($ispay,$pay_coinfig->key);
        }else{
            //去请求微信支付接口
            $payparm = [
                'openid'     => $this->user->miniapp_uid,
                'miniapp_id' => $this->miniapp_id,
                'name'       => $this->miniapp->appname.'购买商品',
                'order_no'   => $order->order_no,
                'total_fee'  => $order_amount*100,
                'notify_url' => api(3,'fastshop/notify/shop',$this->miniapp_id),
                'notify_url' => $param['buytype'] == 'wepay'? api(3,'fastshop/notify/shop',$this->miniapp_id) : api(3,'fastshop/notify/shopPoint',$this->miniapp_id),
            ];
            $ispay = WechatPay::orderPay($payparm);
            if($ispay['code'] == 0){
                return enjson(403,$ispay['msg']);
            }
            $paydata = $ispay['data'];
        }
        return enjson(200,'成功',['type' => $config->is_pay_types,'order' => $paydata]);
    }
  
    /**
     * 关闭订单
     */
    public function closeorder(){
        $rule = [
            'order_no' => Request::param('order_no'),
            'sign'     => Request::param('sign')
        ];
        $rel = $this->apiSign($rule);
        if($rel['code'] == 200){
            $condition['order_no'] = $rule['order_no'];
            $condition['user_id']  = $this->user->id;
            $condition['is_del']   = 0;
            $rel = Shopping::where($condition)->update(['is_del' => 1]);
            if(empty($rel)){
                return enjson(403,'关闭失败');
            }
            return enjson(200,'成功关闭');
        }
        return enjson(204,'签名失败');

    }

    /**
     * 签收订单
     */
    public function signOrder($order_no){
        $rule = [
            'order_no' => Request::param('order_no'),
            'sign'     => Request::param('sign')
        ];
        $rel = $this->apiSign($rule);
        if($rel['code'] == 200){
            $condition['order_no']       = Filter::filter_escape($order_no);
            $condition['user_id']        = $this->user->id;
            $condition['is_del']         = 0;
            $condition['paid_at']        = 1;
            $condition['express_status'] = 1;
            $rel = Shopping::where($condition)->update(['status' => 1]);
            if(empty($rel)){
                return enjson(403,'未找到当前订单');
            }
            return enjson(200,'订单签收成功');
        }
        return enjson(204,'签名失败');
    } 

    /**
     * ######################################
     * 小程序端用户的订单列表
     * 我的产品列表
     */
    public function order(){
        $rule = [
            'types' => Request::param('types',0),
            'page'  => Request::param('page',0),
            'sign'  => Request::param('sign')
        ];
        $rel = $this->apiSign($rule);
        if($rel['code'] != 200){
            return enjson(403,'签名失败');
        }
        $condition['user_id'] = $this->user->id;
        $condition['is_del']  = 0;
        switch ($rule['types']) {
            case 1:
                $condition['paid_at'] = 1;
                $condition['express_status'] = 0;
                break;
            case 2:
                $condition['paid_at']        = 1;
                $condition['express_status'] = 1;
                $condition['status']         = 0;
                break;
            case 3:
                $condition['paid_at'] = 1;
                $condition['status']  = 1;
                break;
            default:
                $condition['paid_at'] = 0;
                break;
        }
        $order = Shopping::where($condition)->order('id desc')->paginate(10);
        if(empty($order)) {
            return enjson(204,'没有订单');
        }
        $data = Shopping::orderData($order);
        return enjson(200,'成功',$data);
    }


    /**
     * 读取购物车预览
     */
    public function review(){
        $param = [
            'order_no' => Request::param('order_no'),
            'sign'     => Request::param('sign')
        ];
        $rel = $this->apiSign($param);
        if($rel['code'] != 200){
            return enjson(403,$rel['msg']);
        }
        $condition['user_id']    = $this->user->id;
        $condition['is_del']     = 0;
        $condition['order_no']   = $param['order_no'];
        $order = Shopping::where($condition)->order('id desc')->select();
        if(empty($order)) {
            return enjson(204,'没有订单');
        }
        return enjson(200,'成功',Shopping::orderData($order));
    }
}