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

class ShopCart extends Model{
	protected $tableName='shop_cart';
	protected $_validate = array(
		array('sku_id','/^(\d+[\x{4e00}-\x{9fa5}\w:;]*)$/u','sku_id格式错误',3),
		array('sku_id','','你已经添加过了',3,'unique'),
		array('quantity','/^[1-9]\d*$/','数量错误',3),
	);
	protected $_auto = array(
		array('create_time', NOW_TIME, 1),
	);

	public function add_shop_cart($shop_cart)
	{
		if(empty($shop_cart['id']))
		{
			$ret = $this->add($shop_cart);
		}
		else
		{
			//			$this->create();
			$ret = $this->where('id='.$shop_cart['id'])->save($shop_cart);
		}
		return $ret;
	}

	public function delete_shop_cart($ids,$user_id)
	{
		if(!is_array($ids))
		{
			$ids = array($ids);
		}
		$ret = $this->where('id in ('.implode(',',$ids).') and user_id='.$user_id)->delete();
		return $ret;
	}


	public function get_shop_cart_by_user_id($user_id)
	{
		return $this->where('user_id = '.$user_id)->select();
	}

	public function get_shop_cart_by_ids($ids,$user_id)
	{
		if(!is_array($ids))
		{
			$ids = array($ids);
		}
		return $this->where('id in ('.implode(',',$ids).') and user_id = '.$user_id)->select();
	}

	public function _after_select(&$ret,&$option)
	{
		empty($ret) ||
		array_walk($ret, function (&$a)
		{
			$a['product'] = D('Shop/ShopProduct')->get_product_by_sku_id($a['sku_id']);
		});

	}


}

