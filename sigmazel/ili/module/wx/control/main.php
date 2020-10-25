<?php
//版权所有(C) 2014 www.ilinei.com

namespace wx\control;

use admin\model\_menu;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//公众号
class main{
	//默认
	public function index(){
		global $_var;
		
		$_menu = new _menu();
		
		$menu = $_menu->get_by_url('{$ADMIN_SCRIPT}/wx/main');
		$menus = $_menu->get_list_of_user($_var['current'], "AND m.PARENTID = '{$menu[MENUID]}'");
		
		include_once view('/module/admin/view/main');
	}
	
}
?>