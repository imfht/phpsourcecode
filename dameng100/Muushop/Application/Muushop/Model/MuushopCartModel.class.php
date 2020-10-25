<?php

namespace Muushop\Model;
use Think\Model;

class MuushopCartModel extends Model{
	protected $tableName='muushop_cart';
	protected $_validate = array(
		//array('sku_id','/^(\d+[^\u4e00-\u9fa5\w:;]*)$/u','sku_id格式错误',3),
		array('sku_id','/^([0-9]+)/','sku_id格式错误',3),
		array('sku_id','','你已经添加过了',3,'unique'),
		array('quantity','/^[1-9]\d*$/','数量错误',3),
	);
	protected $_auto = array(
		array('create_time', NOW_TIME, 1),
	);

	public function add_shop_cart($shop_cart)
	{
		if(empty($shop_cart['id'])){
			$ret = $this->add($shop_cart);
		}else{
			$ret = $this->where('id='.$shop_cart['id'])->save($shop_cart);
		}
		return $ret;
	}

	public function delete_shop_cart($ids,$user_id)
	{
		!is_array($ids)&&$ids=explode(',',$ids);
		$map['id']=array('in',$ids);
		$map['uid'] = $user_id;
		$ret = $this->where($map)->delete();
		return $ret;
	}



	public function get_shop_cart_by_user_id($user_id)
	{
		$map['user_id']=$user_id;
		return $this->where($map)->select();
	}

	public function get_shop_cart_count_by_user_id($user_id)
	{
		return $this->where('user_id = '.$user_id)->count();
	}

	public function get_shop_cart_by_ids($ids,$user_id)
	{
		!is_array($ids)&&$ids=explode(',',$ids);
		$map['id']=array('in',$ids);
		$map['user_id'] = $user_id;
		$ret = $this->where($map)->select();
		return $ret;
	}

	public function _after_select(&$ret,&$option)
	{
		empty($ret) || array_walk($ret, function (&$a)
		{
			$a['product'] = D('Muushop/MuushopProduct')->get_product_by_sku_id($a['sku_id']);
		});
	}
}

