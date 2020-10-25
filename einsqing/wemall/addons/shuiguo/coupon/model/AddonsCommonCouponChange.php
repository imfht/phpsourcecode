<?php
namespace addons\common\coupon\model;

use think\Model;

class AddonsCommonCouponChange extends Model
{
	protected $resultSetType = 'collection';
	protected function getTypeAttr($value, $data)
    {
        $type = $data['type'];
        switch ($type)
		{
		case 1:
		  $type = '积分兑换';
		  break;  
		case 2:
		  $type = '手动发送';
		  break;
        case 3:
          $type = '用户获取';
          break;
		default:
		  $type = '未知来源';
		}
        return $type;
    }
    public function coupon()
    {
        return $this->hasOne('AddonsCommonCoupon','id','coupon_id');
    }
    public function user()
    {
        return $this->hasOne('app\common\model\User','id','user_id');
    }
}