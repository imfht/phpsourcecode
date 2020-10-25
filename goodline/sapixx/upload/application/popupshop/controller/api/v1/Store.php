<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 小程序公共API服务
 */
namespace app\popupshop\controller\api\v1;
use app\popupshop\controller\api\Base;
use app\popupshop\model\Store as AppStore;
use app\popupshop\model\Sale;
use app\popupshop\model\SaleUser;
use app\popupshop\model\SaleOrder;
use app\popupshop\model\SaleOrderCache;
use app\popupshop\model\SaleCategory;
use app\popupshop\model\SaleHouse;
use app\popupshop\model\Order;
use app\popupshop\model\OrderCache;
use app\popupshop\model\Config;
use app\popupshop\model\BankBill;
use app\popupshop\widget\Reward;
use app\popupshop\model\Bank;
use app\common\facade\WechatPay;
use app\common\model\SystemMemberPayment;
use app\common\model\SystemUserAddress;
use think\facade\Request;

class Store extends Base{
    
    public function initialize() {
        parent::initialize();
        if(!$this->user){
            exit(json_encode(['code'=>401,'msg'=>'用户认证失败']));
        }
    }

    /**
     * 获取配置
     */
    public function isOpen(){
        $rel = AppStore::where(['uid' => $this->user->id ])->find();
        if($rel){
            return enjson(200,'已开通小店',['name' => $rel->name]);
        }else{
            return enjson(204);
        }
    }

   /**
     * 开通小店
     * @return void
     */
    public function regStore(){
        if (request()->isPost()) {
            $param['store_name'] = Request::param('store_name');
            $param['formId']     = Request::param('formId');
            $param['sign']       = Request::param('sign');
            $rel = $this->apiSign($param);
            if($rel['code'] != 200){
                return enjson(204,'签名失败');
            }
            if (empty($param['store_name'])) {
                return enjson(403,'必须输入小店名称');
            }
            $store = AppStore::where(['uid' => $this->user->id])->count();
            if($store){
                return enjson(204,'已开通小店不用重复申请');
            }
            AppStore::insert(['name' => $param['store_name'],'uid' => $this->user->id,'update_time' =>  time()]);
            return enjson(200,'小店开通成功');
        }
    }

   /**
     * 我的产品
     * @return void
     */
    public function item(){
        if (request()->isGet()) {
            $param['page']  = Request::param('page/d');
            $param['types'] = Request::param('types/d',0);
            $param['sign']  = Request::param('sign');
            $rel = $this->apiSign($param);
            if($rel['code'] != 200){
                return enjson(204,'签名失败');
            }
            $condition['user_id']           = $this->user->id;
            $condition['member_miniapp_id'] = $this->miniapp_id;
            switch ($param['types']) {
                case 1:
                    $condition['is_sale']   = 1;
                    $condition['is_rebate'] = 0;
                    $condition['is_out']    = 0;
                    break;
                case 2:
                    $condition['is_sale']   = 0;
                    $condition['is_rebate'] = 1;
                    $condition['is_out']    = 0;
                    break;
                case 3:
                    $condition['is_out']   = 1;
                    break;
                default:
                    $condition['is_sale']   = 0;
                    $condition['is_rebate'] = 0;
                    $condition['is_out']    = 0;
                    break;
            }
            $lists = SaleUser::with(['house' => function($query) {
                $query->field('id,title,name,img,note');
            }])->where($condition)->order('id desc')->paginate(10);
            $data = [];
            foreach ($lists as $key => $value) {
                $data[$key]['id']          = $value->id;
                $data[$key]['house']       = $value->house;
                $data[$key]['status_text'] = SaleUser::status($value);
                $data[$key]['create_time'] = date('Y-m-d H:i',$value->create_time);
                $data[$key]['is_out']      = $value->is_out;
                $data[$key]['is_sale']     = $value->is_sale;
                $data[$key]['is_rebate']   = $value->is_rebate;
                $data[$key]['rebate']      = $value->rebate;
                $data[$key]['user_price']  = $value->user_price;
                //计算配套产品
                $gift = [];
                if(isset($value->sale)){
                    $house_ids = array_column(json_decode($value->sale->gift),'house_id');
                    foreach ($house_ids as $i => $id) {
                        $gift[$i] = SaleHouse::where(['id' => $id])->find()->toArray();
                    }
                }
                $data[$key]['sale'] = $gift;
            }
            if(empty($data)){
                return enjson(204,'无内容');
            }
            return enjson(200,'成功',$data);
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
            $sale = SaleUser::where(['user_id' => $this->user->id,'id' => $param['id'],'is_out' => 0])->find();
            if(empty($sale)){
                return enjson(403,'宝贝未找到或未下架');
            }
            if(empty($sale->sale)){
                return enjson(302,'初次上架,请先采购礼品',['url' => '/pages/store/house?id='.$sale->id]);
            }
            $is_sale = $sale->is_sale ? 0 : 1;
            //更新上下架
            Sale::where(['sales_user_id' => $sale->id])->update(['is_sale' => $is_sale,'update_time' => time()]);
            $sale->is_sale      = $is_sale;
            $sale->update_time  = time();
            $sale->save();
            $msg = $is_sale ? '宝贝上架成功' : '宝贝下架成功';
            return enjson(200,$msg);
        }
    }
   
