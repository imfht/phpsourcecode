<?php
namespace app\muushop\logic;

use think\Model;
use app\muushop\model\MuushopOrder as Order;
use app\muushop\logic\MuushopCoupon as Coupon;
/*
 * 订单逻辑层
 */
class MuushopOrder extends Model{

	/*
	 * 下单
	 */
	public function make_order($order)
	{
		if (empty($order['pay_type'] || $order['pay_type'] == Order::PAY_TYPE_NULL)) {
			$this->error_str = '支付方式未设置或出错！';
			return false;
		}
		if ($order['pay_type'] ==  'delivery') {
			//货到付款
			$order['status'] = 2;//待发货
		}

		//记录下单时的商品价格
		//减库存, [在取消订单的时候加库存]
		//计算运费
		//计算各种优惠, 满100减5元, 满100包邮 等
		$opaid_fee = 0; //最终应付总价
		$oback_point = 0; //应返还积分数
		$total_count = 0;//商品的总件数
		$ops = []; //保存到order中
		$tmp_price=0;//单个临时商品价格变量
		
		$cart = model('muushop/MuushopCart')->getDataByIds($order['cart_id'], get_uid());
		if(empty($cart)){
			$this->error_str = '购物车数据错误！';
			return false;
		}
		foreach ($cart as $p) {
			
			//检查库存是否足够
			if($p['product']['sku_table']) {
				$sku_info = substr($p['sku_id'],strpos($p['sku_id'],';')+1);
				if($p['quantity'] > $p['product']['sku_table']['info'][$sku_info]['quantity']){
					$this->error_str = '木有库存了';
					return false;
				}
			}else{
				if ($p['quantity'] > $p['product']['quantity']) {
					$this->error_str = '木有库存了';
					return false;
				}
			}
			
			/*
			计算运费，多加商品运费模版取单价最高商品运费模版
			*/
			//获得商品总件数
			$total_count += $p['quantity'];
			//保存到订单表中products的数据
			$ops[] = [
				'sku_id' => $p['sku_id'],
                'quantity' => $p['quantity'],
                'paid_price' => $p['product']['price'],
                'title' => $p['product']['title'],
                'main_img' => $p['product']['main_img']
            ];

			$opaid_fee += $p['product']['price'] * $p['quantity'];//应付总价
			$oback_point += $p['product']['back_point'] * $p['quantity'];//返还积分
		}
		
		//根据运费模版id 地址 商品数量获取计算最终运费价格
		$odelivery_fee = $this->calcDeliveryFee($order['delivery_id'], $order['address_id'], $total_count);
		$odelivery_fee = $odelivery_fee*100;//将价格单位转为分
		if(!isset($odelivery_fee)){
				$this->error_str = '运费计算出错！';
				return false;
		}

		//计算各种优惠信息
		$odiscount_fee = 0; //初始化已优惠价格为0
		//取优惠券优惠信息
		if (!empty($order['coupon_id'])) {
			$order_info = [
				'uid' => $order['uid'],
                'opaid_fee' => $opaid_fee,
                'odelivery_fee' => $odelivery_fee,
            ];
            //获取优惠金额并判断优惠卷是否可用
            $coupon = new Coupon();
			$ret = $coupon->calcCouponFee($order['coupon_id'], $order_info);
			if(!$ret){
				$this->error_str = $coupon->error_str;
				return false;
			}
			$odiscount_fee += $ret;
		}

		//获取积分使用总抵用金额
		if (!empty($order['use_point'])) {
			$able_score = modC('MUUSHOP_SCORE_TYPE','','muushop');
			$able_score_id = substr($able_score,5);
			$exchange = modC('MUUSHOP_SCORE_PROP','','muushop');//获取兑换比例
			$ret = $order['use_point']/$exchange; //该积分抵用金额
			$ret = $ret*100;//金额转化成分
			$odiscount_fee += $ret;
		}
		//余额支付（依赖钱包模块）该版本暂不提供
		
		// 获取应付总金额
		$opaid_fee = max(0, $opaid_fee + $odelivery_fee - $odiscount_fee);
		
		if ($opaid_fee == 0) {
			$data['pay_type'] = 'free';
		}
		//免费或货到付款时把订单状态改为待发货
		if (($order['pay_type'] == 'free') || ($order['pay_type'] == 'delivery')) {
			$data['status'] = Order::ORDER_WAIT_FOR_DELIVERY;
		}
		
		$data['uid'] = $order['uid'];//用户ID获取
		$data['client_ip'] = request()->ip();
		$data['order_no'] = date('YmdHis').rand(100000, 999999);//自动生成商家唯一订单号
		$data['pay_type'] = $order['pay_type'];
		$data['channel'] = $order['channel'];
		$data['paid_fee'] = $opaid_fee;//应付总金额
		$data['use_point'] = $order['use_point']; //抵用积分数据
		$data['back_point'] = $oback_point; //返还总积分数量
		$data['discount_fee'] = $odiscount_fee;//返现金额
		$data['delivery_fee'] = $odelivery_fee;//运费价格
		$data['products'] = json_encode($ops);
		$data['address'] = json_encode(model('muushop/MuushopUserAddress')->getDataById($order['address_id']));
		$data['info'] = json_encode($order['info']);

		
		$this->startTrans();
		//写入订单表
		$order['id'] = model('muushop/MuushopOrder')->editData($data);//写入订单数据
		
		if(!$order['id']){
			$this->error_str = '增加订单失败';
			$this->rollback();
			return false;
		}
		//dump($data);exit;
		//根据$ops减库存
		foreach ($ops as $l) {
			if (!$this->decreaseProductQuantity($l['sku_id'], $l['quantity'])) {
				$this->rollback();
				$this->error_str = '减库存失败';
				return false;
			}
		}

		//标记优惠券为已使用
		if(!empty($order['coupon_id'])){
			$coupon_data['order_id'] = $order['id'];
			$coupon_data['id'] = $order['coupon_id'];
			$coupon_data['status'] = 1;
			$coupon_res = model('muushop/MuushopUserCoupon')->editData($coupon_data);

			if(!$coupon_res){
				$this->error_str = '优惠卷处理失败';
				$this->rollback();
				return false;
			}
		}
		
		//如果使用积分抵用扣除相应积分
		if($order['use_point']>0) {
			$score_conf = modC('MUUSHOP_SCORE_TYPE','muushop');
			if(!$score_conf){
				$this->error_str = '系统未设置积分类型，请联系管理员';
				$this->rollback();
				return false;
			}
			$score_id = substr($score_conf, 5);
			$scoreType = model('ucenter/Score')->getType(['id'=>$score_id]);//根据ID获取积分类型详细
            $remark = '商城订单[ID:'.$order['id'].']抵用'.$scoreType['title'].'：-'.$order['use_point'].$scoreType['unit'];
			$ress = model('ucenter/Score')->setUserScore([$order['uid']],$order['use_point'],$score_id,'dec','muushop',$order['id'],$remark);//减少积分
			if(!$ress) {
				$this->error_str = '减扣积分时系统出错';
				$this->rollback();
				return false;
			}
		} 
		//如果有返还积分增加对应积分

		
		//删除购物车
		if ((!empty($GLOBALS['_POST']['cart_id']))) {
			$cart_ids = $GLOBALS['_POST']['cart_id'];
			is_numeric($cart_ids) && $cart_ids = array($cart_ids);
			model('muushop/MuushopCart')->deleteData($cart_ids, $order['uid']);
		}
		$this->commit();
		return $order['id'];
	}

