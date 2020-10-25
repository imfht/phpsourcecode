<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 商城小程序公共API服务
 */
namespace app\fastshop\controller\api\v3;
use app\fastshop\controller\api\Base;
use app\common\facade\WechatPay;
use app\common\model\SystemUserAddress;
use app\common\model\SystemMemberPayment;
use app\common\model\SystemUserLevel;
use app\common\model\SystemMemberBank;
use app\fastshop\model\Sale;
use app\fastshop\model\Order as AppOrder;
use app\fastshop\model\OrderCache;
use app\fastshop\model\Vip;
use app\fastshop\model\Entrust;
use app\fastshop\model\EntrustList;
use app\fastshop\model\Item;
use app\fastshop\model\Store;
use app\fastshop\model\Config;
use app\fastshop\model\Fare;
use util\Util;

class Order extends Base{

    public function initialize() {
        parent::initialize();
        $this->isUserAuth();
    }

 
    /**
     * 查看是否有订单
     * @param integer 读取ID
     * @return json
     */
    public function isOrder(){
        $param['id']   = $this->request->param('id/d',0);
        $param['sign'] = $this->request->param('sign');
        $rel = $this->apiSign($param);
        if($rel['code'] != 200){
            return enjson(204,'签名失败');
        }
        $where['id']    = $param['id'];
        $where['types'] = 1;
        $sale = Sale::where($where)->find();
        if (empty($sale)) {
            return json(['code'=>403,'msg'=>'活动已下架']);
        }
        if($sale->types == 0){
            return json(['code'=>403,'msg'=>'活动已下架']);
        }
        if($sale->sale_nums <= 0){
            return json(['code'=>403,'msg'=>'请下次抢购再来']);
        }
        if($sale->start_time >= time() || time() >= $sale->end_time){
            return json(['code'=>403,'msg'=>'抢购未开始']);
        }
        return json(['code'=>200,'msg'=>'成功']);
    }

    /**
     * 读取购物车产品列表
     */
    public function cartItem(){
        if(request()->isPost()){
            $param['cart'] = $this->request->param('cart',0);
            $param['sign'] = $this->request->param('sign');
            $rel = $this->apiSign($param);
            if($rel['code'] != 200){
                return enjson(204,'签名失败');
            }
            if(empty($param['cart'])){
                return json(['code'=>204,'msg'=>'购物车中没有宝贝','url'=>'/pages/market/index']);
            }
            $rel = Sale::where(['member_miniapp_id' => $this->miniapp_id,'id' => $param['cart']])->find();
            if(empty($rel)){
                return json(['code'=>403,'msg'=>'活动已下架']);
            }
            if($rel['sale_nums'] <= 0){
                return json(['code'=>403,'msg'=>'活动宝贝已抢完了']);
            }
            $rel['start_time']    = date('Y-m-d',$rel->start_time);
            $rel['end_time']      = date('Y-m-d',$rel->end_time);
            $rel['market_price']  = money($rel->market_price/100);
            $rel['sale_price']    = money($rel->sale_price/100);
            $rel['gift']          = AppOrder::gift(json_decode($rel->gift,true));
            //计算产品价格
            $price = [];
            foreach ($rel['gift'] as $key => $value) {
                $price[$key] = ['amount' => 0,'weight' =>$value['weight'],'num' =>1];
            }
            $item_price = [['amount' => $rel['sale_price'],'weight' =>$rel->item->weight,'num' =>1]];
            $price      = array_merge($item_price,$price);
            $amount = Fare::realAmount($price,$this->miniapp_id); 
            if(empty($rel)){
                return json(['code'=>204,'msg'=>'没有内容了','url'=>'/pages/market/index']);
            }else{
                return json(['data'=>['item'=>$rel,'amount' => $amount],'code'=>200,'msg'=>'成功']);
            }
        }
    }

