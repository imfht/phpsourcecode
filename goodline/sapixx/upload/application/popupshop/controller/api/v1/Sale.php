<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 小程序公共API服务
 */
namespace app\popupshop\controller\api\v1;
use app\popupshop\controller\api\Base;
use app\common\facade\WechatPay;
use app\common\model\SystemMemberPayment;
use app\common\model\SystemUserAddress;
use app\common\model\SystemUserLevel;
use app\common\model\SystemMemberBank;
use app\popupshop\model\Sale as AppSale;
use app\popupshop\model\SaleHouse;
use app\popupshop\model\SaleOrder;
use app\popupshop\model\SaleOrderCache;
use app\popupshop\model\SaleUser;
use app\popupshop\model\Config;
use think\facade\Request;
use util\Util;

class Sale extends Base{

    /**
     * 获得首页
     */
    public function index(){
        $param['signkey'] = Request::param('signkey');
        $param['sign']    = Request::param('sign');
        $rel = $this->apiSign($param);
        if($rel['code'] != 200){
            return enjson(204,'签名失败');
        }
        $condition[] = ['member_miniapp_id','=',$this->miniapp_id];
        $condition[] = ['is_sale','=',1];
        $condition[] = ['is_pay','=',0];
        $condition[] = ['is_out','=',0];
        $info = AppSale::with(['User'=> function($query) {
            $query->field('face,nickname,id');
        }])
        ->with(['house'=> function($query) {
            $query->field('id,title,name,note,sell_price,img');
        }])
        ->where($condition)->field('id,store_id,user_id,house_id,user_cost_price,user_entrust_price,user_sale_price,gift,update_time')->order('id desc')->limit(5)->select();
        if($info->isEmpty()){
            return enjson(204,'空内容');
        }
        $data = [];
        foreach ($info as $key => $value) {
            $data[$key] = $value;
            $data[$key]['user']  = empty($value->user) ? []  : $value->user;
            $data[$key]['store'] = empty($value->store) ? [] : $value->store;
            $data[$key]['house'] = $value->house;
            $house_ids = array_column(json_decode($value->gift),'house_id');
            $gift = [];
            foreach ($house_ids as $i => $id) {
                $gift[$i] = SaleHouse::where(['id' => $id])->field('id,title,name,note,sell_price,img')->find()->toArray();
            }
            $data[$key]['gift'] = $gift;
        }
        $rel['sale'] = $data;
        //统计每日数量
        $rel['num']  = AppSale::where($condition)->count();
        return enjson(200,'成功',$rel);
    }

    /**
     * 获得列表
     */
    public function lists(){
        $param['page'] = Request::param('page/d',1);
        $param['sign'] = Request::param('sign');
        $rel = $this->apiSign($param);
        if($rel['code'] != 200){
            return enjson(204,'签名失败');
        }
        $condition[] = ['member_miniapp_id','=',$this->miniapp_id];
        $condition[] = ['is_sale','=',1];
        $condition[] = ['is_pay','=',0];
        $condition[] = ['is_out','=',0];
        //读取我的推荐人
        $condition_user = [];
        if($this->user){
            $level_user = SystemUserLevel::where(['user_id' => $this->user->id,'level'=>1])->field('parent_id')->find();
            if(!empty($level_user)){
                $condition_user[] = ['user_id','=',$level_user->parent_id];
                $condition_user[] = ['user_id','<>',$this->user->id];
            }
        }
        //数据
        $field = 'id,store_id,user_id,house_id,user_cost_price,user_entrust_price,user_sale_price,gift,update_time';
        $info = AppSale::with(['User'=> function($query) {
            $query->field('face,nickname,id');
        }])->with(['house'=> function($query) {
            $query->field('id,title,name,note,sell_price,img');
        }])->where($condition)->where($condition_user)->field($field)->order('id desc')->paginate(10);
        if($info->isEmpty()){
            if($param['page'] == 1){
                $info = AppSale::with(['User' => function($query) {
                    $query->field('face,nickname,id');
                }])->with(['house'=> function($query) {
                    $query->field('id,title,name,note,sell_price,img');
                }])->where($condition)->field($field)->order('create_time asc')->limit(2)->select();
            }else{
                $info = [];
            }
        }
        $data = [];
        foreach ($info as $key => $value) {
            $data[$key] = $value;
            $data[$key]['user']      = empty($value->user) ? []  : $value->user;
            $data[$key]['is_store']  = empty($value->user) ? 0 : 1;
            $data[$key]['store']     = empty($value->store) ? [] : $value->store;
            $data[$key]['house']       = $value->house;
            $house_ids = array_column(json_decode($value->gift),'house_id');
            $gift = [];
            foreach ($house_ids as $i => $id) {
                $gift[$i] = SaleHouse::where(['id' => $id])->field('id,title,name,note,sell_price,img')->find()->toArray();
            }
            $data[$key]['gift'] = $gift;
        }
        return enjson(200,'成功',$data);
    }

