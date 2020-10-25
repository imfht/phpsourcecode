<?php

namespace Muushop\Controller;

use Think\Controller;

class PublicController extends BaseController {
	protected $product_cats_model;

	public function _initialize()
	{
		parent::_initialize();
		$this->product_cats_model = D('Muushop/MuushopProductCats');
	}

	/**
	 * 搜索弹出层
	 * @return [type] [description]
	 */
	public function _search() {

		$this->display();
	}
	/**
	 * 全部分类及子分类显示
	 * @return [type] [description]
	 */
	public function topcats(){
		//分类
		$map['status'] = 1;
		$items = $this->product_cats_model->getList($map,$order='sort asc');
		foreach($items as &$v){
			$v['link'] = U('Muushop/Index/cats',array('id'=>$v['id']));
		}
		unset($v);
		$items = list_to_tree($items,$pk = 'id', 'parent_id', $child = 'items');
		
		$this->assign('cats',$items);

		$this->display('_topcats');
	}
}
