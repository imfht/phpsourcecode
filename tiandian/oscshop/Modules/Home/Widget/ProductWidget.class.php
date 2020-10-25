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
namespace Home\Widget;
use Think\Controller;
/**
 * 商品
 */
class ProductWidget extends Controller{

	/**
	 * 首页的商品
	 * 
	 */
	function home_goods_list($title,$order_by,$limit){		
		$key='home_goods_cache'.$order_by;
		
		if (!$home_goods_cache = S($key)) {
			$sql='SELECT goods_id,image,price,name FROM '.C('DB_PREFIX').'goods WHERE status=1 ORDER BY '.$order_by.' LIMIT 0,'.$limit;
			$list=M()->query($sql);	
			
			$hashids = new \Lib\Hashids(C('PWD_KEY'), C('URL_ID'));
			foreach ($list as $k => $v) {
				
				$list[$k]['image']=resize($v['image'], C('common_image_thumb_width'), C('common_image_thumb_height'));
				$list[$k]['goods_id']=$hashids->encode($v['goods_id']);
			}
			S($key, $list);	
			
			$home_goods_cache=$list;
		}
		$this->products=$home_goods_cache;		
		$this->title=$title;
		$this->display('Widget:home_goods_list');	
	}
	//详情页热门产品
	function hot_goods_list($title,$order_by,$limit){		
		$key='hot_goods_cache'.$order_by;
		
		if (!$hot_goods_cache = S($key)) {
			$sql='SELECT goods_id,image,price,name FROM '.C('DB_PREFIX').'goods WHERE status=1 ORDER BY '.$order_by.' LIMIT 0,'.$limit;
			$list=M()->query($sql);	
			
			$hashids = new \Lib\Hashids(C('PWD_KEY'), C('URL_ID'));
			foreach ($list as $k => $v) {
				
				$list[$k]['image']=resize($v['image'], C('goods_cart_thumb_width'), C('goods_cart_thumb_height'));
				$list[$k]['goods_id']=$hashids->encode($v['goods_id']);
			}
			S($key, $list);	
			
			$hot_goods_cache=$list;
		}
		$this->products=$hot_goods_cache;		
		$this->title=$title;
		$this->display('Widget:goods_show_hot_goods_list');	
	}
	//详情页推荐的关联产品
	function related_goods_list(){
		$sql='SELECT goods_id,image,price,name FROM '.C('DB_PREFIX').'goods WHERE status=1 ORDER BY rand() LIMIT 0,3';
		$list=M()->query($sql);	
		
		$hashids = new \Lib\Hashids(C('PWD_KEY'), C('URL_ID'));
		foreach ($list as $k => $v) {
			
			$list[$k]['image']=resize($v['image'], C('goods_related_thumb_width'), C('goods_related_thumb_height'));
			$list[$k]['goods_id']=$hashids->encode($v['goods_id']);
		}
			
		
		$this->related_goods=$list;		
		
		$this->display('Widget:related_goods_list');
	}
	
}
