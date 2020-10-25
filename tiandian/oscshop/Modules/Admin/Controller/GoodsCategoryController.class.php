<?php
/**
 * oscshop 电子商务系统
 *
 * ==========================================================================
 * @link      http://www.oscshop.cn/
 * @copyright Copyright (c) 2015 oscshop.cn. 
 * @license   http://www.oscshop.cn/license.html License
 * ==========================================================================
 *
 * @author    李梓钿
 *
 */
namespace Admin\Controller;

class GoodsCategoryController extends CommonController{
	
	protected function _initialize(){
		parent::_initialize();
			$this->breadcrumb1='商品';
			$this->breadcrumb2='商品分类';
	}
	
	public function index(){
		
		$sql='SELECT id,pid,name FROM '
		.C('DB_PREFIX').'goods_category';
		
		$cate = M()->query($sql);
		$list =list_to_tree($cate);	
		$this->list=json_encode($list);
		
		$this->display();
	}
	function add(){
		
		if(IS_POST){
			
			$d['name']=I('name');
			$d['pid']=I('id');
			$d['meta_keyword']=I('meta_keyword');
			$d['meta_description']=I('meta_description');
			$d['sort_order']=I('sort_order');
			
			if(M('goods_category')->where(array('name'=>$d['name'],'pid'=>$d['pid']))->find()){
				$data['err']='该分类名称已经存在';				
				$this->ajaxReturn($data);				
				die();
			}
			
			$id=M('goods_category')->add($d);
			if($id){
				
				$data['name'] =$d['name'];			
				$data['id']=$id;
				$this->ajaxReturn($data);
				
				die();
			}else{
				
				die();
			}
		}

	}
	
		function edit(){
		if(IS_POST){
			
			$d['id']=I('id');
			$d['name']=I('name');
			$d['meta_keyword']=I('meta_keyword');
			$d['meta_description']=I('meta_description');
			$d['sort_order']=I('sort_order');
			
			$category=M('goods_category')->find($d['id']);
			
			if(M('goods_category')->where(array('name'=>$d['name'],'pid'=>$category['pid']))->find()){
				$data['err']='该分类名称已经存在';				
				$this->ajaxReturn($data);				
				die();
			}
			
			
			$r=M('goods_category')->save($d);
			
			if($r){
				
				$data['success']='修改成功';
				$data['name']=$d['name'];
				$this->ajaxReturn($data);
								
				die();
			}else{
				
				$data['err']='修改失败';
				
				$this->ajaxReturn($data);				
				
				die();
			}
		}
	}
	
	function get_info(){
		if(IS_POST){
			$id=I('id');
			$d=M('goods_category')->find($id);
			
			$data['name']=$d['name'];
			$data['meta_keyword']=$d['meta_keyword'];
			$data['meta_description']=$d['meta_description'];
			$data['sort_order']=$d['sort_order'];
			
			
			$this->ajaxReturn($data);
		}
	}
	function del(){
		if(IS_POST){
			$id=I('id');
			
			if(M('goods_category')->where('pid='.$id)->find()){
				$data['err']='请先删除子节点！！';
				$this->ajaxReturn($data);
				die;
			}
			if(M('goods_to_category')->where(array('category_id'=>$id))->find()){
				$data['err']='请先删除该分类下商品！！';
				$this->ajaxReturn($data);
				die;
			}
			
			if(M('goods_category')->where('id='.$id)->delete()){
				$data['success']='删除成功';
				$this->ajaxReturn($data);
				die();
			}
		}		
	}	
	
	function autocomplete(){
		$json = array();
		
		$filter_name=I('filter_name');
		
		if (isset($filter_name)) {
			$sql='SELECT id,name FROM '.c('DB_PREFIX')."goods_category where name LIKE'%".$filter_name."%' LIMIT 0,20";
		}else{
			$sql='SELECT id,name FROM '.c('DB_PREFIX')."goods_category LIMIT 0,20";
		
		}
			$results = M('goods_category')->query($sql);

		foreach ($results as $result) {
			$json[] = array(
				'category_id' => $result['id'],
				'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
			);
		}
			
		$this->ajaxReturn($json);
	}
	
}
?>