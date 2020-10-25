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
class Search extends MobileBase
{
	function index(){
		
		$this->assign('SEO',['title'=>'搜索-'.config('SITE_TITLE')]);
		$this->assign('flag','search');		
		return $this->fetch();
		
	}
	
	function ajax_goods_list(){
		
		$page=input('param.page');//页码
		
		$search_key=input('param.searchKey');//关键字
		
		$order_by=input('param.orderby');//排序
		//开始数字,数据量
		$limit = (6 * $page) . ",6";		
		
		if(empty($order_by)){
			$order_by=' goods_id';
		}
		
		$where['name']=['like','%'.$search_key.'%'];
		
		$list=Db::name('goods')->where($where)->order($order_by)->limit($limit)->select();

		if(isset($list)&&is_array($list)){
			foreach ($list as $k => $v) {				
				$list[$k]['image']=resize($v['image'], 200, 200);		
			}		
		}
		$this->assign('goods',$list);
		
		exit($this->fetch());	
	}

}
