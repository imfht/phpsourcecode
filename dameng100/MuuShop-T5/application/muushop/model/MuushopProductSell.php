<?php
namespace app\muushop\model;

use think\Model;

class MuushopProductSell extends Model {

	/*
	 * 增加成交记录
	 */
	public function addData($sell_record)
	{
		return $this->save($sell_record);
	}

	/*
	 * 删除成交记录
	 */
	public function deleteData($ids)
	{
		if(!is_array($ids)){
			$ids = array($ids);
		}
		$map['id'] = ['in',implode(',',$ids)];
		return $this->where($map)->delete();
	}
	/*
	 * 获取商品成交记录
	 */
	public function getDataByMap($option)
	{
		if(!empty($option['product_id'])) {
			$where_arr[] = 'product_id = '.$option['product_id'];
		}
		if(!empty($option['uid'])) {
			$where_arr[] = 'uid = '.$option['uid'];
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

	public function getDataByMay($option,$may = array('order_id','product_id','uid'))
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

