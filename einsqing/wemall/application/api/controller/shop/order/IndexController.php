<?php
namespace app\api\controller\shop\order;
use app\api\controller\BaseController;

class IndexController extends BaseController
{
	// 获取订单列表
    public function index()
    {
        if (request()->isPost()){
            
            return $this->add();
        }else{
            $user_id = $this->get_user_id();

            $map = array();
            $map['user_id']  = $user_id;
            if(input('param.status') != ''){
                $map['status']  = ['in',input('param.status')];
            }
            if(input('param.pay_status') != ''){
                $map['pay_status']  = input('param.pay_status');
            }
            
            if(input('param.page')){
                
                $data['order'] = model('Order')->with('user,contact,detail.product.file,fee')->where($map)->order('id', 'desc')->paginate();
            }else{
               
                $data['order'] = model('Order')->with('user,contact,detail.product.file,fee')->where($map)->order('id', 'desc')->select();
            }
            
            return json(['data' => $data, 'msg' => '订单列表', 'code' => 1]);
        } 
    }
    // 获取订单详情
    public function detail()
    {
    	$user_id = $this->get_user_id();
    	
    	$id = input('param.id');
        $order = model('Order')->with('user,contact,delivery,detail.product.file,fee')->where('user_id',$user_id)->find($id);
        if($order['delivery_code']){
            $Express = new \com\Express();
            $order['express'] = $Express -> getorder($order['delivery_code']);
        }else{
            $order['express'] = '';
        }

        $data['order'] = $order;
        return json(['data' => $data, 'msg' => '订单详情', 'code' => 1]);
    }
    //检查库存
    private function checkStore($cartData)
    {
        $msg = '';
        $data = array();
        $data['totalprice'] = 0;
        $data['totalscore'] = 0;
        $data['totalprice_org'] = 0;
        foreach ($cartData as $key => $value) {
            $product = model('Product')->with('skus')->find($value['id'])->toArray();
            if(empty($value['ids'])){
                if($product['store']){
                    if($value['num'] > $product['store']){
                        $msg .= $product['name'].',';
                    }else{
                        $data['totalprice'] += floatval($product["price"]*$value['num']);
                        $data['totalprice_org'] += floatval($product["price"]*$value['num']);
                        $data['totalscore'] += floatval($product["score"]*$value['num']);
                    }
                }else{
                    $msg .= $product['name'].',';
                } 
            }else{
                // 有sku情况
                $map['product_id'] = $value['id'];
                $map['ids'] = $value['ids'];
                $product_sku = model('ProductSku')->where($map)->find();
                if($product_sku['store']){
                    if($value['num'] > $product_sku['store']){
                        $msg .= $product_sku['name'].',';
                    }else{
                        $data['totalprice'] += floatval($product_sku["price"]*$value['num']);
                        $data['totalprice_org'] += floatval($product_sku["price"]*$value['num']);
                        $data['totalscore'] += floatval($product["score"]*$value['num']);
                    }
                }else{
                    $msg .= $product_sku['name'].',';
                } 
            }
        }
        if($msg){
            abort(json(['data' => false,"msg" => $msg.'库存不足', "code" => 0]));
        }else{
            return $data;
        }
    }
    //处理余额支付
    private function updateUserMoney($userId, $money)
    {
        $user = model('User')->find($userId);
        $balance = floatval($user["money"]) - floatval($money);
        if ($balance >= 0) {
            model('User')->where('id',$userId)->update(['money' => $balance]);
            return true;
        } else {
            return false;
        }
    }
    // 下订单
    public function add()
    {
        $user_id = $this->get_user_id();//用户id
        if(input('param.id')){
            $data = input('param.');
            $map = array();
            $map['id']  = ['in',$data['id']];
            $map['user_id'] = $user_id;

            $result = model('Order')->where($map)->update(['status' => $data['status']]);
            if($result){
                return json(['data' => false, 'msg' => '取消成功', 'code' => 1]);
            }else{
                return json(['data' => false, 'msg' => '取消失败', 'code' => 0]);
            }
        }else{
            $post_data = input('post.');
            $cartData = $post_data['cartData'];//购物车数据
            $contact_id = input('?post.contact_id') ? $post_data['contact_id'] : '';//收获地址
            $payment_id = $post_data['payment_id'];//付款方式
            $delivery_time = $post_data['delivery_time'];//配送时间
            $type = input('?post.type') ? $post_data['type'] : 0;//1到店自提0送货上门
            $store_address = input('?post.store_address') ? $post_data['store_address'] : 0;//自提点
            
            $phone = input('?post.phone') ? $post_data['phone'] : '';
            $coupon_id = input('?post.coupon_id') ? $post_data['coupon_id'] : '';//优惠券
            $remark = input('?post.remark') ? $post_data['remark'] : '';//备注
            $fee_id = input('?post.fee_id') ? $post_data['fee_id'] : '';//费用模版
            // $delivery_id = input('post.delivery_id');//快递方式
            // $user_id = 1;//用户id
            // $contact_id = 1;//收获地址
            // $payment_id = 1;//付款方式
            // $delivery_id = 1;//快递方式
            // $cartData = array(
            //     '0' => array(
            //         'id'  => 1,
            //         'ids'  => 2-6,
            //         'num' => 2,
            //     ),
            //     '1' => array(
            //         'id'  => 2,
            //         'ids'  => 2-7,
            //         'num' => 2,
            //     ),
            // );
            $data = $this->checkStore($cartData);//检查库存

            $data['user_id'] = $user_id;
            $data['orderid'] = date("ymdhis") . mt_rand(1, 9);
            $data['payment_id'] = $payment_id;
            $data['delivery_time'] = $delivery_time;
            $data['type'] = $type;
            $data['remark'] = $remark;
            if($store_address){
                $store_address = x_model("AddonStores")->where('id',$store_address)->value('address');
                $data['stores']['address'] = $store_address;
                $data['stores']['phone'] = $phone;
            }
            //费用模版
            if($fee_id){
                $fee_ids = explode(",",$fee_id);
                foreach ($fee_ids as $key => $value) {
                    $fee = model('FeeTpl')->find($value);
                    $data['totalprice'] = floatval($data['totalprice']) + floatval($fee['value']);
                    $data['totalprice_org'] = floatval($data['totalprice_org']) + floatval($fee['value']);
                }
            }
            if($coupon_id){
                addons_hook('useCoupon', ['user_id'=>$user_id,'id'=>$coupon_id]);
                $coupon = x_model("AddonsCommonCoupon")->find($coupon_id);
                $data['coupon']['code'] = $coupon['code'];
                $data['coupon']['price'] = $coupon['price'];
                $data['totalprice'] = floatval($data['totalprice']) - floatval($coupon['price']);
                $data['totalprice'] = $data['totalprice'] >= 0 ? $data['totalprice'] : 0;
            }
            // $data['delivery_id'] = $delivery_id;
            
            $order = model("Order")->create($data);

            if($contact_id){
                //收获地址处理
                $contact = model("UserContact")->with('country,province,city,district')->find($contact_id)->toArray();
                model("OrderContact")->create([
                    'user_id'  =>  $user_id,
                    'order_id' =>  $order->id,
                    'name'     =>  $contact['name'],
                    'phone'    =>  $contact['phone'],
                    'province' =>  $contact['province']['name'],
                    'city'     =>  $contact['city']['name'],
                    'district' =>  $contact['district']['name'],
                    'address'  =>  $contact['address']
                ]);
            }

            //处理商品detail
            $order_detail = array();
            foreach ($cartData as $key => $value) {
                $product = model('Product')->with('skus')->find($value['id'])->toArray();
                if(empty($value['ids'])){
                    $item = array();
                    $item['order_id'] = $order->id;
                    $item['product_id'] = $product['id'];
                    $item['user_id'] = $user_id;
                    $item['name'] = $product['name'];
                    $item['num'] = $value['num'];
                    $item['price'] = $product['price'];
                    array_push($order_detail, $item);
                    model('Product')->where('id', $value['id'])->setDec('store', $value['num']);
                }else{
                    // 有sku情况
                    $map['product_id'] = $value['id'];
                    $map['ids'] = $value['ids'];
                    $product_sku = model('ProductSku')->where($map)->find();

                    $item = array();
                    $item['order_id'] = $order->id;
                    $item['product_id'] = $value['id'];
                    $item['user_id']  = $user_id;
                    $item['name']     = $product['name'];
                    $item['num']      = $value['num'];
                    $item['price']    = $product_sku['price'];
                    $item['sku_id']   = $product_sku['id'];
                    $item['sku_name'] = $product_sku['name'];
                    array_push($order_detail, $item);
                    model('ProductSku')->where($map)->setDec('store', $value['num']);
                } 
            }
            model('OrderDetail')->saveAll($order_detail);

            //费用模版
            if($fee_id){
                $fee_ids = explode(",",$fee_id);
                foreach ($fee_ids as $key => $value) {
                    $fee = model('FeeTpl')->find($value);
                    model("OrderFee")->create([
                        'order_id' =>  $order->id,
                        'name'     =>  $fee['name'],
                        'value'    =>  $fee['value']
                    ]);
                }
            }
            //统计
            $newBuyUser = 0;
            $buyUser = model("User")->where('id',$user_id)->value('buy_num');
            if (!$buyUser) {
                $newBuyUser = 1;
            }
            model("Analysis")->add(1, floatval($order["totalprice"]), 0, $newBuyUser);
            // 发送模版消息
            action('admin/WechatController/sendTplMsgOrder',['order_id' => $order->id]);

            //微信打印机
            $wxprint = model("WxPrint")->find();
            if($wxprint["switch"] == 1){
               wxPrint($order["id"]); 
            }

            //支付方式
            $back = array();
            $back["order"] = model('Order')->with('user,contact,detail.product.file,fee')->find($order["id"]);

            return json(['data' => $back, 'msg' => '提交成功', 'code' => 1]);
        }
    }

    //取消订单
    public function cancel()
    {
        $id = input('param.id');
        $user_id = $this->get_user_id();

        $map = array();
        $map['id'] = $id;
        $map['user_id'] = $user_id;
        
        $result = model('Order')->where($map)->update(['status' => -1]);
        if($result){
            return json(['data' => false, 'msg' => '取消成功', 'code' => 1]);
        }else{
            return json(['data' => false, 'msg' => '取消失败', 'code' => 0]);
        }
    }

    //确认收货
    public function confirmReceipt(){
        $user_id = $this->get_user_id();
        $id = input('param.id');

        $map = array();
        $map['id'] = $id;
        $map['user_id'] = $user_id;
        
        $result = model('Order')->where($map)->update(['status' => 2]);
        if($result){
            return json(['data' => false, 'msg' => '确认成功', 'code' => 1]);
        }else{
            return json(['data' => false, 'msg' => '确认失败', 'code' => 0]);
        }
    }
	

}