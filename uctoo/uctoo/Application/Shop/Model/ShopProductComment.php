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

class ShopProductComment extends Model{
	//用户收货地址
	protected $tableName='shop_product_comment';
	protected $_validate = array(
		array('brief','0,255','留言长度不对',Model::MUST_VALIDATE,'length'),
		array('score','0,10','评星非法',Model::MUST_VALIDATE,'between'),
		array('images','0,256','图片信息有误',Model::MUST_VALIDATE,'length'),
		array('brief','0,256','评论长度有误',Model::MUST_VALIDATE,'length'),
		array('id','/[1-9]\d*/','id参数错误',Model::EXISTS_VALIDATE),
		array('product_id','/[1-9]\d*/','product_id参数错误',Model::MUST_VALIDATE),
		array('order_id','/[1-9]\d*/','order_id参数错误',Model::MUST_VALIDATE),
		array('parent_id','/[0-9]\d*/','parent_id参数错误',Model::EXISTS_VALIDATE),
		array('user_id','/[0-9]\d*/','user_id参数错误',Model::MUST_VALIDATE),
		array('sku_id','/^(\d+[\x{4e00}-\x{9fa5}\w:;]*)$/u','sku_id参数错误',Model::MUST_VALIDATE),
	);
	protected $_auto = array(
		array('create_time', NOW_TIME, Model::MODEL_INSERT),
		array('status', 1, Model::MODEL_INSERT),//默认通过审核
	);

	public function edit_status_product_comment($ids,$status)
	{
		is_array($ids) ||
		$ids = explode(',',$ids);
		return $this->where('id in ('.implode(',',$ids).')')->save(array('status'=>$status));

	}

	public function add_or_edit_product_comment($product_comment)
	{
		if(!empty($product_comment['id']))
		{
			return	$this->save($product_comment);
		}
		else
		{
			return	$this->add($product_comment);
		}
	}

	public function get_product_comment_list($option)
	{
		$where_str = '';
		isset($option['status']  ) && ($option['status'] >= 0) && $where_arr[] = 'status = ' . $option['status'];
		empty($option['user_id']) || $where_arr[] = 'user_id = ' . $option['user_id'];
		empty($option['id']) || $where_arr[] = 'id = ' . $option['id'];
		empty($option['product_id']) || $where_arr[] = 'product_id = ' . $option['product_id'];
		empty($where_arr) || $where_str .= implode(' and ', $where_arr);

		$order_str = '';
		$order_arr[] = (empty($order_arr) ? 'create_time desc' : $order_arr);
		$order_str .= implode(' , ', $order_arr);

		$option['page'] = (empty($option['page']) ? 1 : $option['page']);
		$option['r']    = (empty($option['r']) ? 10 : $option['r']);

		$ret['list']  = $this->where($where_str)->order($order_str)->page($option['page'], $option['r'])->select();
		$ret['count'] = $this->where($where_str)->count();

		return $ret;
	}

	public function get_product_comment_by_may($option,$may = array('order_id','user_id','sku_id'))
	{
		$where_str = '';
		foreach($may as $v)
		{
			if(isset($option[$v]))
			{
				$where_arr[] = $v.'= "' . $option[$v].'"';
			}
		}
		empty($where_arr) || $where_str .= implode(' and ', $where_arr);
		$ret = $this->where($where_str)->find();
		return  $ret;
	}

	protected function _after_select(&$ret,$option)
	{
		empty($ret) ||
		array_walk($ret,function(&$a)
		{
			$this->func_get_product_comment($a);
		});

	}

	protected function _after_find(&$ret,$option)
	{
		$this->func_get_product_comment($ret);
	}

	public function func_get_product_comment(&$item)
	{
		empty($item['user_id']) || $item['user']=query_user(array('nickname','avatar32'),$item['user_id']);
		empty($item['images']) || $item['images']=explode(';',$item['images']);
	}
}

