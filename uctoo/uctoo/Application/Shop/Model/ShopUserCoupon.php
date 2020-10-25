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
namespace app\shop\model;
 use think\Model;

class ShopUserCoupon extends Model{
	//用户收货地址
	protected $tableName='shop_user_coupon';
	protected $_validate = array(
		array('brief','0,255','留言长度不对',1,'length'),
		array('title','1,32','优惠券名称长度不对',3,'length'),
	);
	protected $_auto = array(
		array('create_time', NOW_TIME, 1),
		array('duration', 0, 1),
		array('order_id', 0, 1),
		array('publish_cnt', 0, 1),
	);

	public function add_or_edit_user_coupon($coupon)
	{
		if(!empty($coupon['id']))
		{
			$ret = $this->where($coupon)->save();
		}
		else
		{
			$ret = $this->add($coupon);
		}
		return $ret;
	}

	public function delete_user_coupon($ids)
	{
		return $this->where('id in ('.implode(',',$ids).')')->delete();
	}

	/*
		用户优惠劵列表
	*/
	public function get_user_coupon_list($option)
	{
		if(!empty($option['id'])) {
			$where_arr[] = 'id= '.$option['id'];
		}
		if(!empty($option['user_id'])) {
			$where_arr[] = 'user_id= '.$option['user_id'];
		}
		if(!empty($option['available'])) {
			$where_arr[] = 'order_id = 0 && (expire_time = 0 || expire_time >= '.$_SERVER['REQUEST_TIME'].')';
		}

		if(!empty($option['unread'])) {
			$where_arr[] = 'read_time= 0';
		}

		$where_str = '';
		if (!empty($where_arr))
		{
			$where_str .=  implode(' and ', $where_arr);
		}
		$ret['list']  = $this->where($where_str)->order('id desc')->page($option['page'], $option['r'])->select();
		empty($ret['list']) ||
		array_walk($ret['list'],
			function(&$a){
				$this->func_get_user_coupon($a);
			});

		$ret['count'] = $this->where($where_str)->count();
		return $ret;
	}

	public function get_user_coupon_by_id($id)
	{
		$ret = $this->where('id='.$id)->find($id);
		$ret = $this->func_get_user_coupon($ret);
		return $ret;
	}

	public function func_get_user_coupon(&$item)
	{
		if(!empty($item['info'])) $item['info'] = json_decode($item['info'], true);
		if(!(empty($GLOBALS['_TMP']['paid_fee']))
			&& !empty($item['info']['rule']['min_price'])
			&& $GLOBALS['_TMP']['paid_fee']<$item['info']['rule']['min_price'] )
		{
			$item['out_limit_price'] = true;
		}
		if( empty($item['order_id']) && $item['expire_time']!=0 && $item['expire_time']<time())
		{
			$item['status'] = 2;
		}
		else
		{
			$item['status'] = (empty($item['order_id'])?0:1);
		}

	}

}