    /**
     * 发起支付并通知支付接口
     * @param string $no
     * @return void
     */
    public function doPay(){
        if (request()->isPost()) {
            $param['address'] = $this->request->param('address/d',0);
            $param['ids']     = $this->request->param('ids/d',0);
            $param['ucode']   = $this->request->param('ucode');
            $param['buytype'] = $this->request->param('buytype','wepay');
            $param['sign']    = $this->request->param('sign');
            $rel = $this->apiSign($param);
            if($rel['code'] != 200){
                return enjson(403,'参数有误');
            }
            $validate = $this->validate($param, 'Cart.add_order');
            if (true !== $validate) {
                return enjson(403,$validate);
            }
            //判断提交商品(是否购买过)
            $sale = model('Sale')->where(['id' => $param['ids']])->find();  //价格是分
            if (empty($sale)) {
                return enjson(403,"活动不存在");
            }
            if ($sale->sale_nums <= 0) {
                return enjson(403,"商品库存不足");
            }
            if ($sale->types == 0) {
                return enjson(403,"活动已下架");
            }
            if ($sale->start_time >= time() || time() >= $sale->end_time) {
                return enjson(403,"活动暂未开始");
            }
            //读取配置
            $config  = Config::where(['member_miniapp_id' => $this->miniapp_id])->find();
            if($param['buytype'] == 'point' && $config->payment_type == 0){
                return json(['code'=>403,'msg'=>"未开通余额支付功能"]);
            }
            //增加下单次数限制
            if($config->num_referee_people > 0){
                //下单数量
                $entrust_list = EntrustList::where(['member_miniapp_id' => $this->miniapp_id,'user_id' => $this->user->id,'is_rebate' => 0])->field('user_id,order_no,is_rebate')->select();
                $entrust_num  = count(Util::unique_array($entrust_list->toArray()));
                //统计推荐人数
                $peple_num = SystemUserLevel::where(['parent_id' => $this->user->id,'level'=>1])->count();
                if($peple_num <= 0){
                    return enjson(403,"最少邀请['.$config->num_referee_people.']朋友才允许抢购");
                }else{
                    $num = intval($peple_num/$config->num_referee_people);
                    if($num){
                        if($entrust_num >= $num){
                            $people_num = $config->num_referee_people*($num+1)-$peple_num;
                            return enjson(403,"还有[{$entrust_num}]单未成交,再邀请[".$people_num."]个朋友,就可以增加一单");
                        }
                    }else{
                        $people_num = $config->num_referee_people-$peple_num;
                        return enjson(403,"已邀[{$peple_num}]人,再邀请[".$people_num."]个朋友,就可以抢购");
                    }
                }
            }
            //判断下单的是否会员
            $vip = Vip::where(['user_id' => $this->user->id,'state' => 1])->find();
            if ($config['shop_types'] == 0) {
                if ($sale->is_vip && empty($vip)) {
                    return enjson(302,"本活动仅会员可参与,请先开通会员",['url' =>'/pages/user/vip']);
                }
            } else {
                if (empty($vip)) {
                    return enjson(302,"仅会员可参与,请先开通会员",['url' =>'/pages/user/vip']);
                }
            }
            //判断订单
            $condition['member_miniapp_id'] = $this->miniapp_id;
            $condition['user_id']           = $this->user->id;
            $condition['is_del']            = 0;
            $condition['paid_at']           = 1;
            if($config->old_users  > 0){
                $order_num = AppOrder::where($condition)->count();
                if ($sale->is_newuser == 1) {
                    if ($order_num >= $config->old_users) {
                        return enjson(403,"活动只允许新用户抢购");
                    }
                }
            }
            if($config->day_ordernum > 0){
                $today = date('Y-m-d');
                $today_num = AppOrder::where($condition)->whereBetweenTime('order_starttime',$today)->count();
                if ($today_num >= $config->day_ordernum) {
                    return enjson(403,"今天已没有抢购机会,请明天再来");
                }
            }
            if($config->sale_ordernum > 0){
                $condition['sale_id'] = $sale->id;
                $orderNum = AppOrder::where($condition)->count();
                if ($orderNum >= $config->sale_ordernum) {
                    return enjson(403,"每个活动最多只允许抢购{$config->sale_ordernum}次");
                }
            }
            //读取发货地址
            $address = SystemUserAddress::where(['user_id'=>$this->user->id,'id' => $this->request->param('address/d',0)])->find();
            if (empty($address)) {
                return enjson(403,'请重新选择收货地址');
            }
            //支付接口
            $payment = SystemMemberPayment::where(['apiname' => 'wepay','member_miniapp_id'=>$this->miniapp_id])->find();
            if (empty($payment)) {
                return enjson(403,'未开通微信支付功能');
            }
            //计算商品价格(从上面的分换算成 元)
            $item = Item::field('weight,img,name')->where(['id' => $sale->item_id])->find();
            //计算产品价格(包含主商品和赠品的重量计算)
            $price = [];
            $gift = AppOrder::gift(json_decode($sale->gift,true));
            foreach ($gift as $key => $value) {
                $price[$key] = ['amount' => 0,'weight' =>$value['weight'],'num' =>1];
            }
            $item_price = [['amount' => money($sale->sale_price/100),'weight' =>$item->weight,'num' =>1]];
            $price      = array_merge($item_price,$price);
            $amount     = Fare::realAmount($price,$this->miniapp_id);
            if($param['buytype'] == 'point'){
                if($config->payment_type == 0){
                    return enjson(403,'未开通余额支付功能');
                }
                $point_fee = money($amount['order_amount']*($config->payment_point/100));  //积分付款
                if ($point_fee <= 0) {
                    $param['buytype'] = 'wepay'; //如果积分付款为零0转换为正常的微信全额支付
                    $order_amount = money($amount['order_amount']);
                }else{
                    $order_amount = money($amount['order_amount'] - $point_fee);
                    $order_amount = $order_amount <= 0 ? 1 : $order_amount; //如果是100%积分设置最低付款金额
                    //判断积分够不够
                    $rel = model('Bank')->isPay($this->user->id,$point_fee,$config->payment_type);
                    if(!$rel){
                        return enjson(403,'余额不足,请选择其它支付渠道');
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
                    return enjson(403,'账户余额不足,暂停交易');
                }
            }
            $order_no      = $this->user->invite_code.order_no(); //生成的订单号
            //计算最后付款金额
            $order['order_amount']      = $order_amount;          //元
            $order['real_amount']       = $amount['real_amount']; //元
            $order['real_freight']      = $amount['real_freight']; //元
            $order['payment_id']        = $payment->id;    //支付ID
            $order['express_name']      = $address->name;
            $order['express_phone']     = $address->telphone;
            $order['express_address']   = $address->address;
            $order['sale_id']           = $sale->id;
            $order['is_fusion']         = $sale->is_fusion;
            $order['member_miniapp_id'] = $this->miniapp_id;
            $order['user_id']           = $this->user->id;
            $order['order_no']          = $order_no;
            $order['paid_at']           = 0;
            $order['order_starttime']   = time();
            $rel = AppOrder::insert($order);
            if (empty($rel)) {
                return enjson(403,'创建订单失败');
            }
            //保存订单产品到缓存数据表
            $item_data['order_no']   = $order_no;
            $item_data['item_id']    = $sale->item_id;
            $item_data['sale_price'] = $sale->sale_price;//分
            $item_data['name']       = $sale->title;
            $item_data['img']        = $item->img;
            $item_data['gift']       = $sale->gift;
            OrderCache::insert($item_data);
            //支付方式
            if($config->is_pay_types == 1){
                $pay_coinfig = json_decode($payment->config);
                //云收银台
                $ispay = [
                    'name'       => $sale->title,
                    'mchid'      => json_decode($payment->config)->mch_id,
                    'order_no'   => $order_no,
                    'note'       => $this->miniapp_id,
                    'total_fee'  => $order_amount*100,
                    'notify_url' => $param['buytype'] == 'wepay'? api(3,'fastshop/goodpay/sale',$this->miniapp_id):api(3,'fastshop/goodpay/salePoint',$this->miniapp_id),
                    'publickey'  => uuid(1)
                ];
                $paydata = $this->makeSign($ispay,$pay_coinfig->key);
            }else{
                //去请求微信支付接口
                $payparm = [
                    'name'       => $sale->title,
                    'openid'     => $this->user->miniapp_uid,
                    'miniapp_id' => $this->miniapp_id,
                    'order_no'   => $order_no,
                    'total_fee'  => $order_amount*100,
                    'notify_url' => $param['buytype'] == 'wepay'? api(3,'fastshop/notify/sale',$this->miniapp_id) : api(3,'fastshop/notify/salePoint',$this->miniapp_id),
                ];
                $ispay = WechatPay::orderPay($payparm);
                if($ispay['code'] == 0){
                    return enjson(403,$ispay['msg']);
                }
                $paydata = $ispay['data'];
            }
            return enjson(200,'成功',['type' =>$config->is_pay_types,'order' =>$paydata]);
        }
    }

    /**
     * 我的寄卖
     * @return void
     */
    public function gift(){
        $rule = [
            'types' => $this->request->param('types',0),
            'page'  => $this->request->param('page',0),
            'sign'  => $this->request->param('sign')
        ];
        $rel = $this->apiSign($rule);
        if($rel['code'] != 200){
            return enjson(403,'签名失败');
        }
        $condition['user_id'] = $this->user->id;
        $condition['is_del']  = 0;
        switch ($rule['types']) {
            case 1:
                $condition['paid_at']    = 1;
                $condition['is_entrust'] = 1;
                $condition['express_status'] = 0;
                break;
            case 2:
                $condition['paid_at']        = 1;
                $condition['express_status'] = 1;
                $condition['is_entrust']     = 1;
                break;
            default:
                $condition['paid_at']    = 1;
                $condition['is_entrust'] = 0;
                break;
        }
        $order = AppOrder::where($condition)->order('id desc')->paginate(10);
        if(empty($order)) {
            return enjson(204,'没有订单');
        }
        $data = [];
        foreach ($order as $key => $value) {
            $data[$key] = $value;
            $data[$key]['status_text']     = AppOrder::statuText($value->status,$value->paid_at,$value->is_entrust,$value->express_status);
            $data[$key]['order_starttime'] = empty($value->order_starttime) ? '' : date('Y-m-d H:i:s',$value->order_starttime);
            $data[$key]['order_endtime']   = empty($value->order_endtime) ? '' : date('Y-m-d H:i',$value->order_endtime);
            $data[$key]['item']            = $value->orderItem;
            $gift_data = [];            
            $gift = array_column(json_decode($value->orderItem->gift,true),'item_id');
            foreach ($gift as $k => $item_id) {
                $items  = Item::field('id,name,img')->where(['id' => $item_id])->find();
                $gift_data[$k]['name'] = $items->name;
                $gift_data[$k]['img'] = $items->img."?x-oss-process = style/auto";
            }
            $data[$key]['gift']=  $gift_data; 
        }
        return enjson(200,'成功',$data);
    }

    /**
     * 读取购物车预览
     */
    public function review(){
        $param['order_no']   = $this->request->param('order_no');
        $param['sign']       = $this->request->param('sign');
        $rel = $this->apiSign($param);
        if($rel['code'] != 200){
            return enjson(204,'签名失败');
        }
        $condition['order_no'] = $param['order_no'];
        $condition['user_id']  = $this->user->id;
        $order = AppOrder::where($condition)->find();
        if(empty($order)){
            return enjson(204);
        }
        $data = $order;
        $data['status_text']     = AppOrder::statuText($order->status,$order->paid_at,$order->is_entrust,$order->express_status);
        $data['order_starttime'] = empty($order->order_starttime) ? '' : date('Y-m-d H:i:s',$order->order_starttime);
        $data['order_endtime']   = empty($order->order_endtime) ? '' : date('Y-m-d H:i',$order->order_endtime);
        $data['item']            = $order->orderItem;
        $gift = array_column(json_decode($order->orderItem->gift,true),'item_id');
        $entrust_state           = json_decode($order->orderItem->entrust);
        $gift_data               = [];
        foreach ($gift as $k => $item_id) {
            $items  = Item::field('id,name,img')->where(['id' => $item_id])->find();
            $gift_data[$k]['id']   = $items->id;
            $gift_data[$k]['name'] = $items->name;
            $gift_data[$k]['img']  = $items->img."?x-oss-process = style/auto";
            $gift_data[$k]['entrust_state'] = empty($entrust_state[$k]) ? false : $entrust_state[$k];
        }
        $data['gift'] = $gift_data;
        return enjson(200,'成功',$data);
    }

    /**
     * 签收订单
     */
    public function signorder(){
        $param['order_no'] = $this->request->param('order_no');
        $param['sign']     = $this->request->param('sign');
        $rel = $this->apiSign($param);
        if($rel['code'] != 200){
            return enjson(204,'签名失败');
        }
        $condition['order_no']       = $param['order_no'];
        $condition['user_id']        = $this->user->id;
        $condition['is_del']         = 0;
        $condition['paid_at']        = 1;
        $condition['express_status'] = 1;
        $rel = AppOrder::where($condition)->data(['status' => 1])->update();
        if(empty($rel)){
            return json(['code'=>403,'msg'=>'未找到当前订单']);
        }
        return json(['code'=>200,'msg'=>'订单签收成功']);
    }


    
   /**
     * 确定是否寄卖商品
     */
    public function giftAction(){
        $param['order_no']      = $this->request->param('order_no');
        $param['service']       = $this->request->param('service',0);
        $param['item_checkbox'] = $this->request->param('item_checkbox',0);
        $param['gift']          = $this->request->param('gift');
        $param['sign']          = $this->request->param('sign');
        $rel = $this->apiSign($param);
        if($rel['code'] != 200){
            return enjson(204,'签名失败');
        }
        if (!$param['service']) {
            return enjson(403,'必须接受用户服务协议');
        }
        $item_checkbox = $param['item_checkbox'];
        $is_gift       = (array)ids(json_decode($param['gift']),true);
        if(empty($is_gift)){
            return enjson(403,'委托商品选择异常');
        }
        $store = Store::where(['uid' => $this->user->id])->find();
        if(empty($store)){
            return json(['code'=>302,'msg'=>'未开通小店,请先开通小店','url'=>'/pages/store/index']);
        }
        //确认提货
        $condition['order_no']       = $param['order_no'];
        $condition['user_id']        = $this->user->id;
        $condition['paid_at']        = 1;
        $condition['is_del']         = 0;
        $condition['is_entrust']     = 0;
        $rel = AppOrder::where($condition)->find();
        if (empty($rel)) {
            return json(['code'=>403,'msg'=>'订单未付款']);
        }
        //读取配置
        $config  = Config::where(['member_miniapp_id' => $this->miniapp_id])->field('lock_sale_day,amountlimit')->find();
        //读取当前用户的收益(是否允许委托)
        if($config->amountlimit){
            $bank = model('Bank')->where(['user_id' => $this->user->id])->field('profit')->find();
            if(!empty($bank)){
                $profit = intval($bank->profit/100);
                if($profit >= $config->amountlimit){
                    $amountlimit = false;
                    if($rel->is_fusion && $item_checkbox){
                        $amountlimit = true;
                    }else{
                        if(in_array(1,$is_gift)){
                            $amountlimit = true;
                        }
                    }
                    if($amountlimit){
                        return json(['code'=>403,'msg'=>'收益已达上限'.$config->amountlimit.'禁止委托,本单必须提货.']);
                    }else{
                        model('Bank')->isProfit($this->user->id,0); //清空收益
                    }
                }
            }
        }
        $is_under = $config->lock_sale_day > 0 ? 1 : 0;  //是否立即委托销售
        $fusion_state = 0;
        //读取委托商品(并计算寄卖数量)
        $info = OrderCache::where(['order_no' => $rel->order_no])->find();
        $gift = AppOrder::gift(json_decode($info['gift'],true));  //分已转化成元
        $entrust_data = [];
        $entrust_state = [];
        if($rel->is_fusion){
            foreach ($gift as $key => $value) {
                $entrust_state[$key] = false;
            }
            if($item_checkbox){
                //增加通一个商品的委托库存
                $entrust = Entrust::where(['member_miniapp_id'=>$this->miniapp_id,'item_id' => $rel->sale->item_id])->find();
                if (empty($entrust)) {
                    $data['item_id']           = $rel->sale->item_id;
                    $data['gite_count']        = 1;
                    $data['entrust_price']     = $rel->sale->sale_price; //分
                    $data['member_miniapp_id'] = $this->miniapp_id;
                    $entrust_id = Entrust::insertGetId($data);
                } else {
                    Entrust::where(['member_miniapp_id'=>$this->miniapp_id,'item_id' => $rel->sale->item_id])->setInc('gite_count',1);
                    $entrust_id = $entrust->id;
                }
                $fusion_state = 1;
                //增加托买数据
                $entrust_data[] = [
                    'member_miniapp_id' => $this->miniapp_id,
                    'order_no'          => $rel->order_no,
                    'entrust_id'        => $entrust_id,
                    'user_id'           => $this->user->id,
                    'order_amount'      => $rel->order_amount*100, //元=>分
                    'entrust_price'     => $rel->sale->sale_price,   //分
                    'item_id'           => $rel->sale->item_id,
                    'is_under'          => $is_under,
                    'create_time'       => time(),
                    'is_fusion'         => 1
                ];
            }
        }else{
            foreach ($gift as $key => $value) {
                if ($is_gift[$key] == 1) {  //委托
                    //增加通一个商品的委托库存
                    $entrust = Entrust::where(['member_miniapp_id'=>$this->miniapp_id,'item_id' => $value['item_id']])->find();
                    if (empty($entrust)) {
                        $data['item_id']           = $value['item_id'];
                        $data['gite_count']        = 1;
                        $data['entrust_price']     = $value['sale_price']*100;
                        $data['member_miniapp_id'] = $this->miniapp_id;
                        $entrust_id = Entrust::insertGetId($data);
                    } else {
                        Entrust::where(['member_miniapp_id'=>$this->miniapp_id,'item_id' => $value['item_id']])->setInc('gite_count',1);
                        $entrust_id = $entrust->id;
                    }
                    //增加托买数据
                    $entrust_data[$key]['member_miniapp_id'] = $this->miniapp_id;
                    $entrust_data[$key]['order_no']          = $rel->order_no;
                    $entrust_data[$key]['entrust_id']        = $entrust_id;
                    $entrust_data[$key]['user_id']           = $this->user->id;
                    $entrust_data[$key]['order_amount']      = $rel->order_amount*100; //分
                    $entrust_data[$key]['entrust_price']     = $value['sale_price']*100; //分
                    $entrust_data[$key]['item_id']           = $value['item_id'];
                    $entrust_data[$key]['is_under']          = $is_under;
                    $entrust_data[$key]['create_time']       = time();
                    $entrust_data[$key]['is_fusion']         = 0;
                    $entrust_state[$key] = true;
                } else {
                    //提货
                    $entrust_state[$key] = false;
                }
            }
        }
        if (!empty($entrust_data)) {
            EntrustList::insertAll($entrust_data);
        }
        OrderCache::where(['order_no' => $rel->order_no])->update(['entrust' => json_encode($entrust_state),'fusion_state' => $fusion_state]);
        $rel->is_entrust = 1;   //修改已确认订单状态
        $rel->save();
        return json(['code'=>200,'msg'=>'订单已确认,在小店中可管理寄卖的产品']);
    }
}