    /**
     * 获取某个产品
     */
    public function item(){
        $param['id']   = Request::param('id/d',1);
        $param['sign'] = Request::param('sign');
        $rel = $this->apiSign($param);
        if($rel['code'] != 200){
            return enjson(204,'签名失败');
        }
        $condition[] = ['member_miniapp_id','=',$this->miniapp_id];
        $condition[] = ['is_sale','=',1];
        $condition[] = ['is_pay','=',0];
        $condition[] = ['is_out','=',0];
        $condition[] = ['id','=',$param['id']];
        $info = AppSale::with(['User'=> function($query) {
            $query->field('face,nickname,id');
        }])
        ->with(['house'=> function($query) {
            $query->field('id,title,name,note,sell_price,img,imgs,content');
        }])
        ->where($condition)->field('id,store_id,user_id,house_id,user_entrust_price,user_sale_price,gift,update_time')->order('id desc')->find();
        if(empty($info)){
            return enjson(204,'空内容');
        }
        $data = $info->toArray();
        $data['user']  = empty($info->user) ? []  : $info->user;
        $data['store'] = empty($info->store) ? [] : $info->store;
        $data['is_store']  = empty($info->user) ? 0 : 1;
        $data['house'] = $info->house;
        $data['house']['imgs'] = json_decode($info->house->imgs,true);
        $house_ids = array_column(json_decode($info->gift),'house_id');
        $gift = [];
        foreach ($house_ids as $i => $id) {
            $gift[$i] = SaleHouse::where(['id' => $id])->field('id,title,name,note,sell_price,img')->find()->toArray();
        }
        $data['gift'] = $gift;
        return enjson(200,'成功',$data);
    }

    /**
     * 查看是否允许下单
     * @param integer 读取ID
     * @return json
     */
    public function isOrder(){
        $this->isUserAuth();
        $param['id']   = Request::param('id/d',1);
        $param['sign'] = Request::param('sign');
        $rel = $this->apiSign($param);
        if($rel['code'] != 200){
            return enjson(204,'签名失败');
        }
        $condition[] = ['id','=',$param['id']];
        $condition[] = ['member_miniapp_id','=',$this->miniapp_id];
        $condition[] = ['is_sale','=',1];
        $condition[] = ['is_pay','=',0];
        $condition[] = ['is_out','=',0];
        $sale = AppSale::where($condition)->count();
        if (empty($sale)) {
            return enjson(403,'宝贝已下架');
        }
        return enjson(200);
    } 
    

    /**
     * 查看是否允许下单
     * @param integer 读取ID
     * @return json
     */
    public function cartItem(){
        $this->isUserAuth();
        $param['cart'] = Request::param('cart/d',0);
        $param['sign'] = Request::param('sign');
        $rel = $this->apiSign($param);
        if($rel['code'] != 200){
            return enjson(204,'签名失败');
        }
        $condition[] = ['id','=',$param['cart']];
        $condition[] = ['member_miniapp_id','=',$this->miniapp_id];
        $condition[] = ['is_sale','=',1];
        $condition[] = ['is_pay','=',0];
        $condition[] = ['is_out','=',0];
        $info = AppSale::with(['house'=> function($query) {
            $query->field('id,title,name,note,sell_price,cost_price,img');
        }])
        ->where($condition)->field('id,store_id,user_id,house_id,user_entrust_price,user_sale_price,gift,update_time')->find();
        if(empty($info)){
            return enjson(204,'空内容');
        }
        $data = $info->toArray();
        $data['house']  = $info->house;
        $data['amount']['order_amount'] = $info->user_sale_price;
        $data['amount']['real_amount']  = $info->user_sale_price;
        $data['amount']['real_freight'] = 0;
        $house_ids = array_column(json_decode($info->gift),'house_id');
        $gift = [];
        foreach ($house_ids as $i => $id) {
            $gift[$i] = SaleHouse::where(['id' => $id])->field('id,title,name,note,sell_price,img')->find()->toArray();
        }
        $data['gift'] = $gift;
        return enjson(200,'成功',$data);
    }