	/*
	 * 支付订单
	 */
	public function pay($order,$channel = '')
	{
		if ($order['status'] != Order::ORDER_WAIT_USER_PAY) {
			$this->error_str = '错误的订单状态';
			return false;
		}
		$update = [
			'id' => $order['id'],
			'channel' => $channel,
		];
		$res = model('muushop/MuushopOrder')->editData($update);
		return $res;
	}
	/*
	 * 支付完成订单
	 */
	public function payOrder($order)
	{
		if ($order['status'] != Order::ORDER_WAIT_USER_PAY) {
			$this->error_str = '错误的订单状态';
			return false;
		}
		$update = [
			'id' => $order['id'],
			'status' => Order::ORDER_WAIT_FOR_DELIVERY,
			'paid_time' => (empty($order['paid_time'])?time():$order['paid_time']) ,
			'pay_type' => $order['pay_type'],
			'pay_info' => $order['pay_info'],
		];
		model('muushop/MuushopOrder')->editData($update);
		return true;
	}
	/**
	 * 订单支付后处理
	 * @param [type] $data  [description]
	 * @param [type] $odata [description]
	 */
	public function AfterPayOrder($data,$odata)
	{
		$shop_order = $this->order_model->where('id ="'.$odata['aim_id'].'"')->find();
		if(!empty($shop_order))
		{
			$shop_order['paid_time'] = strtotime($data['time_end']);
			$shop_order['pay_type'] = Order::PAY_TYPE_WEIXINPAY;
			$shop_order['pay_info'] =   array(
				'callback_time' => $_SERVER['REQUEST_TIME'],
				'transaction_id' => $data['transaction_id'],
				'trade_type' => $data['trade_type'],
				'appid' => $data['appid'],
				'mch_id' => $data['mch_id'],
				'openid' => $data['openid'],
				'bank_type' => $data['bank_type'],
				'fee_type' => $data['fee_type'],
				'total_fee' => $data['total_fee'],
			);
			$shop_order['pay_info'] = json_encode($shop_order['pay_info']);
			$this->pay_order($shop_order);//支付订单
		}
	}

