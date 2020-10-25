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

class ShopProductSell extends Model {
	protected $tableName = 'shop_product_sell';
	/*
	 * 增加成交记录
	 */
	public function add_sell_record($sell_record)
	{
		return $this->add($sell_record);
	}

	/*
	 * 删除成交记录
	 */
	public function delete_sell_record($ids)
	{
		if(!is_array($ids))
		{
			$ids = array($ids);
		}
		return $this->where('id in ('.implode(',',$ids).')')->delete();
	}

	/*
	 * 获取某次交易记录
	 */
	public function get_sell_record_by_id($id)
	{

	}

	/*
	 * 获取商品成交记录
	 */
	public function get_sell_record($option)
	{
		if(!empty($option['product_id'])) {
			$where_arr[] = 'product_id = '.$option['product_id'];
		}
		if(!empty($option['user_id'])) {
			$where_arr[] = 'user_id = '.$option['user_id'];
		}
		if(!empty($option['min_time'])) {
			$where_arr[] = 'create_time >= '.$option['min_time'];
		}
		$where_str ='';
		if(!empty($where_arr)) {
			$where_str .= ' where '.implode(' and ', $where_arr);
		}
		$ret['list'] = $this->where($where_str)->order('create_time desc')->page($option['page'],$option['r'])->select();
		$ret['count'] = $this->where($where_str)->count();
		return $ret;
	}

	public function get_sell_record_by_may($option,$may = array('order_id','product_id','user_id'))
	{
		$where_str = '';
		foreach($may as $v)
		{
			if(isset($option[$v]))
			{
				$where_arr[] = $v.'= ' . $option[$v];
			}
		}
		empty($where_arr) || $where_str .= implode(' and ', $where_arr);
		return  $this->where($where_str)->find();
	}

}

