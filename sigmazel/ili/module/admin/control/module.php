<?php
//版权所有(C) 2014 www.ilinei.com

namespace admin\control;

use admin\model\_module;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/admin/lang.php';

//模块
class module{
	//默认
	public function index(){
		$_module = new _module();
		
		$query = '';
		$modules = $_module->get_all();
		foreach ($modules as $key => $module){
			$query .= "{$module[id]},{$module[version]}|";
		}

		include_once view('/module/admin/view/module');
	}
	
	//安装
	public function _install(){
		global $_var;
		
		$_module = new _module();
		
		$modules = $_module->get_all();
		
		$module = $_var['gp_id'];
		
		if(!$module || !$modules[$module]) exit_json_message($GLOBALS['lang']['admin.module.message.error.param']);
		if(!is_file(ROOTPATH."/module/{$module}/model/_{$module}.php")) exit_json_message($GLOBALS['lang']['admin.module.message.error.file']);
		
		$class = $module.'\\_'.$module;
		
		if(!class_exists($class)) exit_json_message($GLOBALS['lang']['admin.module.message.error.class']);
		if(!method_exists($class, 'install')) exit_json_message($GLOBALS['lang']['admin.module.message.error.method']);
		
		$cls = new $class();
		$rtn = $cls->install();

        cache_delete('modules');
		
		exit_json($rtn);
	}
	
	//更新
	public function _update(){
		global $_var;
		
		$_module = new _module();
		
		$modules = $_module->get_all();
		
		$module = $_var['gp_id'];
		
		if(!$module || !$modules[$module]) exit_json_message($GLOBALS['lang']['admin.module.message.error.param']);

		$dirs = scandir(ROOTPATH."/module/{$module}/update");
		foreach ($dirs as $file){
			if(is_file(ROOTPATH."/module/{$module}/update/{$file}")){
				require_once ROOTPATH."/module/{$module}/update/{$file}";
				unlink(ROOTPATH."/module/{$module}/update/{$file}");
			}
		}
		
		exit_json_message('', true);
	}
	
	//卸载
	public function _uninstall(){
		global $_var;
		
		$_module = new _module();
		
		$modules = $_module->get_all();
		
		$module = $_var['gp_id'];
		
		if(!$module || !$modules[$module]) exit_json_message($GLOBALS['lang']['admin.module.message.error.param']);
		if(!is_file(ROOTPATH."/module/{$module}/model/_{$module}.php")) exit_json_message($GLOBALS['lang']['admin.module.message.error.file']);
		
		$class = $module.'\\_'.$module;
		
		if(!class_exists($class)) exit_json_message($GLOBALS['lang']['admin.module.message.error.class']);
		if(!method_exists($class, 'uninstall')) exit_json_message($GLOBALS['lang']['admin.module.message.error.method']);
		
		$cls = new $class();
		$rtn = $cls->uninstall();

        cache_delete('modules');
		
		exit_json($rtn);
	}
	
}
?>