	/**
	* 更改订单价格
	*/
	public function changePrice($order,$price_info = [])
	{
		if ($order['status'] != order::ORDER_WAIT_USER_PAY) {
			$this->error_str = '未支付订单可更改价格';
			return false;
		}
		//计算总价格
		$paid_fee = 0;
		foreach($price_info['paid_price'] as $val){
			$paid_fee += $val*100;
		}

		$paid_fee += $price_info['delivery_fee']*100;
		//商品单品价格
		$new_products = [];
		foreach($order['products'] as $key => $val){
			$new_products[$key]['sku_id'] = $val['sku_id'];
			$new_products[$key]['quantity'] = $val['quantity'];
			$new_products[$key]['paid_price'] = $price_info['paid_price'][$key]*100;
			$new_products[$key]['title'] = $val['title'];
			$new_products[$key]['main_img'] = $val['main_img'];
		}
		$new_products = json_encode($new_products);
		//已优惠价格
		$discount_fee = $order['discount_fee'] + ($order['paid_fee'] - $paid_fee);
		
		if ($order['delivery_fee'] == $price_info['delivery_fee']*100 && 
			$order['paid_fee'] == $paid_fee
		){
			$this->error_str = '价格未改变';
			return false;
		}
		
		//组装更新数据
		$update = [
			'id' => $order['id'],
			'order_no' => date('YmdHis').rand(100000, 999999),//需重新生成订单号
			'paid_fee' => $paid_fee,//总价
			'delivery_fee' => $price_info['delivery_fee']*100,//邮费
			'discount_fee' => $discount_fee, //已优惠的价格
			'products' => $new_products,
		];

		if(model('muushop/MuushopOrder')->editData($update))
		{
			return true;
		}
	}

	/*
	 * 取消订单
	 */
	public function cancalOrder($order)
	{
		if ($order['status'] != order::ORDER_WAIT_USER_PAY) {
			$this->error_str = '错误的订单状态';
			return false;
		}
		$update = [
			'id' => $order['id'],
			'status' => Order::ORDER_CANCELED,
		];

		$this->startTrans();
		if(!model('muushop/MuushopOrder')->editData($update))
		{
			$this->rollback();
			return false;
		}
		//取消订单, 把商品库存加回去
		foreach ($order['products'] as $p) {
			if(!$this->increaseProductQuantity($p['sku_id'], $p['quantity']))
			{
				$this->error_str = '更新库存数据失败';
				$this->rollback();
				return false;
			}
		}
		//使用积分部分返还用户
		if($order['use_point']>0) {
			$score_conf = modC('MUUSHOP_SCORE_TYPE','muushop');
			if(!$score_conf){
				$this->error_str = '系统未设置积分类型，请联系管理员';
				$this->rollback();
				return false;
			}
			$score_id = substr($score_conf, 5);
			$scoreType = model('ucenter/Score')->getType(['id'=>$score_id]);//根据ID获取积分类型详细
            $remark = '商城订单[ID:'.$order['id'].']取消，返还'.$scoreType['title'].'：+'.$order['use_point'].$scoreType['unit'];
			$ress = model('ucenter/Score')->setUserScore([$order['uid']],$order['use_point'],$score_id,'inc','muushop',$order['id'],$remark);//返还积分
			if(!$ress) {
				$this->error_str = '返还积分时系统出错';
				$this->rollback();
				return false;
			}
		} 

		$this->commit();
		return true;
	}

	/*
	 * 发货和更改物流信息
	 */
	public function sendGood($order,$deliver_info){
		if ($order['status'] == Order::ORDER_WAIT_FOR_DELIVERY || $order['status'] == Order::ORDER_WAIT_USER_RECEIPT) {

			$update = [
				'id' => $order['id'],
				'status' => Order::ORDER_WAIT_USER_RECEIPT,
				'send_time' => time(),
				'delivery_info' => json_encode($deliver_info),
			];
			$res = model('muushop/MuushopOrder')->editData($update);
			
			return $res;
		}else{
			$this->error_str = '订单状态错误';
			return false;
		}
	}

