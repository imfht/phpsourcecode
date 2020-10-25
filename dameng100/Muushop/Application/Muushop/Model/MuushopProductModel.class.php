<?php

namespace Muushop\Model;
use Think\Model;

class MuushopProductModel extends Model {
	protected $tableName = 'muushop_product';
	protected $_validate = array(
		array('title', '1,64', '分类标题长度不对', 1, 'length'), //默认情况下用正则进行验证
		array('cat_id','0','请选择分类',1,'notequal'),
	);
	protected $_auto     = array(
		array('create_time', NOW_TIME, self::MODEL_INSERT),
		array('modify_time', NOW_TIME, self::MODEL_BOTH),

	);

	public function getListByPage($map,$page=1,$order='create_time desc',$field='*',$r=20)
    {	
        $totalCount=$this->where($map)->count();
        if($totalCount){
            $list=$this->where($map)->page($page,$r)->order($order)->field($field)->select();
        }
        return array($list,$totalCount);
    }

    public function getList($map,$order='click_cnt desc',$limit=8,$field='*')
    {
        $lists = $this->where($map)->order($order)->limit($limit)->field($field)->select();
        return $lists;
    }

	/*
	 * 编辑商品
	 */
	public function edit_product($product)
	{
		if(empty($product['id'])){
			$ret = $this->add($product);
		}else{
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
		//$ret['quantity_total'] = $ret['quantity'];
		if(!empty($sku_id[1]) && !empty($ret['sku_table']['info'][$sku_id[1]])) {
			$ret = array_merge($ret, $ret['sku_table']['info'][$sku_id[1]]);
		}
		unset($ret['sku_table']);
		$ret['sku_id'] = $sku_id;
		return $ret;
	}

	/**
     * 获取推荐位数据列表
     * @param $pos 推荐位ID 如：1-热卖，2-推荐，4-新品
     * @param null $category
     * @param $limit
     * @param bool $field
     * @return mixed
     * @author 大蒙<59262424@qq.com>
     */
    public function position($pos, $cat_id = null, $limit = 5,$order='create_time desc,view desc'){

    	$map = array();
    	if($pos){
    		$map['_string']='FIND_IN_SET('.$pos.',position)';	
    	}
    	if($category){
    		$map['cat_id'] = $cat_id;
    	}
        
        $res=$this->where($map)->order($order)->limit($limit)->select();
        foreach($res as &$val){
        	$val['price'] = price_convert('yuan',$val['price']);
        	$val['ori_price'] = price_convert('yuan',$val['ori_price']);
        }
        /* 读取数据 */
        return $res;
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

