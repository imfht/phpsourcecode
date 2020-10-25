<?php
// +----------------------------------------------------------------------
// | UCToo [ Universal Convergence Technology ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014-2015 http://uctoo.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Patrick <contact@uctoo.com>
// +----------------------------------------------------------------------

namespace app\shop\logic;
use think\Model;
use app\shop\model\ShopOrder as ShopOrderModel;
/*
 * 订单逻辑层
 */
class ShopOrder extends Model{

	protected $product_cats_model;
	protected $product_sell_model;
	protected $product_model;
	protected $order_model;
	protected $delivery_model;
	protected $coupon_logic;
	protected $user_coupon_logic;
	protected $cart_model;
	protected $product_comment_model;
	public $error_str='';

	function _initialize()
	{
		$this->product_cats_model = D('Shop/ShopProductCats');
		$this->product_model = D('Shop/ShopProduct');
		$this->order_model = D('Shop/ShopOrder');
		$this->delivery_model = D('Shop/ShopDelivery');
		$this->coupon_logic = D('Shop/ShopCoupon','Logic');
		$this->user_coupon_logic = D('Shop/ShopUserCoupon','Logic');
		$this->cart_model         = D('Shop/ShopCart');
		$this->product_comment_model = D('Shop/ShopProductComment');
		$this->product_sell_model = D('Shop/ShopProductSell');
		$this->delivery_model = D('Shop/ShopDelivery');

		parent::_initialize();
	}
	/*
	 * 下单
	 */
	public function make_order($order)
	{
		if (!isset($order['pay_type']) || $order['pay_type'] ==  ShopOrderModel::PAY_TYPE_FREE) {
			//禁止设为 免费
			$order['pay_type'] = ShopOrderModel::PAY_TYPE_NULL;
		}

		//记录下单时的商品价格
		//减库存, [在取消订单的时候加库存]
		//计算运费
		//计算各种优惠, 满100减5元, 满100包邮 等

		$opaid_fee = 0; //最终应付总价
		$opoint_fee = 0; //应付积分数
		$oback_point = 0; //应返还积分数
		$ops = array(); //保存到order中
		$lps = array(); //检查购买限制, 更新库存
		$dps = array(); //计算运费
		foreach ($order['products'] as $p) {
			if (!($sp = $this->product_model->get_product_by_sku_id($p['sku_id']))) {
				$this->error_str = '系统错误';
				return false;
			}
			//检查库存是否足够
			if ($p['quantity'] > $sp['quantity']) {
				$this->error_str = '木有库存了';
				return false;
			}

			/*
				同一个商品不同sku, 进行同样的限购处理
				todo 应该把历史购买过的数目也加上
			*/
			if (isset($lps[$sp['id']])) {
				$lps[$sp['id']]['quantity'] += $p['quantity'];
			} else {
				$lps[$sp['id']] = array('buy_limit' => $sp['buy_limit'],
				                         'quantity' => $p['quantity'],
				                         'sku_id' => $p['sku_id'],
				                         'quantity_total' => $sp['quantity_total']);
			}

			/*
				计算运费,相同运费模板的商品以件数算一次运费
			*/
			if (isset($dps[$sp['delivery_id']])) {
				$dps[$sp['delivery_id']]['count'] += $p['quantity'];
			} else {
				$dps[$sp['delivery_id']] = array('count' => $p['quantity']);
			}

			$ops[] = array('sku_id' => $p['sku_id'],
			               'quantity' => $p['quantity'],
			               'paid_price' => $sp['price'],
			               'title' => $sp['title'],
			               'main_img' => $sp['main_img']);
			$opaid_fee += $sp['price'] * $p['quantity'];
			$opoint_fee += $sp['point_price'] * $p['quantity'];
			$oback_point += $sp['back_point'] * $p['quantity'];
		}

		foreach ($lps as $l) {
			if (($l['buy_limit'] > 0) && ($l['buy_limit'] < $l['quantity'])) {
				$this->error_str = '超过限制购买数量';
				return false;
			}
		}

		//根据$dps, $address计算运费
		$odelivery_fee = 0;
		if (!empty($order['address'])) {
			foreach ($dps as $k => $v) {
				$odelivery_fee += $this->calc_delivery_fee($k, $order['address'], $v);
				$odelivery_fee +=0;
			}
		}

		//计算各种优惠信息
		$odiscount_fee = 0;
		//取优惠券优惠信息
		if (!empty($order['coupon_id'])) {
			$order_info = array('user_id' => $order['user_id'],
			                    'opaid_fee' => $opaid_fee,
			                    'odelivery_fee' => $odiscount_fee,
			                    'products' => $order['products']);
			$ret = $this->coupon_logic->calc_coupon_fee($order['coupon_id'], $order_info);
			if(!$ret)
			{
				$this->error_str = $this->coupon_logic->error_str;
				return false;
			}
			$odiscount_fee += $ret;
		}

		//如果>0认为是一个积分换购商品, 此时不收商品费而只收运费. 并且不再返还积分
		if ($opoint_fee > 0) {
			$discount_fee = $opaid_fee;
			$oback_point = 0;
		}
		$order['use_point'] = $opoint_fee;
		$order['back_point'] = $oback_point;

		$opaid_fee = max(0, $opaid_fee + $odelivery_fee - $odiscount_fee);
		//or ?
		//$opaid_fee = max(0, $opaid_fee - $odiscount_fee) + $odelivery_fee;
		if ($opaid_fee == 0) {
			$order['pay_type'] = ShopOrderModel::PAY_TYPE_FREE;
		}
		//免费或货到付款时把订单状态改为待发货
		if (($order['pay_type'] == ShopOrderModel::PAY_TYPE_FREE) ||
			($order['pay_type'] == ShopOrderModel::PAY_TYPE_CACHE)
		) {

			//某些订单(如外卖订单)需要等待买家确认
			$order['status'] = !empty($GLOBALS['_TMP']['on_free_status']) ? $GLOBALS['_TMP']['on_free_status']
				: ShopOrderModel::ORDER_WAIT_FOR_DELIVERY;
		}
		$order['create_time'] = $_SERVER['REQUEST_TIME'];
		$order['paid_fee'] = $opaid_fee;
		$order['discount_fee'] = $odiscount_fee;
		$order['delivery_fee'] = $odelivery_fee;

		$order['products'] = json_encode($ops);
		if (!empty($order['address'])) {
			$order['address'] = json_encode($order['address']);
		}
		$order['info'] = json_encode($order['info']);


		$this->startTrans();
		//根据$lps减库存
		foreach ($lps as $l) {
			if (!$this->decrease_product_quantity($l['sku_id'], $l['quantity'])) {
				$this->rollback();
				$this->error_str = '减库存失败';
				return false;
			}
		}

		if(!$this->order_model->add_or_edit_order($order))
		{
			$this->rollback();
			$this->error_str = '增加订单失败';
			return false;
		}

		$order['id'] = $this->getLastInsID();

		if ($order['use_point'] > 0) {
		//处理积分的
		}

		//标记优惠券
		if(!empty($order['coupon_id']))
		{
			if(!$this->user_coupon_logic->where('order_id = 0 and id ='.$order['coupon_id'])->save(array('order_id'=>$order['id'])))
			{
				$this->rollback();
				return false;
			}
		}
		//删除购物车
		if ((!empty($GLOBALS['_TMP']['cart_id']))) {
			$cart_ids = $GLOBALS['_TMP']['cart_id'];
			is_numeric($cart_ids) && $cart_ids = array($cart_ids);
			$this->cart_model->delete_shop_cart($cart_ids, $order['user_id']);
		}
		//下单以后事件
		Hook('AfterMakeOrder',$order);
		$this->commit();

		return $order['id'];
	}

