<?php
namespace addons\common\coupon\model;

use think\Model;

class AddonsCommonCouponUserLog extends Model
{
	protected $resultSetType = 'collection';
	public function user_exist($user_id){
		// 如果用户不存在，则添加
		$is_exist = x_model('AddonsCommonCouponUserLog')->where(['user_id'=>$user_id])->find();
		if(!$is_exist){
			x_model('AddonsCommonCouponUserLog')->create(['user_id'=>$user_id,'date'=>date('Y-m-d')]);
		}
	}
	public function left_times($user_id){
		$allow_times = x_model('AddonsCommonCouponActiveConfig')->field('total_times,day_times')->find(1);
		$use_times	 = x_model('AddonsCommonCouponUserLog')->field('total_use,day_use')->where(['user_id'=>$user_id])->find();
		$day_left 	 = $allow_times['day_times']-$use_times['day_use'];
		$total_left  = $allow_times['total_times']-$use_times['total_use'];
		return min($day_left,$total_left);
	}
}