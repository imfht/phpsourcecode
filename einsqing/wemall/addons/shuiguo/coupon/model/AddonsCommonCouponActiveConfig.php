<?php
namespace addons\common\coupon\model;

use think\Model;

class AddonsCommonCouponActiveConfig extends Model
{
	protected $resultSetType = 'collection';
	public function file(){
		return $this->hasOne('app\common\model\File', 'id', 'header_img');
	}
}