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

class ShopProductExtraInfo extends Model {
	protected $tableName = 'shop_product_extra_info';
	protected $_validate = array(
		array('ukey', '1,32', '参数名长度不对', 1, 'length'), //默认情况下用正则进行验证
		array('data', '1,512', '参数值长度不对', 1, 'length'), //默认情况下用正则进行验证
	);
	protected $_auto     = array(
	);

	public function add_or_edit_product_extra_info($pei)
	{
		$id =$this->where('product_id = ' .$pei['product_uid'].' && ukey = "'.addslashes($pei['ukey']).'"')->field('id')->find();
		if($id)
		{
			$ret = $this->where('id='.$id)->save($pei);
		}
		else{
			$ret = $this->add($pei);
		}
		return $ret;
	}

	public function delete_product_extra_info($product_id,$unkey)
	{
		return $this->where('product_id = '. $product_id.' && ukey = "'.addslashes($unkey).'"')->delete();
	}

	public function get_product_extra_info($product_id)
	{
		return $this->where('product_id ='.$product_id)->order('sort desc')->select();
	}

	public function get_shop_product_extra_info(){
		if(!($all = $this->order('sort desc')->select())) {
			return array();
		}
		$ret = array();
		foreach($all as $a) {
			if(!isset($ret[$a['ukey']]) || !in_array($a['data'], $ret[$a['ukey']])) {
				$ret[$a['ukey']][] = $a['data'];
			}
		}
		return $ret;
	}

}