	/*
	 * 下单后操作 往通用订单内插入订单
	 */
	public function AfterMakeOrder($order)
	{


	}


	/*
	 * 支付订单
	 */
	public function pay_order($order)
	{
		if ($order['status'] != ShopOrderModel::ORDER_WAIT_USER_PAY) {
			$this->error_str = '错误的订单状态';
			return false;
		}
		$update = array(
			'id' => $order['id'],
			'status' => ShopOrderModel::ORDER_WAIT_FOR_DELIVERY,
			'paid_time' => (empty($order['paid_time'])?$_SERVER['REQUEST_TIME']:$order['paid_time']) ,
			'pay_type' => $order['pay_type'],
			'pay_info' => $order['pay_info'],
		);
		$this->order_model->add_or_edit_order($update);
		return true;
	}

	public function AfterPayOrder($data,$odata)
	{
		$shop_order = $this->order_model->where('id ="'.$odata['aim_id'].'"')->find();
		if(!empty($shop_order))
		{
			$shop_order['paid_time'] = strtotime($data['time_end']);
			$shop_order['pay_type'] = ShopOrderModel::PAY_TYPE_WEIXINPAY;
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

	/*
	 * 取消订单
	 */
	public function cancal_order($order)
	{
		if (!in_array($order['status'], array(ShopOrderModel::ORDER_WAIT_USER_PAY,
		                                  //ShopOrderModel::ORDER_WAIT_FOR_DELIVERY, 待发货时可以申请退款，不要取消
		                                  ShopOrderModel::ORDER_UNDER_NEGOTATION,
		                                  ShopOrderModel::ORDER_WAIT_SHOP_ACCEPT))
		) {
			$this->error_str = '错误的订单状态';
			return false;
		}

		$update = array(
			'id' => $order['id'],
			'status' => (!(CONTROLLER_NAME=='Index')?ShopOrderModel::ORDER_SHOP_CANCELED: ShopOrderModel::ORDER_CANCELED),
		);
		$this->startTrans();


		if(!$this->order_model->add_or_edit_order($update))
		{
			$this->rollback();
			return false;
		}
		//取消订单, 把商品库存加回去
		foreach ($order['products'] as $p) {
			if(!$this->increase_product_quantity($p['sku_id'], $p['quantity']))
			{
				$this->rollback();
				return false;
			}

		}
		Hook('AfterCancalOrder',$order);

		$this->commit();

//		$order = $this->order_model->get_order_by_id($order['id']);
		return true;
	}

	/*
	 * 发货
	 */
	public function send_good($order,$deliver_info){
		if ($order['status'] != ShopOrderModel::ORDER_WAIT_FOR_DELIVERY) {
//			setLastError(ERROR_BAD_STATUS);
			$this->error_str = '订单状态错误';
			return false;
		}

		$update = array(
			'id' => $order['id'],
			'status' => ShopOrderModel::ORDER_WAIT_USER_RECEIPT,
			'send_time' => $_SERVER['REQUEST_TIME'],
			'delivery_info' => json_encode($deliver_info),
		);
		$ret = $this->order_model->add_or_edit_order($update);
		Hook('AfterSendGood',$order);
		return $ret;
	}

	/*
	 * 收货
	 */
	/*
	  收货
  */
	public  function recv_goods($order)
	{
		if ($order['status'] != ShopOrderModel::ORDER_WAIT_USER_RECEIPT) {
//			setLastError(ERROR_BAD_STATUS);
			$this->error_str = '订单状态错误';
			return false;
		}

		$update = array(
			'id' => $order['id'],
			'status' => ShopOrderModel::ORDER_DELIVERY_OK,
			'recv_time' => $_SERVER['REQUEST_TIME'],
		);
		$product_cell_model = D('Shop/ShopProductSell');
		$this->startTrans();
		if(!$this->order_model->add_or_edit_order($update))
		{
			$this->error_str ='修改订单错误';
			$this->rollback();
			return false;
		}

		//增加购买记录
		foreach($order['products'] as $p) {
			$sp =$this->product_model->get_product_by_sku_id($p['sku_id']);
			$insert = array('order_id' => $order['id'],
			                'product_id' => current(explode(';', $p['sku_id'])),
			                'user_id' => $order['user_id'],
			                'create_time' => $_SERVER['REQUEST_TIME'],
			                'paid_price' => $sp['price'],
			                'quantity' => $p['quantity'],
			                'detail' => json_encode(array('sku_id' => $p['sku_id'])),
			);

			if(!$product_cell_model->add_sell_record($insert))
			{
				$this->error_str ='增加商品购买记录出错';
				$this->rollback();
				return false;
			}
		}
		Hook('AfterRecvGood',$order);
		$this->commit();
		return true;
	}



	/*
	 * 协商订单
	 */
	public function negotation_order($order,$info = array())
	{
		$update = array(
			'id' => $order['id'],
			'status' => ShopOrderModel::ORDER_UNDER_NEGOTATION,
		);
		if ($info) {
			$update['info'] = $info;
		}
		$this->order_model->add_or_edit_order($update);

		return true;

	}

	/*
	 * 协商结果
	 */
	public function negotation_over_order($order)
	{
		if ($order['status'] != ShopOrderModel::ORDER_UNDER_NEGOTATION) {
			$this->error_str ='错误的订单状态';
			return false;
		}

		$update = array(
			'id' => $order['id'],
			'status' => ShopOrderModel::ORDER_NEGOTATION_OK,
		);
		$this->order_model->add_or_edit_order($update);
		return true;
	}


	/*
	 * 取消订单增加 库存
	 */
	public function increase_product_quantity($sku_id, $quantity)
	{
		$sku_id = explode(';', $sku_id, 2);
		//处理并发
		do{
			$p = $this->product_model->get_product_by_id($sku_id[0]);
			if(!$p) {
				$this->error_str = '系统错误';
				return false;
			}
			if(empty($sku_id[1])) { //普通减库存
				$ret = $this->product_model->where('id = '.$p['id'])->save(array('sell_cnt'=>($p['sell_cnt']-1),'quantity'=>($p['quantity']+$quantity)));

			}
			else {
				if(empty($p['sku_table']['info'][$sku_id[1]])) {
					$this->error_str = '系统错误';
					return false;
				}
				$new = $p['sku_table'];
				$new['info'][$sku_id[1]]['quantity'] += $quantity;
				$ret = $this->product_model->where('id = '.$p['id'])->save(array('sku_table'=>json_encode($new),'sell_cnt'=>($p['sell_cnt']-$quantity)));

			}
		}
		while(!$ret);



		return true;
	}

	/*
	 *减库存 , 加销量
	 */
	public function decrease_product_quantity($sku_id, $quantity) {
		$sku_id = explode(';', $sku_id, 2);
		//处理并发
		do{
			$p = $this->product_model->where('id = '.$sku_id[0])->field('id, quantity, sku_table,sell_cnt')->find();
			$p['sku_table_ori'] = $p['sku_table'];
			if(empty($sku_id[1])) { //普通减库存
				if($p['quantity'] < $quantity) {
					$this->error_str = '木有库存了';
					return false;
				}
				$ret = $this->product_model->where('id = '.$p['id'].' && quantity >= '.$quantity)->save(array('sell_cnt'=>($p['sell_cnt']+1),'quantity'=>($p['quantity']-$quantity)));
			}
			else {
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
				$ret = $this->product_model
					->where('id = '.$p['id'].' and sku_table = "'.addslashes(json_encode($p['sku_table_ori'])).'"')
					->save(array('sku_table'=>json_encode($new),'sell_cnt'=>($p['sell_cnt']+$quantity)));
			}
		}while(!$ret);

		return true;
	}

	/*
	 * 删除订单
	 */
	public function delete_order($ids)
	{
		$order = $this->order_model->get_order_by_id($ids);
		if ($order['status'] == ShopOrderModel::ORDER_WAIT_USER_PAY) {
			$this->cancal_order($order);
		}
		if (!in_array($order['status'], array(ShopOrderModel::ORDER_CANCELED, ShopOrderModel::ORDER_WAIT_USER_PAY))) {
			$this->error_str = '只有取消或未付款的订单可以删除';
			return false;
		}
		/*
		 * 使用的优惠券 返回原来状态
		 */
		if(($id = $this->user_coupon_logic->where('order_id = '.$order['id'].' and user_id = '.$order['user_id'])->find()))
		{
			$this->user_coupon_logic->where('id = '.$id)->save(array('order_id'=>0));
		}

		$ret = $this->order_model->delete($order['id']);


		return $ret;
	}

	/*
		计算运费
		$d 运费模板参数, 也可以是运费模板id
		$address 送货地址  'address' => array(
							'province' => '广东省',
							'city' => '深圳市',
							'town' => '南山区',

							'delivery' => '' //运送方式, 可以为空,或 express ems mail self 自提
							)
		$goods 货物信息 array(
							'count' => 2
						)

	*/
	public  function calc_delivery_fee($d, $address, $goods) {
		if(!$d || (is_numeric($d) && !($d = $this->delivery_model->get_delivery_by_id($d)))) {
			return 0;
		}

		//如果address中没有指定运送方式则以规则第一个为准,快递
		$way = isset($address['delivery']) ? $address['delivery'] : '';
		if(!isset($d['rule'][$way])) $way = key($d['rule']);

		switch($d['valuation']) {
			//固定运费
			case 0: {
				return $d['rule'][$way];
			}

			//计件
			case 1:
			default: {
				if(!empty($d['rule'][$way]['customer'])) {
					$rule = $this->array_usearch($address, $d['rule'][$way]['customer'], function($a, $as){
						foreach($as['location'] as $l) {
							if(isset($l['city'])) {
								if(($l['city'] == $a['city']) && ($l['province'] == $a['province'])) {
									return true;
								}
							}
							else {
								//var_export($l);
								//var_export($a);
								if(($l['province'] == $a['province'])) {
									return true;
								}
							}
						}
						return false;
					});
				}
				if(empty($rule)) {
					$rule = $d['rule'][$way]['normal'];
				}

				return $goods['count'] <= $rule['start'] ? $rule['start_fee'] :
					($rule['start_fee'] + ceil((float)($goods['count'] - $rule['start'])/$rule['add']) * $rule['add_fee']);
			}
		}
	}

	/*
	类似array_search, 可以自定义比较函数
	@param mix $needle
	@param array $haystack 在此数组中搜索
	@param function $comparator

	@return 搜索到的值或false
*/
	private function array_usearch($needle, $haystack, $comparator) {
		foreach($haystack as $h) {
			if($comparator($needle, $h)) {
				return $h;
			}
		}
		return false;
	}

	/*
		邮费预览, 多个商品
		$products = array(
			array(
			'id' => 商品id
			'count' => 商品件数
			),
		)

		$address = array(
			'province' =>
			'city' =>
		)

		返回 array(
		)
	*/
	public  function precalc_delivery($products, $address) {
		$dps = array(); //计算运费
		foreach($products as $p) {
			if(!($sp = $this->product_model->get_product_by_sku_id($p['id']))) {
				$this->error_str='系统错误';
				return false;
			}
			if(isset($dps[$sp['delivery_id']])) {
				$dps[$sp['delivery_id']]['count'] += $p['count'];
			}
			else {
				$dps[$sp['delivery_id']] = array('count' => $p['count']);
			}
		}

		$ret = array();
		foreach($dps as $d => $goods) {
			if(!$d || !($d = $this->delivery_model->get_delivery_by_id($d)) || empty($d['rule'])) {
				$k = 'free';
				if(isset($ret[$k])) {
					$ret[$k] = array('fee' => 0, 'cnt' => $ret[$k]+1);
				}
				else {
					$ret[$k] = array('fee' => 0, 'cnt' => 1);
				}
				continue;
			}
			foreach(array_keys($d['rule']) as $k) {
				$address['delivery'] = $k;
				$fee = $this->calc_delivery_fee($d, $address, $goods);
				if(isset($ret[$k])) {
					$ret[$k] = array('fee' => $ret[$k]['fee'] + $fee, 'cnt' => $ret[$k]['cnt'] + 1);
				}
				else {
					$ret[$k] = array('fee' => $fee, 'cnt' => 1);
				}
			}
		}

		uasort($ret, function($a, $b) {
			return $a['cnt'] == $b['cnt'] ? 0 : ($a['cnt'] > $b['cnt'] ? 1 : -1);
		});
		$cnt = count($dps);
		foreach($ret as $k => $v) {
			$ret[$k] = $ret[$k]['fee'];
		}
		return $ret;
	}


	/*
		邮费预览
		$goods = array(
			'id' => 商品id
			'count' => 商品件数
		)

		返回 array(
		)
	*/
	public  function preview_delivery($goods, $address) {
		;
		if(!($d = $d = $this->product_model->where('id='.$goods['id'])->fidld('delivery_id')->find()) ||
			!($d = $this->delivery_model->get_delivery_by_id($d)) ||
			empty($d['rule'])) {
			return (array('free' => 0));
		}

		$ret = array();
		foreach(array_keys($d['rule']) as $k) {
			$address['delivery'] = $k;
			$ret[$k] = $this->calc_delivery_fee($d, $address, $goods);
		}

		return $ret;
	}
	
	/*
	 * 增加商品评论
	 */
	public  function add_product_comment($product_comments)
	{
		$this->startTrans();
		foreach($product_comments as $product_comment)
		{
			if(($ret = $this->product_comment_model->get_product_comment_by_may($product_comment)))
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
			if(!$this->product_comment_model->add_or_edit_product_comment($product_comment))
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
		//更改订单zhuangt
		$may = array(
			'id'=>$product_comments[0]['order_id'],
			'user_id'=>$product_comments[0]['user_id'],
			'status' => ShopOrderModel::ORDER_DELIVERY_OK
			);
		if(!$this->order_model->where($may)->save(array('status' => ShopOrderModel::ORDER_COMMENT_OK)))
		{
			$this->error_str = '订单状态更新失败';
			$this->rollback();
			return false;
		}

		$this->commit();
		return  true;
	}

	/*
	 * 修改评论
	 */
	public  function edit_product_comment()
	{

	}


	public function BeforePayOrder($order_id,$user_id,$mp_id)
	{
		$shop_order = $this->order_model->get_order_by_id($order_id);
		if(empty($shop_order)){
			$this->error_str = '订单不存在';
			return false;
		}
		if($shop_order['status']!=ShopOrderModel::ORDER_WAIT_USER_PAY || $shop_order['paid_fee'] ==0)
		{
			$this->error_str = '订单不能被支付';
			return false;
		}

		$order = D("Order"); // 实例化order对象
		$odata = $order->where('aim_id = '.$order_id.' and module = "'.MODULE_NAME.'"')->find();
		if(empty($odata))
		{
			$odata['mp_id'] = $mp_id;
			$odata['uid']   = $user_id;
			$odata['order_id'] = $shop_order['id'];                                     //需要传给微信
			$odata['order_total_price'] = $shop_order['paid_fee'];                      //订单总价，单位：分 需要传给微信
			foreach($shop_order['products'] as $product)
			{
				$sku_id     = explode(';', $product['sku_id'], 2);
				$product_id = $sku_id[0];
				//			$oproduct    = $this->product_model->get_product_by_id($product_id);
				$odata['product_id'] = (empty($odata['product_id'])?$product_id:($odata['product_id'].';'.$product_id)) ;

				$odata['product_name'] =(empty($odata['product_name'])?$product['title']:$odata['product_name'].';'.$product['title']) ;                       //需要传给微信

				$odata['product_price'] =(empty($odata['product_price'])?$product['paid_price']:($odata['product_price']+$product['paid_price'])) ;                        //商品价格，单位：分

				$odata['product_sku'] =(empty($odata['product_sku'])?$product['sku_id']:($odata['product_sku'].'&&'.$product['sku_id'])) ;                                 //需要传给微信
				$odata['product_count'] = (empty($odata['product_count'])?$product['quantity']:($odata['product_count']+$product['quantity']));

			}
			$odata['module'] = MODULE_NAME;
			$odata['model'] = 'shop_order';
			$odata['aim_id'] = $shop_order['id'];
			do{
				$odata['order_id']      = 'time'. date("YmdHis");
				$odata = $order->create($odata); // 生成数据对象
			}
			while($order->getError()==-1);//保证 order_id唯一
			$order->add(); // 写入数据
		}
		return $odata;
	}
}

