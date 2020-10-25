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
 * 优惠券逻辑层
 */
class ShopCoupon extends Model{

	protected $product_cats_model;
	protected $product_model;
	protected $order_model;
	protected $delivery_model;
	protected $user_coupon_model;
	protected $coupon_model;
	public $error_str='';
	function _initialize()
	{
		$this->product_cats_model = D('Shop/ShopProductCats');
		$this->product_model = D('Shop/ShopProduct');
		$this->order_model = D('Shop/ShopOrder');
		$this->delivery_model = D('Shop/ShopDelivery');
		$this->coupon_model = D('Shop/ShopCoupon');
		$this->user_coupon_model = D('Shop/ShopUserCoupon');
		parent::_initialize();
	}
	/*
		计算优惠劵信息
		可以实现现金劵, 折扣劵, 满就减, 几件包邮等

		$coupon 优惠劵 id
		$order_info = array(
				'user_id' => 顾客id
				'opaid_fee' => 商品总价
				'odelivery_fee' => 邮费
				'products' => 商品列表
		)
	*/
	public function calc_coupon_fee($coupon, $order_info) {
		if(is_numeric($coupon)) $coupon = $this->user_coupon_model->get_user_coupon_by_id($coupon);
		if(!$coupon || ($coupon['user_id'] != $order_info['user_id']) ||
			($coupon['order_id'] || (($coupon['expire_time'] > 0) && ($coupon['expire_time'] < $_SERVER['REQUEST_TIME'])))) {
			$this->error_str = '优惠券已使用或过期';
			return 0;
		}
		if(isset($coupon['info']['rule']['min_price']) && $coupon['info']['rule']['min_price']>$order_info['opaid_fee'])
		{
			$this->error_str = '消费未满优惠券使用最低价格';
			return 0;
		}
		switch($coupon['info']['valuation']) {
			case 0: { //现金劵
				$ret = $coupon['info']['rule']['discount'];
				break;
			}
			
			default :
				return 0;
		}

		return $ret;
	}
	
	/*
		发放一张优惠劵给user_id
	*/
	public  function add_a_coupon_to_user($coupon_id, $user_id) {
		$coupon =$this->coupon_model->get_coupon_by_id($coupon_id);
		if(!$coupon || ($coupon['publish_cnt'] && ($coupon['used_cnt'] >= $coupon['publish_cnt']))) {
			$this->error_str = '优惠券选择出错或已经没有剩余';
			return false;
		}
		//用户领取次数检查
		if(!empty($coupon['rule']['max_cnt'])) {
			$max_cnt = $this->user_coupon_model->where('user_id = '.$user_id.' and coupon_id = '.$coupon['id'])->count();
			if($max_cnt >= $coupon['rule']['max_cnt']) {
				$this->error_str ='超过可领取总次数';
				return false;
			}
		}

		//用户每日领取次数检查
		if(!empty($coupon['rule']['max_cnt_day'])) {
			$where_str = 'user_id = '.$user_id.' and coupon_id = '.$coupon['id'].' && create_time >= '.strtotime('today').' && create_time < '.strtotime('tomorrow');
			$max_cnt_day = $this->user_coupon_model->where($where_str)->count();
			if($max_cnt_day >= $coupon['rule']['max_cnt_day']) {
				$this->error_str ='今天已超过可领取次数';
				return false;
			}
		}
		$this->startTrans();
		$ret=$this->coupon_model->where('id = '.$coupon['id'].' && (publish_cnt = 0 || used_cnt < publish_cnt)')->setinc('used_cnt');
		if(!$ret)
		{
			$this->rollback();
			return false;
		}
		$insert = array(
		                'user_id' => $user_id,
		                'create_time' => $_SERVER['REQUEST_TIME'],
		                'expire_time' => ($coupon['duration'] ? ($_SERVER['REQUEST_TIME'] + $coupon['duration']) : 0),
		                'order_id' => 0,
		                'coupon_id' => $coupon['id'],
		                'info' => json_encode(array('title' => $coupon['title'],
		                                            'img' => $coupon['img'],
		                                            'valuation' => $coupon['valuation'],
		                                            'rule' => $coupon['rule'],
		                )),
		);
		$this->user_coupon_model->add($insert);
		if(!$ret)
		{
			$this->rollback();
			return false;
		}
		$this->commit();


		//todo 可以发个通知消息给用户

		return $this->user_coupon_model->getLastInsID();
	}

	/*
	 * 标记优惠券已读
	 */
	public function do_read_user_coupon($ids,$user_id)
	{
		is_array($ids) || $ids = array($ids);
		return $this->user_coupon_model->where('id in ('.implode(',',$ids).') and user_id = '.$user_id)->save(array('read_time'=>time()));
	}
}

