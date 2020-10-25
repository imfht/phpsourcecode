<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 商城购物车
 */
namespace app\popupshop\controller\api\v1;
use app\popupshop\controller\api\Base;
use app\popupshop\model\Item;
use app\popupshop\model\Fare;
use app\popupshop\model\Order;
use app\popupshop\model\OrderCache;
use app\common\facade\WechatPay;
use app\common\model\SystemMemberPayment;
use app\common\model\SystemUserAddress;
use think\facade\Request;
use filter\Filter;

class Cart extends Base{

    public function initialize() {
        parent::initialize();
        if(!$this->user){
            exit(json_encode(['code'=>401,'msg'=>'用户认证失败']));
        }
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
        $order = Order::where(['order_no' => $order_no,'paid_at' => 0])->field('id,order_no,paid_no,order_amount')->find();
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
        $rule = [
            'address' => Request::param('address/d',0),
            'ids'     => Request::param('ids/s','','htmlspecialchars_decode'),
            'ucode'   => Request::param('ucode'),
            'sign'    => Request::param('sign')
        ];
        $validate = $this->validate($rule,'Cart.add_order');
        if(true !== $validate){
            return enjson(403,$validate);
        }
        $rel = $this->apiSign($rule);
        if($rel['code'] != 200){
            return enjson(403,'签名失败');
        }
        $cart = (array)ids(json_decode($rule['ids'],true),true); //购买的产品ID和数量
        $ids  = ids(array_keys($cart),true);  //购买的产品ID
        if (empty($ids)){
            return enjson(403,'购物车是空的');
        }
        //读取发货地址
        $address = SystemUserAddress::where(['user_id'=>$this->user->id,'id' =>$rule['address']])->find();
        if(empty($address)){
            return enjson(403,'请选择收货地址');
        }
        //支付接口
        $payment = SystemMemberPayment::where(['apiname'=>'wepay','member_miniapp_id'=>$this->miniapp_id])->field('id')->find();
        if(empty($payment)){
            return enjson(403,'未开通微信支付功能');
        }
        //读取订单 
        $item = Item::where(['is_sale'=>2,'id' => $ids,'member_miniapp_id' => $this->miniapp_id])
                ->field('id,sell_price,market_price,points,repoints,weight,name,img')
                ->select();
        if(empty($item)){
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
                 'img'         => $value['img'],
            ];
        }
        $order_no = $this->user->invite_code.order_no(); //生成的订单号
        $amount = Fare::realAmount($data,$this->miniapp_id);
        //创建订单
        $order['payment_id']        = $payment['id'];    //支付ID
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
        $order_id = Order::insertGetId($order); 
        if(empty($order_id)){
            return enjson(403,'创建订单失败');
        }
        //保存订单产品到缓存数据表
        foreach ($data as $key => $value) {
            $item_data[$key]['order_id']  = $order_id ;
            $item_data[$key]['order_no']  = $order_no;
            $item_data[$key]['item_id']   = $value['id'];
            $item_data[$key]['buy_price'] = $value['sell_price'];
            $item_data[$key]['buy_nums']  = $value['num'];
            $item_data[$key]['name']      = $value['name'];
            $item_data[$key]['img']       = $value['img'];
        }
        OrderCache::insertAll($item_data);
        //去请求微信支付接口
        $payparm = [
            'openid'     => $this->user->miniapp_uid,
            'miniapp_id' => $this->miniapp_id,
            'name'       => $this->miniapp->appname.'购买商品',
            'order_no'   => $order_no,
            'total_fee'  => $amount['order_amount']*100,
            'notify_url' => api(1,'popupshop/notify/shop',$this->miniapp_id),
        ];
        $ispay = WechatPay::orderPay($payparm);
        if($ispay['code'] == 0){
            return enjson(403,$ispay['msg']);
        }
        return enjson(200,'成功',$ispay['data']);
    }

 
    /**
     * 重新支付
     * @param string $no
     * @return void
     */
    public function retrypay(){
        $rule = [
            'order_no' => Request::param('order_no'),
            'sign'     => Request::param('sign')
        ];
        $rel = $this->apiSign($rule);
        if($rel['code'] == 200){
            $order = Order::where(['order_no' => $rule['order_no'],'member_miniapp_id' => $this->miniapp_id,'paid_at' => 0])->find();
            if(empty($order)){
                return enjson(403,'未找到当前订单信息');
            }
            $payorder = [
                'openid'     => $this->user->miniapp_uid,
                'miniapp_id' => $this->miniapp_id,
                'name'       => $this->miniapp->appname.'购买商品',
                'order_no'   => $order->order_no,
                'total_fee'  => $order->order_amount*100,
                'notify_url' => api(1,'popupshop/notify/shop',$this->miniapp_id),
            ];
            $ispay = WechatPay::orderPay($payorder);
            if($ispay['code'] == 0){
                return enjson(403,$ispay['msg']);
            }
            return enjson(200,'成功',$ispay['data']);
        }
        return enjson(204,'签名失败');
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
            $rel = Order::where($condition)->update(['is_del' => 1]);
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
            $rel = Order::where($condition)->update(['status' => 1]);
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
        if($rel['code'] == 200){
            $order = Order::getUserOrderList($this->user->id,$rule['types']);
            if(empty($order)) {
                return enjson(204,'没有订单');
            }
            return enjson(200,'成功',Order::order_data($order));
        }
        return enjson(204,$rel['msg']);
    }

    /**
     * 读取购物车预览
     */
    public function review(){
        $rule = [
            'order_no' => Request::param('order_no',0),
            'sign'     => Request::param('sign')
        ];
        $rel = $this->apiSign($rule);
        if($rel['code'] == 200){
            $order = Order::getOrder($rule['order_no'],$this->user->id);
            if(empty($order)) {
                return enjson(204,'没有订单');
            }
            return enjson(200,'成功',Order::order_data($order));
        }
        return enjson(204,$rel['msg']);
    }
}