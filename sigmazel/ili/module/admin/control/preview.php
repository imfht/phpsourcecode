<?php
//版权所有(C) 2014 www.ilinei.com

namespace admin\control;

use wx\model\_wx_menu;
use wx\model\_wx_setting;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/admin/lang.php';
//引入二维码库
require_once ROOTPATH.'/source/lib/QRcode.php';

//导航
class preview{
	//手机预览
	public function mobile(){
		global $_var;
		
		$_wx_menu = new _wx_menu();
		$_wx_setting = new _wx_setting();
		
		$wx_open = $_wx_setting->get('WX_OPEN');
		
		if($wx_open['WX_OPEN']){
			$menus = $_wx_menu->get_tree();
		}
		
		include_once view('/module/admin/view/preview_mobile');
	}
	
	//二维码
	public function qrcode(){
		global $_var;
		$_var['gp_url'] = str_replace('!', '?', $_var['gp_url']);
		$_var['gp_url'] = str_replace('|', '&', $_var['gp_url']);
		$_var['gp_url'] = str_replace(',', '=', $_var['gp_url']);
		
		\QRcode::png("{$_var[gp_url]}", '', QR_ECLEVEL_H);
	}
	
}
?>