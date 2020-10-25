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
namespace Admin\Widget;
use Think\Controller;
/**
 * 后台菜单
 */
class MenuWidget extends Controller{	
	function menu_show(){		
		$menu=M('Menu')->order('sort_order')->select();		
		$tree=list_to_tree($menu,'id','pid','children',0);
		
		$this->admin_menu=$tree;		
		$this->display('Widget:menu');	
	}
	
}
