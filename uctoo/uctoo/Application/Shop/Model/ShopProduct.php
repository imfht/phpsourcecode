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

 //define('NOW_TIME',input('server.REQUEST_TIME'));
class ShopProduct extends Model {
	protected $tableName = 'shop_product';
	protected $_validate = array(
		array('title', '1,64', '分类标题长度不对', 1, 'length'), //默认情况下用正则进行验证
		array('cat_id','0','请选择分类',1,'notequal'),
	);
	protected $_auto     = array(
		array('create_time', NOW_TIME, self::MODEL_INSERT),
		array('modify_time', NOW_TIME, self::MODEL_BOTH),

	);


	/*
	 * 编辑商品
	 */
	public function add_or_edit_product($product)
	{
		if(empty($product['id']))
		{
			$ret = $this->add($product);
		}
		else
		{
//			$this->create();
			$ret = $this->where('id='.$product['id'])->save($product);
		}
		return $ret;
	}

	/*
	 * 删除商品
	 */
	public function delete_product($ids)
	{
		if(!is_array($ids))
		{
			$ids = array($ids);
		}
		$ret = $this->where('id in ('.implode(',',$ids).')')->delete();
		return true;

	}

	/*
	 * 获取商品信息
	 */
	public function get_product_by_id($id)
	{
		$ret = $this->where('id = '.$id)->find();

		return $ret;
	}

	public function get_product_list($option)
	{
		if(isset($option['cat_id']) && $option['cat_id'] >= 0) {
			$where_arr[] = 'cat_id = '.$option['cat_id'];
		}
		if(isset($option['status'])) {
			$where_arr[] = 'status = '.$option['status'];
		}
		$where_str ='';
		if(!empty($where_arr)) {
			$where_str .= implode(' and ', $where_arr);
		}
		$ret['list'] = $this->where($where_str)->order('sort desc, create_time')->page($option['page'],$option['r'])->field('content,sku_table,location,delivery_id',true)->select();
		$ret['count'] = $this->where($where_str)->count();

		return $ret;
	}


	/*
	 * 通过sku_id 获取商品
	 */
	public function get_product_by_sku_id($sku_id)
	{
		$sku_id = explode(';', $sku_id, 2);
		$product_id = $sku_id[0];

		$where_arr[] = 'id = '.$product_id.'';
		$where_str ='';
		if(!empty($where_arr)) {
			$where_str .= implode(' and ', $where_arr);
		}
		$ret = $this->where($where_str)->find();
		$ret['quantity_total'] = $ret['quantity'];
		if(!empty($sku_id[1]) && !empty($ret['sku_table']['info'][$sku_id[1]])) {
			$ret = array_merge($ret, $ret['sku_table']['info'][$sku_id[1]]);
		}
		unset($ret['sku_table']);
		$ret['sku_id'] = $sku_id;
		return $ret;
	}

	protected function _after_find(&$ret,$option)
	{
		if(!empty($ret['sku_table'])) $ret['sku_table'] = json_decode($ret['sku_table'],true);
	}

	protected function _after_select(&$ret,$option)
	{
		if(!empty($ret['sku_table'])) $ret['sku_table'] = json_decode($ret['sku_table'],true);
	}


	/*
	 * 取某个分类、某几个分类下所有分类的商品id
	 */
	public function get_all_product_id_by_cat_id($cat_id)
	{
		is_array($cat_id) || $cat_id = array($cat_id);
		$ret = $this->where('cat_id in ('.implode(',',$cat_id).')')->field('id')->select();
		is_array($ret) && $ret = array_column($ret,'id');
		return $ret;
	}
}

