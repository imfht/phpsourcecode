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
 * 导航
 */
class MenuWidget extends Controller{
	
	function menu_show($type){
		if (!$menu_cache = S('menu_cache')) {
			$hashids = new \Lib\Hashids(C('PWD_KEY'), C('URL_ID'));
			$menu=M('goods_category')->select();		
			foreach ($menu as $k => $v) {
				$menu[$k]['id']=$hashids->encode($v['id']);
			}			
			S('menu_cache', $menu);	
			$menu_cache=$menu;
		}
		
		$this->menu=$menu_cache;		
		$this->type=$type;
		$this->display('Widget:menu');
	}
	
}
