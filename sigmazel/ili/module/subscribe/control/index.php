<?php
//版权所有(C) 2014 www.ilinei.com

namespace subscribe\control;

use admin\model\_log;
use subscribe\model\_subscribe;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/subscribe/lang.php';

//订阅
class index{
	//默认
	public function index(){
		global $_var;
		
		$_log = new _log();
		$_subscribe = new _subscribe();
		
		$search = $_subscribe->search();
		
		if(is_array($_var['gp_newemail'])){
			foreach($_var['gp_newemail'] as $key => $val){
				if($_var['gp_newemail'][$key]){
					$subscribe = $_subscribe->get_by_id($_var['gp_newemail'][$key]);
					if($subscribe) continue;
					
					$_subscribe->insert(array(
					'EMAIL' => utf8substr($_var['gp_newemail'][$key], 0, 60),
					'ADDRESS' => $_var['clientip'], 
					'AGENT' => cutstr($_SERVER['HTTP_USER_AGENT'], 200, ''), 
					'USERID' => $_var['current']['USERID'], 
					'USERNAME' => $_var['current']['USERNAME'], 
					'EDITTIME' => date('Y-m-d H:i:s')
					));
				}
				
				unset($subscribe);
			}
			
			$_log->insert($GLOBALS['lang']['subscribe.index.log.add'], $GLOBALS['lang']['subscribe.index']);
		}
		
		if($_var['gp_do'] == 'delete_list' && is_array($_var['gp_cbxItem'])){
			$email_names = '';
			foreach($_var['gp_cbxItem'] as $key => $val){
				$subscribe = $_subscribe->get_by_id($val);
				if(!$subscribe) continue;
				
				$_subscribe->delete($subscribe['SUBSCRIBEID']);
				
				$email_names .= $subscribe['EMAIL'].',';
			}
			
			if($email_names) $_log->insert($GLOBALS['lang']['subscribe.index.log.delete.list']."({$email_names})", $GLOBALS['lang']['subscribe.index']);
		}
		
		$count = $_subscribe->get_count($search['wheresql']);
		if($count){
			$perpage = $_var['psize'];
			$pages = @ceil($count / $perpage);
			$_var['page'] = $_var['page'] > $pages ? $pages : $_var['page'];
			$start = ($_var['page'] - 1) * $perpage;
			
			$subscribes = $_subscribe->get_list($start, $perpage, $search['wheresql']);
			
			$pager = pager($count, $perpage, $_var['page'], "{ADMIN_SCRIPT}/subscribe{$search[querystring]}", $perpage);
		}
		
		include_once view('/module/subscribe/view/index');
	}
	
	//导出excel
	public function _excel(){
		global $dispatches;
		
		$_subscribe = new _subscribe();
		
		$search = $_subscribe->search();

        if(empty($dispatches['operations']['export'])){
			$subscribes = $_subscribe->get_list(0, 0, $search['wheresql']);
		}
		
		header("Content-type:application/vnd.ms-excel");
		header("Content-Disposition:attachment;filename=".$GLOBALS['lang']['subscribe.index.excel.title'].".xls");
		
		include_once view('/module/subscribe/view/index_excel');
		exit(0);
	}
}
?>