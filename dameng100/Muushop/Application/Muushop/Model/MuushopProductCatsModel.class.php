<?php

namespace Muushop\Model;
use Think\Model;

class MuushopProductCatsModel extends Model{
	protected $tableName='muushop_product_cats';
	protected $_validate = array(
		array('title','1,64','分类名称长度不符',1,'length'),
		array('title_en','0,128','分类英文标题长度不符',2,'length'),

	);
	protected $_auto = array(
		array('create_time', NOW_TIME, self::MODEL_INSERT),
		array('status', '1', self::MODEL_INSERT),
	);
	/*
	 * 增加修改商品分类
	 */
	public function add_or_edit_product_cats($product_cats){
		if(empty($product_cats['id'])){
			
			$ret = $this->add();
		}else{
			$this->create();
			$ret = $this->where('id='.$product_cats['id'])->save();
		}
		return $ret;
	}
	/*
	 * 删除商品分类
	 */
	public function delete_product_cats($ids)
	{
		if(!is_array($ids)){
			$ids = array($ids);
		}
		$this->startTrans();
		$ret1 = $this->where('id in ('.implode(',',$ids).')')->delete();
		if($this->where('parent_id in ('.implode(',',$ids).')')->count()>0){
			$ret2 = $this->where('parent_id in  ('.implode(',',$ids).')')->save(array('parent_id'=>'0'));

		}else{
			$ret2=true;
		}
		$product_model = new ShopProductModel();
		if($product_model->where('cat_id in  ('.implode(',',$ids).')')->count()>0){

			$ret3 = $product_model->where('cat_id in  ('.implode(',',$ids).')')->save(array('cat_id'=>'0'));
		}else{
			$ret3 =true;
		}
		if($ret1 && $ret2 && $ret3){
			$this->commit();
			return true;
		}else{
			$this->rollback();
			return false;
		}
	}
	/*
	 * 获取分类信息
	 */
	public function get_product_cats($option)
	{
		$ret = S('product_cats');
		if(empty($ret)){
			if(isset($option['parent_id']) && $option['parent_id'] >= 0) {
				$where_arr[] = 'parent_id = '.$option['parent_id'];
			}
			if(isset($option['status'])) {
				$where_arr[] = 'status = '.$option['status'];
			}
			$where_str ='';
			if(!empty($where_arr)) {
				$where_str .= implode(' and ', $where_arr);
			}
			$ret['list'] = $this->where($where_str)->order('sort asc, create_time')->page($option['page'],$option['r'])->select();
			$ret['count'] = $this->where($where_str)->count();
			//获取父级分类信息
			if(!empty($option['with_parent_info']) && $ret['list']) {
				foreach($ret['list'] as $k => $c) {
					if($c['parent_id']) {
						$ret['list'][$k]['parent_cat'] = $this->get_product_cat_by_id($c['parent_id']);
					}
				}
			}
			S('product_cats',$ret,3600);
		}
		return $ret;	
	}
	/**
	 * 获取分类信息
	 * @param  [type]  $map   [description]
	 * @param  integer $page  [description]
	 * @param  string  $order [description]
	 * @param  string  $field [description]
	 * @param  integer $r     [description]
	 * @return [type]         [description]
	 */
	public function getListByPage($map,$page=1,$order='sort asc',$field='*',$r=20)
    {
        $totalCount=$this->where($map)->count();
        if($totalCount){
            $list=$this->where($map)->page($page,$r)->order($order)->field($field)->select();
        }
        return array($list,$totalCount);
    }
    /**
     * 获取分类列表
     * @param  [type]  $map   [description]
     * @param  string  $order [description]
     * @param  integer $limit [description]
     * @param  string  $field [description]
     * @return [type]         [description]
     */
    public function getList($map,$order='sort asc',$field='*')
    {
        $lists = $this->where($map)->order($order)->field($field)->select();
        return $lists;
    }

	/*
	 * 获取某个分类信息
	 */
	public function get_product_cat_by_id($id)
	{
		$ret = $this->where('id = '.$id)->find();
		return $ret;
	}

	/*
	 * 生成 可用于 config select 位置的 数组
	 */
	public function get_produnct_cat_config_select($show_titile='顶级分类')
	{
		$map['status'] = 1;
		$parent = $this->getList($map,$order='sort asc');
		$parent = D('Common/Tree')->toFormatTree($parent,'title','id','parent_id');
		$all_cats =array_merge(array(0=>array('id'=>0,'title_show'=>$show_titile)), $parent);
		foreach($all_cats as $cat){
			$select[$cat['id']] = html_entity_decode ($cat['title_show']);
		}
		return $select;
	}

	/*
	 * 生成 可用于 list select 位置的 数组 （主要是 列表页下来筛选）
	 */
	public function get_produnct_cat_list_select($show_titile='顶级分类')
	{
		$map['status'] = 1;
		$parent = $this->getList($map,$order='sort asc');
		$parent = D('Common/Tree')->toFormatTree($parent,'title','id','parent_id');
		$all_cats =array_merge(array(0=>array('id'=>0,'title_show'=>$show_titile)), $parent);
		foreach($all_cats as $cat)
		{
			$select[] = array('id'=>$cat['id'],'value'=>html_entity_decode ($cat['title_show']));
		}
		return $select;
	}

	/*
	 * 获取在 这个父分类下所有的分类id
	 *
	 */
	public function get_all_cat_id_by_pid($pid)
	{
		$ret= array($pid);
		is_array($pid) || $pid = array($pid);

		do{
			$ids = $this->where('parent_id in ('.implode(',',$pid).')')->getField('id',true);
			if($ids)
			{
				$ret = array_merge($ret,$ids);
				$pid = $ids;

			}
		}
		while($ids);

		return $ret;
	}
	/**
	 * 排序完整URL数组
	 * 
	 * @return [type] [description]
	 */
	public function sort_url($sort){

		$sort_param = array(
			'all'=>'all',
			'sell_cnt'=>'sell_cnt_desc',
			'price'=>'price_desc',
			'comment_cnt'=>'comment_cnt_desc',
			'create_time'=>'create_time_desc'
		);
		
		switch ($sort)
		{
		case 'sell_cnt_desc':
		  $sort_param['sell_cnt']='sell_cnt_asc';
		  break;  
		case 'sell_cnt_asc':
		  $sort_param['sell_cnt']='sell_cnt_desc';
		  break;
		case 'price_desc':
		  $sort_param['price']='price_asc';
		  break;  
		case 'price_asc':
		  $sort_param['price']='price_desc';
		  break;
		case 'comment_cnt_desc':
		  $sort_param['comment_cnt']='comment_cnt_asc';
		  break;  
		case 'comment_cnt_asc':
		  $sort_param['comment_cnt']='comment_cnt_desc';
		  break;
		case 'create_time_desc':
		  $sort_param['create_time']='create_time_asc';
		  break;  
		case 'create_time_asc':
		  $sort_param['create_time']='create_time_desc';
		  break;
		}
		$sort_url = array();
		foreach($sort_param as $k=>$v){
			$sort_url[$k] = U('Muushop/index/cats',array('sort'=>$v));
		}
		return $sort_url;
	}


}

