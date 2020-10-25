<?php
//版权所有(C) 2014 www.ilinei.com

namespace admin\control;

use admin\model\_log;

if(!defined('INIT')) exit('Access Denied');
require_once ROOTPATH.'/module/admin/lang.php';

//日志
class log{
	//默认
	public function index(){
		global $_var;
		
		$_log = new _log();
		
		$search = $_log->search();
		
		if($_var['gp_do'] == 'delete' && $_var['gp_id'] > 0){
			$_log->delete("LOGID = '{$_var[gp_id]}'");
		}
		
		if($_var['gp_do'] == 'delete_list' && is_array($_var['gp_cbxItem'])){
			$_log->delete("LOGID IN (".eimplode($_var['gp_cbxItem']).")");
		}
		
		$count = $_log->get_count("{$search[wheresql]}");
		if($count){
			$perpage = $_var['psize'];
			$pages = @ceil($count / $perpage);
			$_var['page'] = $_var['page'] > $pages ? $pages : $_var['page'];
			$start = ($_var['page'] - 1) * $perpage;
			
			$logs = $_log->get_list($start, $perpage, "{$search[wheresql]}");
			
			$pager = pager($count, $perpage, $_var['page'], "{ADMIN_SCRIPT}/admin/log{$search[querystring]}", $perpage);
		}
		
		include_once view('/module/admin/view/log');
	}
	
	//清空
	public function _clear(){
		global $ADMIN_SCRIPT;
		
		$_log = new _log();
		$_log->delete();
		
		header("location:{$ADMIN_SCRIPT}/admin/log");
		exit(0);
	}
	
}
?>