	/*
	 * 确认收货
	 */
	public  function recvGoods($order)
	{
		if ($order['status'] != Order::ORDER_WAIT_USER_RECEIPT) {
			$this->error_str = '订单状态错误';
			return false;
		}

		$update = [
			'id' => $order['id'],
			'status' => Order::ORDER_DELIVERY_OK,
			'recv_time' => $_SERVER['REQUEST_TIME'],
		];
		
		$this->startTrans();
		if(!model('muushop/MuushopOrder')->editData($update)){
			$this->error_str ='确认收货更新订单状态时出错';
			$this->rollback();
			return false;
		}

		//增加购买记录
		foreach($order['products'] as $p) {
			$sp =model('muushop/MuushopProduct')->getDataBySkuid($p['sku_id']);
			$insert = [
				'order_id' => $order['id'],
                'product_id' => current(explode(';', $p['sku_id'])),
                'uid' => $order['uid'],
                'create_time' => $_SERVER['REQUEST_TIME'],
                'paid_price' => $sp['price'],
                'quantity' => $p['quantity'],
                'detail' => json_encode(['sku_id' => $p['sku_id']]),
			];

			if(!model('muushop/MuushopProductSell')->addData($insert)){
				$this->error_str ='增加商品购买记录出错';
				$this->rollback();
				return false;
			}
		}

		$this->commit();
		return true;
	}

	/*
	 * 订单退货||售后申请
	 * $info 可以是退货原因，产品图片等数组
	 */
	public function negotationOrder($order,$info = [])
	{
		$update = [
			'id' => $order['id'],
			'status' => Order::ORDER_UNDER_NEGOTATION,
		];
		if ($info) {
			$update['info'] = $info;
		}
		$res = model('muushop/MuushopOrder')->editData($update);

		return true;
	}

	/*
	 * 商家同意退货
	 */
	public function negotationOrderOk($order)
	{
		if ($order['status'] != Order::ORDER_UNDER_NEGOTATION) {
			$this->error_str ='错误的订单状态';
			return false;
		}

		$update = array(
			'id' => $order['id'],
			'status' => Order::ORDER_NEGOTATION_OK,
		);
		model('muushop/MuushopOrder')->editData($update);
		return true;
	}

	/*
	 * 删除订单
	 */
	public function deleteOrder($ids)
	{
		$order = model('muushop/MuushopOrder')->getDataByid($ids);
		if ($order['status'] == Order::ORDER_WAIT_USER_PAY) {
			$this->cancalOrder($order);
		}
		if (!in_array($order['status'], array(Order::ORDER_CANCELED, Order::ORDER_WAIT_USER_PAY))) {
			$this->error_str = '只有取消或长期未付款的订单可以删除';
			return false;
		}
		/*
		 * 使用的优惠券 返回原来状态
		 */
		if(($id = $this->user_coupon_logic->where('order_id = '.$order['id'].' and user_id = '.$order['user_id'])->find()))
		{
			$this->user_coupon_logic->where('id = '.$id)->save(array('order_id'=>0));
		}

		$ret = model('muushop/MuushopOrder')->delete($order['id']);

		return $ret;
	}


	/*
	 * 取消订单增加 库存
	 */
	public function increaseProductQuantity($sku_id, $quantity)
	{
		$sku_id = explode(';', $sku_id, 2);
		//处理并发
		do{
			$p = model('muushop/MuushopProduct')->getDataByid($sku_id[0]);
			if(!$p) {
				$this->error_str = '系统错误';
				return false;
			}
			if(empty($sku_id[1])) { //普通减库存
				$res = model('muushop/MuushopProduct')->save(['sell_cnt'=>($p['sell_cnt']-1),'quantity'=>($p['quantity']+$quantity)],['id'=>$p['id']]);
			}
			else {
				if(empty($p['sku_table']['info'][$sku_id[1]])) {
					$this->error_str = '系统错误';
					return false;
				}
				$new = $p['sku_table'];
				$new['info'][$sku_id[1]]['quantity'] += $quantity;
				$res = model('muushop/MuushopProduct')->save(['sku_table'=>json_encode($new),'sell_cnt'=>($p['sell_cnt']-$quantity)],['id'=>$p['id']]);
			}
		}
		while(!$res);

		return true;
	}

