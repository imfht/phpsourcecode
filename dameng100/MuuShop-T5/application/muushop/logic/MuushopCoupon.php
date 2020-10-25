<?php
namespace app\muushop\logic;

use think\Model;
/*
 * 优惠券逻辑层
 */
class MuushopCoupon extends Model{

	public $error_str='';

	function _initialize()
	{
		parent::_initialize();
	}
	/*
	计算优惠劵信息
	可以实现现金劵, 折扣劵, 满就减, 几件包邮等

	$coupon_id 优惠劵 id
	$order_info = array(
			'uid' => 顾客id
			'opaid_fee' => 商品总价
			'odelivery_fee' => 邮费
	)
	*/
	public function calcCouponFee($coupon_id, $order_info) {
		if(is_numeric($coupon_id)) {
			$coupon = model('muushop/MuushopUserCoupon')->getDataById($coupon_id);
		}
		if(!$coupon){
			$this->error_str = '优惠券不存在';
			return 0;
		}
		if($coupon['order_id']!=0 || (($coupon['expire_time'] < time()) && ($coupon['expire_time']!=0))) {
			$this->error_str = '优惠券已过期或已使用过';
			return 0;
		}

		if($coupon['uid'] != $order_info['uid']) {
			$this->error_str = '这张优惠券不属于您';
			return 0;
		}

		if(isset($coupon['min_price']) && $coupon['min_price']>$order_info['opaid_fee']){
			$this->error_str = '消费未满优惠券使用最低价格';
			return 0;
		}
		switch($coupon['info']['valuation']) {
			case 0: { //现金劵
				$ret = $coupon['discount'];
				break;
			}
			
			default :
				return 0;
		}

		return $ret;
	}
	
	/**
	 * 发放一张优惠劵给uid
	 */
	public function addCouponToUser($coupon_id, $uid) {

		$coupon = model('muushop/MuushopCoupon')->getDataById($coupon_id);
		if(!$coupon || ($coupon['publish_cnt'] && ($coupon['used_cnt'] >= $coupon['publish_cnt']))) {
			$this->error_str = '优惠券选择出错或已经没有剩余';
			return false;
		}
		//用户领取次数检查
		if(!empty($coupon['rule']['max_cnt'])) {
			$map['uid'] = $uid;
			$map['coupon_id'] = $coupon['id'];
			$max_cnt = model('muushop/MuushopUserCoupon')->where($map)->count();
			unset($map);
			if($max_cnt >= $coupon['rule']['max_cnt']) {
				$this->error_str ='超过可领取总次数';
				return false;
			}
			
		}

		//用户每日领取次数检查
		if(!empty($coupon['rule']['max_cnt_day'])) {
			$map['uid'] = $uid;
			$map['coupon_id'] = $coupon['id'];
			$map['create_time'] = [['>=',strtotime('today')],['<',strtotime('tomorrow')],'and'];
			
			$max_cnt_day = model('muushop/MuushopUserCoupon')->where($map)->count();
			unset($map);
			if($max_cnt_day >= $coupon['rule']['max_cnt_day']) {
				$this->error_str ='今天已超过可领取次数';
				return false;
			}
		}
		$this->startTrans();
		$incMap['id'] = $coupon['id'];
		$incMap['used_cnt'] = ['<',$coupon['publish_cnt']];
		$ret = model('muushop/MuushopCoupon')->where($incMap)->setinc('used_cnt');
		if(!$ret){
			$this->error_str ='领取时发生错误';
			$this->rollback();
			return false;
		}

		//最小使用金额
		$min_price = 0;
		if(isset($coupon['rule']['min_price']) && $coupon['rule']['min_price']>0) {
			$min_price = $coupon['rule']['min_price'];
		}
		
		$insert = [
            'uid' => $uid,
            'expire_time' => $coupon['expire_time'],
            'order_id' => 0,
            'coupon_id' => $coupon['id'],
            'info' => json_encode([
            	'title' => $coupon['title'],
                'img' => $coupon['img'],
                'valuation' => $coupon['valuation'],
			]),
			'min_price' => $min_price,
            'discount' => $coupon['rule']['discount']
		];
		$ret = model('muushop/MuushopUserCoupon')->save($insert);
		if(!$ret){
			$this->rollback();
			return false;
		}
		$this->commit();

		//todo 可以发个通知消息给用户
		return true;
	}

	/*
	 * 标记优惠券已读
	 */
	public function doReadUserCoupon($ids,$uid)
	{
		is_array($ids) || $ids = array($ids);
		$map['id'] = ['in',implode(',',$ids)];
		$map['uid'] = $uid;
		return model('muushop/MuushopUserCoupon')->where($map)->save(['read_time'=>time()]);
	}
}

