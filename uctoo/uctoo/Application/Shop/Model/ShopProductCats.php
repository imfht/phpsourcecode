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

 define('NOW_TIME',input('server.REQUEST_TIME'));
class ShopProductCats extends Model{
	protected $tableName='shop_product_cats';
	protected $_validate = array(
		array('title','1,64','分类标题长度不对',1,'length'),
		array('title_en','0,128','分类英文标题长度不对',2,'length'),

	);
	protected $_auto = array(
		array('create_time', NOW_TIME, self::MODEL_INSERT),
		array('status', '1', self::MODEL_INSERT),
//		array('parent_id', '0', self::MODEL_INSERT),
	);
	/*
	 * 增加修改商品分类
	 */
	public function add_or_edit_product_cats($product_cats){
		if(empty($product_cats['id']))
		{
            $ret = $this->allowField(true)->save($product_cats);
		}
		else
		{
			//$this->create();
			$ret = $this->allowField(true)->where('id='.$product_cats['id'])->update($product_cats);
		}
		return $ret;
	}
	/*
	 * 删除商品分类
	 */
	public function delete_product_cats($ids)
	{
		if(!is_array($ids))
		{
			$ids = array($ids);
		}
		$this->startTrans();
		$ret1 = $this->where('id in ('.implode(',',$ids).')')->delete();
		if($this->where('parent_id in ('.implode(',',$ids).')')->count()>0)
		{
			$ret2 = $this->where('parent_id in  ('.implode(',',$ids).')')->save(array('parent_id'=>'0'));

		}
		else
		{
			$ret2=true;
		}
		$product_model = new ShopProductModel();
		if($product_model->where('cat_id in  ('.implode(',',$ids).')')->count()>0)
		{

			$ret3 = $product_model->where('cat_id in  ('.implode(',',$ids).')')->save(array('cat_id'=>'0'));
		}
		else
		{
			$ret3 =true;
		}
		if($ret1 && $ret2 && $ret3)
		{
			$this->commit();
			return true;
		}
		else
		{
			$this->rollback();
			return false;
		}
	}
	/*
	 * 获取分类信息
	 */
	public function get_product_cats($option)
	{
		
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
		if(empty($option)){
            $option['page'] = 1;//当前页
            $option['r']=10;//总页数
        }
        $ret['list'] = $this->where($where_str)->order('sort desc, create_time')->paginate($option['r'],true,
            ['page'=>$option['page']]);
		$ret['page'] = $ret['list']->render();
		$ret['count'] = $this->where($where_str)->count();
		//获取父级分类信息
		if(!empty($option['with_parent_info']) && $ret['list']) {
			foreach($ret['list'] as $k => $c) {
				if($c['parent_id']) {
					$ret['list'][$k]['parent_cat'] = $this->get_product_cat_by_id($c['parent_id']);
				}
			}
		}
		return $ret;
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
		$option = array();
		$parent = $this->get_product_cats($option);
		$parent = model('Common/Tree')->toFormatTree($parent['list'],$title = 'title',$pk='id',$pid = 'parent_id',$root =
            0);
		$all_cats =array_merge(array(0=>array('id'=>0,'title_show'=>$show_titile)), $parent);
		foreach($all_cats as $cat)
		{
//			$select[$cat['id']] = strtr($cat['title_show'],array('&nbsp;'=>'/&nbsp;'));
			$select[$cat['id']] = html_entity_decode ($cat['title_show']);
		}
		return $select;
	}

	/*
	 * 生成 可用于 list select 位置的 数组 （主要是 列表页下来筛选）
	 */
	public function get_produnct_cat_list_select($show_titile='顶级分类')
	{
		$option = array();
		$parent = $this->get_product_cats($option);
		$parent = model('Common/Tree')->toFormatTree($parent['list'],$title = 'title',$pk='id',$pid = 'parent_id',
            $root = 0);
		$all_cats =array_merge(array(0=>array('id'=>0,'title_show'=>$show_titile)), $parent);
		foreach($all_cats as $cat)
		{
//			$select[] = array('id'=>$cat['id'],'value'=>strtr($cat['title_show'],array('&nbsp;'=>"*")));
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



}

