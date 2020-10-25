<?php
//版权所有(C) 2014 www.ilinei.com

namespace admin\control;

use admin\model\_manager;
use admin\model\_viewlog;
use admin\model\_menu;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/admin/lang.php';

//框架
class frame{
	//入口
	public function index(){
		global $_var;

		$_manager = new _manager();
		
		if(!$_var['current'] || !$_var['current']['ISMANAGER']){
			$_manager->unset_state();
			
			$GLOBALS['lang']['show_message.thin'] = true;
			show_message('Access Denied!', '{ADMIN_SCRIPT}');
		}
		
		include_once view('/module/admin/view/frame');
	}
	
	//头部分
	public function top(){
		global $_var, $ADMIN_SCRIPT;

		$_menu = new _menu();
		
		if($_var['current']['USERID'] == -1){
			$top_menus = $_menu->get_list("AND PARENTID = 0 AND TYPE = 1");
			foreach ($top_menus as $key => $menu){
				$top_menus[$key]['URL'] = str_replace('{$ADMIN_SCRIPT}', $ADMIN_SCRIPT, $menu['URL']);
			}
		}else{
			$top_menus = $_menu->get_list_of_user($_var['current'], "AND m.PARENTID = 0 AND m.TYPE = 1");
		}
		
		include_once view('/module/admin/view/frame_top');
	}
	
	//左部分
	public function left(){
		global $_var, $ADMIN_SCRIPT;

		$_menu = new _menu();
		
		if($_var['current']['USERID'] == -1){
			$left_menus = $_menu->get_list("AND PARENTID = 0 AND TYPE = 0");
			foreach ($left_menus as $key => $menu){
				$left_menus[$key]['URL'] = str_replace('{$ADMIN_SCRIPT}', $ADMIN_SCRIPT, $menu['URL']);
			}
		}else{
			$left_menus = $_menu->get_list_of_user($_var['current'], "AND m.PARENTID = 0 AND m.TYPE = 0");
		}
		
		include_once view('/module/admin/view/frame_left');
	}
	
	//读取访问日志
	public function viewlog(){
		$_viewlog = new _viewlog();
		
		$message = array('nexting' => false);
		$dirs = scandir(ROOTPATH.'/_cache/viewlog');
		$todayfile = date('Ymd00') + 0;
		
		foreach ($dirs as $file){
			if(strlen($file) == 10 && $file + 0 < $todayfile){
				$fp = fopen(ROOTPATH.'/_cache/viewlog/'.$file, 'r');
				$temp_logs = array();
				while (!feof($fp)) {
					$temp_logs[] = fgets($fp);
				}
				
				fclose($fp);
				
				foreach($temp_logs as $key => $log){
					$temp_log_arr = explode('|', $log);
					if(count($temp_log_arr) == 5){
						if(check_robot($temp_log_arr[4])) continue;
						
						$temp_date = substr($temp_log_arr[0], 0, 10);
							
						$temp_count = $_viewlog->get_count("AND AUTH = '{$temp_log_arr[2]}' AND DATE_FORMAT(DATELINE, '%Y-%m-%d') = '{$temp_date}'");
						if($temp_count > 0) continue;
							
						$_viewlog->insert(array(
						'DATELINE' => trim($temp_log_arr[0]),
						'IP' => $temp_log_arr[1],
						'AUTH' => $temp_log_arr[2],
						'REQUEST_URI' => $temp_log_arr[3],
						'HTTP_USER_AGENT' => $temp_log_arr[4]
						));
					}
		
					unset($temp_log_arr);
					unset($temp_date);
					unset($temp_count);
				}
					
				unlink(ROOTPATH.'/_cache/viewlog/'.$file);
					
				$message['nexting'] = true;
				exit_json($message);
			}
		
			unset($fp);
			unset($temp_logs);
		}
		
		$_viewlog->analyse();
		
		exit_json($message);
	}
	
}
?>