	/*
	 *减库存 , 加销量
	 */
	public function decreaseProductQuantity($sku_id, $quantity) {
		$sku_id = explode(';', $sku_id, 2);
		//处理并发
		do{
			$p = model('muushop/MuushopProduct')->getDataById($sku_id[0]);
			$p['sku_table_ori'] = $p['sku_table'];
			if(empty($sku_id[1])) { //普通减库存
				if($p['quantity'] < $quantity) {
					$this->error_str = '木有库存了';
					return false;
				}
				$res = model('muushop/MuushopProduct')->save(['sell_cnt'=>($p['sell_cnt']+1),'quantity'=>($p['quantity']-$quantity)],['id'=>$p['id']]);
			} else {
				if(empty($p['sku_table']['info'][$sku_id[1]])) {
					$this->error_str = '系统错误';
					return false;
				}
				if($p['sku_table']['info'][$sku_id[1]]['quantity'] < $quantity) {
					$this->error_str = '木库存了';
					return false;
				}

				$new = $p['sku_table'];
				$new['info'][$sku_id[1]]['quantity'] -= $quantity;
				$res = model('muushop/MuushopProduct')->save(['sku_table'=>json_encode($new),'sell_cnt'=>($p['sell_cnt']+$quantity)],['id'=>$p['id']]);
			}
		}while(!$res);

		return true;
	}

	/**
	*	计算运费
	*	$delivery_id int 运费模版id
	*	$address 送货地址 
	* 	$totalcount 商品总数
	*/
	public function calcDeliveryFee($delivery_id,$address_id, $totalcount) {

		if($delivery_id==0) {
			return 0;
		}else{
			$delivery = model('muushop/MuushopDelivery')->getDataById($delivery_id);
		}
	
		if(!isset($delivery['rule']['express'])){
			$way = key($delivery['rule']);
		}else{
			$way = 'express';
		}
		if($delivery['valuation']==0) {
			//固定运费
			return sprintf("%01.2f", $delivery['rule'][$way]['cost']/100);
		}

		if($delivery['valuation']==1) {
			$address = model('muushop/MuushopUserAddress')->getDataById($address_id);
			
			if(!empty($delivery['rule'][$way]['custom'])) {
	            foreach($delivery['rule'][$way]['custom'] as $val){
					foreach($val['area'] as $v){
						if($address['province'] == $v['id']){
							$cost = $val['cost'];
						}
					}
				}
				if(empty($cost)){
					$cost = $delivery['rule'][$way]['normal'];
				}
			}
			if($totalcount<=$cost['start']){
				$totalPrice = $cost['start_fee'];
			}else{
				$totalPrice = ($cost['start_fee'] + ceil((float)($totalcount - $cost['start'])/$cost['add']) * $cost['add_fee']);
			}
			return sprintf("%01.2f", $totalPrice/100);
		}				

	}
	/*
	 * 增加商品评论
	 */
	public  function add_product_comment($product_comments)
	{
		$this->startTrans();
		foreach($product_comments as $product_comment)
		{
			if(($ret = $this->product_comment_model->getDataByMay($product_comment)))
			{
				$this->error_str = '只能评论一次';
				return false;
			}
			if(!$this->product_sell_model->get_sell_record_by_may($product_comment))
			{
				$this->error_str = '只能购买过才能评论';
				return false;
			}
			//添加评论
			if(!$this->product_comment_model->editData($product_comment))
			{
				$this->error_str = '评论失败';
				$this->rollback();
				return false;
			}
			//增加商品统计信息
			$may = array('id'=>$product_comment['product_id']);
			if(!($this->product_model->where($may)->setInc('comment_cnt'))
				|| !($this->product_model->where($may)->setInc('score_cnt'))
				|| !($this->product_model->where($may)->setInc('score_total',$product_comment['score'])))
			{
				$this->error_str = '增加商品评论数据失败';
				$this->rollback();
				return false;
			}
		}
		//更改订单状态
		$may = array(
			'id'=>$product_comments[0]['order_id'],
			'user_id'=>$product_comments[0]['user_id'],
			'status' => MuushopOrderModel::ORDER_DELIVERY_OK
			);
		if(!model('muushop/MuushopOrder')->where($may)->save(array('status' => MuushopOrderModel::ORDER_COMMENT_OK)))
		{
			$this->error_str = '订单状态更新失败';
			$this->rollback();
			return false;
		}

		$this->commit();
		return  true;
	}
}