    /**
     * 判断订单的赠品是否已经卖了还钱,如果没有就去卖了还钱
     * @return void
     */
    public function isOnSale(){
        if (request()->isPost()) {
            $param['id']       = Request::param('id/d');
            $param['order_no'] = Request::param('order_no/s');
            $param['sign']     = Request::param('sign');
            $rel = $this->apiSign($param);
            if($rel['code'] != 200){
                return enjson(403,'接口签名失败');
            }
            $store = AppStore::where(['uid' => $this->user->id])->find();
            if(empty($store)){
                return enjson(302,'请先开通小店',['url' => '/pages/store/index']);
            }
            $condition = [];
            $condition['member_miniapp_id'] = $this->miniapp_id;
            $condition['user_id']           = $this->user->id;
            $condition['order_no']          = $param['order_no'];
            $condition['paid_at']           = 1;
            $condition['is_entrust']        = 0;
            $condition['is_out']            = 0;
            $condition['status']            = 0;
            $order = SaleOrder::where($condition)->find();
            if(empty($order)){
                return enjson(204,'订单状态不可操作');
            }
            $orderList = $order->orderList()->where(['is_entrust' =>0,'is_sales' => 0])->select(); //查询订单中包含的宝贝泪奔
            if($orderList->isEmpty()){
                $order->is_entrust = 1;
                $order->save();
                return enjson(204,'禁止重复委托');
            }
            $data = []; 
            $order_cache_id = 0; 
            foreach ($orderList as $value) {
                if($value->id == $param['id']){
                    $order_cache_id = $value->id; 
                    $data['member_miniapp_id'] = $this->miniapp_id;
                    $data['user_id']           = $this->user->id;
                    $data['house_id']          = $value->house_id;
                    $data['order_no']          = $value->order_no;
                    $data['user_price']        = $order->real_amount;
                    $data['rebate']            = 0;
                    $data['is_rebate']         = 0;
                    $data['is_sale']           = 0;
                    $data['create_time']       = time();
                    $data['update_time']       = time();
                }
            }
            if($order_cache_id && !empty($data)){
                $sale_user_id = SaleUser::insertGetId($data);
                if($sale_user_id){
                    SaleOrderCache::where(['id' => $order_cache_id])->update(['is_entrust' => 1]);
                    $orderCachenum = SaleOrderCache::where(['order_no' => $param['order_no'],'is_sales' => 0,'is_entrust' => 0])->count();
                    if(!$orderCachenum){
                        SaleOrder::where(['order_no' => $param['order_no']])->update(['is_entrust' => 1]);
                    }
                    //判断结算利润
                    if($order->is_settle == 0 && !empty($order->sale)){
                        $config = Config::where(['member_miniapp_id' => $order->member_miniapp_id])->find();
                        $rebate = saleUser::where(['id' =>$order->sale->sales_user_id,'is_rebate' => 1,'is_lock_rebate' => 0])->find();
                        if(!empty($rebate)){
                            $rebate->is_lock_rebate = 1;
                            $rebate->save();
                            //资金到账并创建日志
                            Bank::setDueMoney($order->member_miniapp_id,$order->sale->user_id,$rebate->rebate);
                            BankBill::add($order->member_miniapp_id,$order->sale->user_id,$rebate->rebate,'成交利润已结算',$order->user_id,$order->order_no);
                            //创建分账与扣点
                            Reward::agent($order,$config); //给代理结算费用
                        }
                        Reward::fees($order,$config);  //扣除平台费用
                    }
                    //修改订单状态为已分账
                    if($order->is_settle == 0){
                        $order->is_settle = 1;
                        $order->save();
                    }
                    return enjson(200,'去采购礼品',['url' => '/pages/store/house?id='.$sale_user_id]);
                }
            }
            return enjson(403,'卖了换钱操作失败,请稍后再试');
        }
    }

