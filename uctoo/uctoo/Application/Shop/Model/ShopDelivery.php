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

class ShopDelivery extends Model{
	protected $tableName = 'shop_delivery';
	protected $_validate = array(
		array('title', '1,32', '模板名称长度不对', 1, 'length'),
		array('brief', '0,256', '模板说明长度不对', 1, 'length'),

	);
	protected $_auto     = array(
		array('create_time', NOW_TIME, self::MODEL_INSERT),
	);

	public function add_or_edit_delivery($delivery)
	{
		if(empty($delivery['id']))
		{
			$ret = $this->add($delivery);
		}
		else
		{
			$ret = $this->where('id='.$delivery['id'])->save($delivery);
		}
		return $ret;
	}

	public function delete_delivery($ids){
		if(!is_array($ids))
		{
			$ids = array($ids);
		}
		$ret = $this->where('id in ('.implode(',',$ids).')')->delete();
		return $ret;
	}

	public function get_delivery_list($option)
	{
		$ret['list'] = $this->order('create_time')->page($option['page'],$option['r'])->select();
		$ret['count'] = $this->count();
		//获取父级分类信息
		if(!empty($ret['list'])) {
			foreach($ret['list'] as $k => $c) {
				$ret['list'][$k] = $this->func_get_delivery($c);
			}
		}
		return $ret;
	}

	public function get_delivery_by_id($id){
		$ret = $this->where('id= '.$id)->find();
		$ret = $this->func_get_delivery($ret);
		return $ret;
	}

	/*
	 * 处理数据库返回数据
	 */
	public function func_get_delivery($item)
	{

		if(!empty($item['rule']))
		{
			$item['rule'] = json_decode($item['rule'], true);
		}
		return $item;
	}


}

