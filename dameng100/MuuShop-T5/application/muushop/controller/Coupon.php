<?php
namespace app\muushop\controller;

use think\Controller;

class Coupon extends Base {

	protected $coupon_model;
	protected $coupon_logic;
	protected $user_coupon_model;


	function _initialize()
	{
		parent::_initialize();
		$this->coupon_model       = model('muushop/MuushopCoupon');
		$this->user_coupon_model  = model('muushop/MuushopUserCoupon');
		$this->coupon_logic       = model('muushop/MuushopCoupon', 'logic');
	}

	/*
	 * 可领优惠券列表
	 */
	public function index()
	{
		$map['expire_time'] = ['>',time()];
		$coupon = $this->coupon_model->getListByPage($map);
		$page = $coupon->render();
		$coupon_arr = $coupon->toArray()['data'];
		
		foreach($coupon_arr as &$val){
			$val['rule']['discount'] = sprintf("%.2f",$val['rule']['discount']/100);
			if(isset($val['rule']['min_price'])) {
				$val['rule']['min_price'] = sprintf("%.2f",$val['rule']['min_price']/100);
			}
		}
		unset($val);
		$this->assign('coupon', $coupon_arr);
		$this->assign('page',$page);
		return $this->fetch();
	}
}