    /**
     * 初次上下架宝贝
     * @return void
     */
    public function onSale(){
        if (request()->isPost()) {
            $param['sale_ids']      = Request::param('sale_ids/s');
            $param['user_sale_id']  = Request::param('user_sale_id/d');
            $param['entrust_price'] = Request::param('entrust_price/f',0);
            $param['sign']          = Request::param('sign');
            $rel = $this->apiSign($param);
            if($rel['code'] != 200){
                return enjson(403,'接口签名失败');
            }
            $sale_ids = ids(json_decode($param['sale_ids'],true),true);
            $store = AppStore::where(['uid' => $this->user->id])->find();
            if(empty($store)){
                return enjson(302,'请先开通小店',['url' => '/pages/store/index']);
            }
            $info = SaleUser::where(['user_id' => $this->user->id,'id' => $param['user_sale_id'],'is_sale' => 0,'is_out' => 0])->find();
            if(empty($info)){
                return enjson(403,'要上架的产品已不存在');
            }
            if(!empty($info->sale)){
                return enjson(403,'禁止重复采购礼品');
            }
            if($info->is_out){
                return enjson(403,'上架产品已退出委托');
            }
            if($info->is_rebate){
                return enjson(403,'上架产品已成交,不用重复上架');
            }
            $house = SaleHouse::where(['id' => $sale_ids,'is_sale' => 1,'is_del' => 0])->select();
            if(empty($house)){
                return enjson(403,'未找到礼品');
            }
            //配置
            $config = Config::where(['member_miniapp_id' => $this->miniapp_id])->find();
            $cost_price = array_sum(array_column($house->toArray(),'cost_price'));
            //利润
            $service_fee = $param['entrust_price']*$config->profit/100; //服务费
            $rebate = $param['entrust_price']-$service_fee-$cost_price; //成交价-服务费-成本
            if($rebate <= 0){
                return enjson(403,'您设置的寄卖价过低.利润低于0元');
            }
            $gift = [];
            $entrust_price = $param['entrust_price'];
            foreach ($house as $key => $value) {
                $gift[$key]['house_id']      = $value->id;
                $gift[$key]['cost_price']    = $value->cost_price;
                $gift[$key]['entrust_price'] = $entrust_price;
                $gift[$key]['sale_price']    = $entrust_price;
            }
            if(count($sale_ids) != count($gift)){
                return enjson(204,'你采购的礼品数量不符');
            }
            $data = [
                'member_miniapp_id' => $this->miniapp_id,
                'sales_user_id'     => $info->id,
                'house_id'          => $info->house_id,
                'cost_price'        => $info->house->cost_price,
                'entrust_price'     => $entrust_price,
                'sale_price'        => $entrust_price,
                'store_id'          => $store->id,
                'user_id'           => $this->user->id,
                'is_sale'           => 1,
                'gift'              => $gift,
            ];
            $rel = Sale::edit($data);
            if($rel){
                $info->is_sale = 1;
                $info->save();
                return enjson(200,'产品上架成功');
            }
            return enjson(200,'产品上架失败');
        }
    }

