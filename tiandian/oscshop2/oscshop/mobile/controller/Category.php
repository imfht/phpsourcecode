<?php
/**
 * oscshop2 B2C电子商务系统
 *
 * ==========================================================================
 * @link      http://www.oscshop.cn/
 * @copyright Copyright (c) 2015-2016 oscshop.cn. 
 * @license   http://www.oscshop.cn/license.html License
 * ==========================================================================
 *
 * @author    李梓钿
 *
 * 产品分类
 */
 
namespace osc\mobile\controller;
use think\Db;
class Category extends MobileBase
{
	function index(){
		
		$tree= new \oscshop\Tree();

		$this->assign('category',$tree->toOdArray(Db::name('category')->field('id,pid,name')->order('sort_order asc')->select()));

		$this->assign('SEO',['title'=>'分类-'.config('SITE_TITLE')]);
		
		return $this->fetch();
	}
	
	//取出分类下所有数据，数据量大时候需要改造
	function get_goods(){
		
		$id=input('param.id');
		
		$list= Db::view('Goods','goods_id,image,name,price')
		->view('GoodsToCategory','category_id','Goods.goods_id=GoodsToCategory.goods_id')					
		->where(array('Goods.status'=>1,'GoodsToCategory.category_id'=>$id))->select();
		
		$this->assign('goods',$list);
		exit($this->fetch('goods'));	
	
	}
}