    /**
     * 微信商城支付
     * @param string $no
     * @return void
     */
    public function doPay(){
        $this->isUserAuth();
        $rule = [
            'address' => Request::param('address/d'),
            'cart'    => Request::param('cart/d'),
            'ucode'   => Request::param('ucode'),
            'sign'    => Request::param('sign')
        ];
        $validate = $this->validate($rule,'Cart.sale_order');
        if(true !== $validate){
            return enjson(403,$validate);
        }
        $rel = $this->apiSign($rule);
        if($rel['code'] != 200){
            return enjson(403,'签名失败');
        }
        $sale_id = $rule['cart'];
        if(empty($sale_id)){
            return enjson(204,'购物车空');
        }
        $config  = Config::where(['member_miniapp_id' => $this->miniapp_id])->find();
        //下单次数
        if($config->num_referee_people > 0){
            $peple_num = SystemUserLevel::where(['parent_id' => $this->user->id,'level'=>1])->count();
            $sale_user    = SaleUser::where(['member_miniapp_id' => $this->miniapp_id,'user_id' => $this->user->id,'is_rebate' => 0,'is_out' => 0])->field('user_id,order_no,is_rebate')->select();
            $entrust_num  = count(Util::unique_array($sale_user->toArray()));
            $num = ceil($peple_num/$config->num_referee_people);
            if($num <= $entrust_num && $num){
                return enjson(403,"还有[{$entrust_num}]单未成交,暂时不能购买。");
            }
        }
        //读取发货地址
        $address = SystemUserAddress::where(['user_id'=>$this->user->id,'id' => $rule['address']])->find();
        if(empty($address)){
            return enjson(204,'请重新选择收货地址');
        }
        //支付接口
        $payment = SystemMemberPayment::where(['apiname'=>'wepay','member_miniapp_id'=>$this->miniapp_id])->field('id')->find();
        if(empty($payment)){
            return enjson(204,'未开通微信支付功能');
        } 
        //读取订单 
        $condition[] = ['id','=',$sale_id];
        $condition[] = ['member_miniapp_id','=',$this->miniapp_id];
        $condition[] = ['is_sale','=',1];
        $condition[] = ['is_pay','=',0];
        $condition[] = ['is_out','=',0];
        $item = AppSale::where($condition)->find();
        if(empty($item)){
            return enjson(204,'空内容');
        }
        //查询应用帐号余额
        if(SystemMemberBank::moneyJudge($this->miniapp->member_id,$item->user_sale_price*0.05)){
            return enjson(204,'官方帐号余额不足,请联系管理员');
        }
        $house_ids = array_column(json_decode($item->gift),'house_id');
        $gift = [];
        foreach ($house_ids as $i => $id) {
            $gift[$i] = SaleHouse::where(['id' => $id])->field('id,title,name,note,sell_price,img')->find()->toArray();
        }
        $item->gift = $gift;
        $order_no = $this->user->invite_code.order_no(); //生成的订单号
        $order['order_no']          = $order_no;
        $order['member_miniapp_id'] = $this->miniapp_id;
        $order['user_id']           = $this->user->id;
        $order['order_amount']      = $item->user_sale_price;
        $order['real_amount']       = $item->user_sale_price;
        $order['sale_id']           = $item->id; 
        $order['payment_id']        = $payment['id'];    //支付ID
        $order['express_name']      = $address['name'];
        $order['express_phone']     = $address['telphone'];
        $order['express_address']   = $address['address'];
        $order['real_freight']      = 0;
        $order['order_starttime']   = time();
        $order_id = SaleOrder::insertGetId($order); 
        if(empty($order_id)){
            return enjson(204,'创建订单失败');
        }
        //主商品
        $house_data = [
            'sale_order_id' => $order_id,
            'order_no'      => $order_no,
            'house_id'      => $item->house_id,
            'sale_price'    => $item->house->sell_price,
            'name'          => $item->house->name,
            'img'           => $item->house->img,
            'is_sales'      => 1,
            'is_entrust'    => 0
        ];
        //保存订单产品到缓存数据表
        $order_cache = [];
        foreach ($item->gift as $key => $value) {
            $order_cache[$key]['sale_order_id'] = $order_id;
            $order_cache[$key]['order_no']      = $order_no;
            $order_cache[$key]['house_id']      = $value['id'];
            $order_cache[$key]['sale_price']    = $value['sell_price'];
            $order_cache[$key]['name']          = $value['name'];
            $order_cache[$key]['img']           = $value['img'];
            $order_cache[$key]['is_sales']      = 0;
            $order_cache[$key]['is_entrust']    = 0;
        }
        array_unshift($order_cache,$house_data);
        $rel = SaleOrderCache::insertAll($order_cache); //批量插入订单记录
        //去请求微信支付接口
        $payparm = [
            'openid'     => $this->user->miniapp_uid,
            'miniapp_id' => $this->miniapp_id,
            'name'       => $this->miniapp->appname.'购买套装',
            'total_fee'  => $item->user_sale_price*100,
            'order_no'   => $order_no,
            'notify_url' => api(1,'popupshop/notify/sale',$this->miniapp_id),
        ];
        $ispay = WechatPay::orderPay($payparm);
        if($ispay['code'] == 0){
            return enjson(403,$ispay['msg']);
        }
        return enjson(200,'成功',$ispay['data']);
    }
}