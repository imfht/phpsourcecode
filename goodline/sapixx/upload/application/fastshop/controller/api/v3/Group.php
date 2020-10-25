<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 商城小程序公共API服务
 */
namespace app\fastshop\controller\api\v3;
use app\fastshop\controller\api\Base;
use app\fastshop\model\Group as AppGroup;
use app\fastshop\model\Fare;
use app\fastshop\model\Config;
use app\fastshop\model\Shopping;
use app\fastshop\model\ShoppingCache;
use app\common\facade\WechatPay;
use app\common\model\SystemMemberPayment;
use app\common\model\SystemUserAddress;
use app\common\model\SystemMemberBank;

class Group extends Base{

    /**
     * 读取团购产品
     */
    public function index(){
        $param = [
            'page'  => $this->request->param('page/d'),
            'sign'  => $this->request->param('sign')
        ];
        $rel = $this->apiSign($param);
        if($rel['code'] != 200){
            return enjson(403,'签名失败');
        }
        $list = AppGroup::where(['member_miniapp_id'=>$this->miniapp_id])->order('id desc')->paginate(5,true);
        if(empty($list)){
            return enjson(204);
        }
        $data = [];
        foreach ($list as $key => $value) {
            $data[$key]['id']         = $value->id;
            $data[$key]['img']        = $value->item->img;
            $data[$key]['name']       = $value->item->name;
            $data[$key]['amount']     = $value->amount;
            $data[$key]['hao_people'] = $value->hao_people;
            $data[$key]['uids']       = count(json_decode($value->uids,true));
        }
        return enjson(200,'成功',$data);
    }

    /**
     * 关注的商家店铺商品
     */
    public function item(){
        $param = [
            'id'     => $this->request->param('id/d'),
            'sign'  => $this->request->param('sign')
        ];
        $rel = $this->apiSign($param);
        if($rel['code'] != 200){
            return enjson(403,'签名失败');
        }
        $rel = AppGroup::where(['member_miniapp_id'=>$this->miniapp_id])->where(['id' => $param['id']])->find();
        $data  = [];
        if(!empty($rel)){
            $data['face'] = [];
            if(!empty($rel->uids)){
                $data['face'] = model('SystemUser')->where(['id' => json_decode($rel->uids,true)])->field('face')->select()->toArray();
            }
            $data['group_price'] = money($rel->amount);
            $data['sale_price']  = money($rel->item->sell_price);
            $data['hao_people'] = $rel->hao_people;
            $data['img']        = $rel->item->img."?x-oss-process=style/500";
            $data['imgs']       = empty($rel->item->imgs) ? [] : json_decode($rel->item->imgs,true);
            $data['name']       = $rel->item->name;
            $data['content']    = $rel->item->content;
            return json(['code'=>200,'msg'=>'成功','data' => $data]);
        }
    }

    /**
     * 下单购买关注好店的商品
     * @return void
     */
    public function cartItem(){
        $this->isUserAuth();
        if(request()->isPost()){
            $param = [
                'cart' => $this->request->param('cart/d',0),
                'sign'    => $this->request->param('sign')
            ];
            $rel = $this->apiSign($param);
            if($rel['code'] != 200){
                return enjson(403,'签名失败');
            }
            if(empty($param['cart'])){
                return json(['code'=>204,'msg'=>'购物车中没有宝贝','url'=>'/pages/store/like']);
            }
            $rel = AppGroup::where(['id' => $param['cart']])->find();
            if(empty($rel)){
                return json(['code'=>403,'msg'=>'活动已下架']);
            }
            $item['name']        = $rel->item->name;
            $item['img']         = $rel->item->img;
            $item['sale_price']  = money($rel->amount);
            $amount = Fare::realAmount(['amount' =>money($rel->amount),'weight' => $rel->item->weight],$this->miniapp_id);
            if(empty($rel)){
                return json(['code'=>204,'msg'=>'没有内容了','url'=>'/pages/store/like']);
            }else{
                return json(['data'=>['item'=>$item,'amount' => $amount],'code'=>200,'msg'=>'成功']);
            }
        }
    }


    /**
     * 微信商城支付
     * @param string $no
     * @return void
     */
    public function doPay(){
        $param = [
            'address' => $this->request->param('address/d',0),
            'ids'     => $this->request->param('ids/d'),
            'ucode'   => $this->request->param('ucode'),
            'buytype' => $this->request->param('buytype','wepay'),
            'sign'    => $this->request->param('sign')
        ];
        $rel = $this->apiSign($param);
        if($rel['code'] != 200){
            return enjson(403,'签名失败');
        }
        $validate = $this->validate($param,'Cart.add_order');
        if(true !== $validate){
            return enjson(403,$validate);
        }
        if (empty($param['ids'])){
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
        $group = AppGroup::where(['id' => $param['ids']])->find();
        if(empty($group)){
            return json(['code'=>403,'msg'=>'团购商品已下架']);
        }     
        $order_no = $this->user->invite_code.order_no(); //生成的订单号
        $amount = Fare::realAmount([['amount' => $group->amount,'weight' => $group->item->weight,'num' =>1]],$this->miniapp_id);
        $config = Config::where(['member_miniapp_id' => $this->miniapp_id])->find();
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
            $goodpay_tax = $param['money']*$config->goodpay_tax/100;
            $bank_rel = SystemMemberBank::moneyJudge($this->miniapp->member_id,$goodpay_tax);
            if($bank_rel){
                return ['code'=>0,'message'=>'官方帐号余额不足,请联系管理员'];
            }
        }
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
        $order_id = Shopping::insert($order); 
        if(empty($order_id)){
            return enjson(403,'创建订单失败');
        }
        //保存订单产品到缓存数据表
        $item_data['order_no']  = $order_no;
        $item_data['item_id']   = $group->item->id;
        $item_data['buy_price'] = $amount['order_amount'];
        $item_data['buy_nums']  = 1;
        $item_data['name']      = $group->item->name;
        $item_data['img']       = $group->item->img;
        ShoppingCache::insert($item_data);
        //支付方式
        $config = Config::where(['member_miniapp_id' => $this->miniapp_id])->find();
        if($config->is_pay_types == 1){
            $pay_coinfig = json_decode($payment->config);
            //云收银台
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
}