     /**
     * 确认提货
     * @return void
     */
    public function onOrder(){
        if (request()->isPost()) {
            $param['id']   = Request::param('id/d');
            $param['sign'] = Request::param('sign');
            $rel = $this->apiSign($param);
            if($rel['code'] != 200){
                return enjson(204,'签名失败');
            }
            $sale = SaleUser::where(['user_id' => $this->user->id,'id' => $param['id'],'is_rebate' => 0,'is_out' => 0])->find();
            if(empty($sale)){
                return enjson(403,'宝贝未找到或未下架');
            }
            //读取发货地址
            $address = SystemUserAddress::where(['user_id'=>$this->user->id,'is_first' => 1])->find();
            if(empty($address)){
                return enjson(403,'请重新选择收货地址');
            }
            //支付接口
            $payment = SystemMemberPayment::where(['apiname' => 'wepay','member_miniapp_id'=>$this->miniapp_id])->find();
            if(empty($payment)){
                return enjson(403,'未开通微信支付功能');
            }
            $amount = 0.01;
            $order_no = $this->user->invite_code.order_no(); //生成的订单号
            $order['payment_id']        = $payment['id'];    //支付ID
            $order['express_name']      = $address['name'];
            $order['express_phone']     = $address['telphone'];
            $order['express_address']   = $address['address'];
            $order['order_amount']      = $amount;
            $order['real_amount']       = $amount;
            $order['real_freight']      = 0;
            $order['order_no']          = $order_no;
            $order['member_miniapp_id'] = $this->miniapp_id;
            $order['user_id']           = $this->user->id;
            $order['sales_user_id']     = $sale->id;
            $order['order_starttime']   = time();
            $order_id = Order::insertGetId($order); 
            if(empty($order_id)){
                return enjson(403,'创建订单失败');
            }
            //保存订单产品到缓存数据表
            $item_data['order_id']  = $order_id ;
            $item_data['order_no']  = $order_no;
            $item_data['item_id']   = $sale->house->id;
            $item_data['name']      = $sale->house->name;
            $item_data['img']       = $sale->house->img;
            $item_data['buy_price'] = 0;
            $item_data['buy_nums']  = 1;
            OrderCache::insert($item_data); 
            //已经创建的订单下架和退出
            Sale::where(['sales_user_id' => $sale->id])->update(['is_sale' => 0,'is_out' => 1,'update_time' => time()]);
            //把用户的产品强制下架并退出
            $sale->is_out      = 1;
            $sale->is_rebate   = 1;
            $sale->is_sale     = 0;
            $sale->update_time  = time();
            $sale->save();
            //去请求微信支付接口
            $payparm = [
                'openid'     => $this->user->miniapp_uid,
                'miniapp_id' => $this->miniapp_id,
                'name'       => $this->miniapp->appname.'申请提货',
                'order_no'   => $order_no,
                'total_fee'  => $amount*100,
                'notify_url' => api(1,'popupshop/notify/shop',$this->miniapp_id),
            ];
            $ispay = WechatPay::orderPay($payparm);
            if($ispay['code'] == 0){
                return enjson(403,$ispay['msg']);
            }
            return enjson(200,'成功',$ispay['data']);
        }
    }
    
    /**
     * 库存商品栏目
     */
    public function saleHouseCate(){
        if (request()->isGet()) {
            $param['signkey'] = Request::param('signkey');
            $param['sign']    = Request::param('sign');
            $rel = $this->apiSign($param);
            if($rel['code'] != 200){
                return enjson(204,'签名失败');
            }
            $info = SaleCategory::where(['member_miniapp_id' => $this->miniapp_id])->field('id,name,title,picture')->order(['sort'=>'desc','id'=>'desc'])->select();
            return enjson(200,'成功',$info->toArray());
        }
    }

    /**
     * 平台中的库存商品列表
     */
    public function salelHouse(){
        if (request()->isGet()) {
            $param['page']         = Request::param('page/d',1);
            $param['cate_id']      = Request::param('cate_id/d',0);
            $param['user_sale_id'] = Request::param('user_sale_id/d',0);
            $param['sign']         = Request::param('sign');
            $param['num']          = Request::param('num/d',10);
            $rel = $this->apiSign($param);
            if($rel['code'] != 200){
                return enjson(204,'签名失败');
            }
            $info = SaleUser::where(['member_miniapp_id' => $this->miniapp_id,'user_id' => $this->user->id,'id' => $param['user_sale_id']])->field('user_price')->find();
            $condition[] = ['is_sale','=',1];
            $condition[] = ['is_del','=',0];
            $condition[] = ['member_miniapp_id','=',$this->miniapp_id];
            $condition[] = ['sell_price','<=',$info->user_price];
            if($param['cate_id'] > 0){
                $condition[] = ['category_id','=',$param['cate_id']];
            }
            $list = SaleHouse::where($condition)->field('id,title,name,note,img,sell_price,cost_price')->order('id desc')->paginate(20)->toArray();
            if(empty($list['data'])){
                return enjson(204,'无内容');
            }
            return enjson(200,'成功',$list['data']);
        }
    }
    
    /**
     * 售卖库存的商品
     * @return void
     */
    public function saleUser(){
        if (request()->isGet()) {
            $param['user_sale_id'] = Request::param('user_sale_id/d',0);
            $param['sign']         = Request::param('sign');
            $rel = $this->apiSign($param);
            if($rel['code'] != 200){
                return enjson(204,'签名失败');
            }
            $info = SaleUser::with('house')->where(['member_miniapp_id' => $this->miniapp_id,'user_id' => $this->user->id,'id' => $param['user_sale_id']])->find();
            if($info->is_out){
                return enjson(403,'上架产品已退出委托');
            }
            if($info->is_rebate){
                return enjson(403,'上架产品已成交,不用重复上架');
            }
            return enjson(200,'成功',$info);
        }
    }
}