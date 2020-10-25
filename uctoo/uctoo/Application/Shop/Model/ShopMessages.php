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

class ShopMessages extends Model{
	//用户收货地址
	protected $tableName='shop_messages';
	protected $_validate = array(
		array('extra_info','0,255','其他信息长度不对',1,'length'),
		array('brief','0,255','留言长度不对',1,'length'),
	);
	protected $_auto = array(
		array('create_time', NOW_TIME, 1),
	);

	public function add_or_edit_shop_message($shop_message)
	{
		if(!empty($shop_message['id']))
		{
			$ret = $this->where('id = '.$shop_message['id'])->save();
		}
		else
		{
			$ret = $this->add($shop_message);
		}
		return $ret;
	}

	public function delete_shop_message($ids)
	{
		is_array($ids) || $ids = array($ids);
		return $this->where('id in ('.implode(',',$ids).')')->delete();
	}

	public function get_shop_message_list($option)
	{
		if(!empty($option['user_id'])) {
			$where_arr[] = 'user_id= '.$option['user_id'];
		}
		if(!empty($option['parent_id']) && $option['parent_id'] >= 0) {
			$where_arr[] = 'parent_id= '.$option['parent_id'];
		}
		else if(empty($option['all'])) { //通常只要用户列表
			$where_arr[] = 'parent_id = 0';
		}
		
		if(isset($option['status']) && ($option['status'] >= 0)) {
			$where_arr[] = 'status = '.$option['status'];
		}
		
		//搜索
		if(!empty($option['key'])) {
			$where_arr[] = '(brief like "%'.$option['key'].'%")';
		}
		$where_str = '';
		if (!empty($where_arr))
		{
			$where_str .=  implode(' and ', $where_arr);
		}
		$ret['list']  = $this->where($where_str)->page($option['page'], $option['r'])->select();
		$ret['count'] = $this->where($where_str)->count();
		return $ret;
	}

	public function get_shop_message_by_id($id)
	{
		$ret = $this->where('id = '.$id)->find();
		return $ret;
	}



}

