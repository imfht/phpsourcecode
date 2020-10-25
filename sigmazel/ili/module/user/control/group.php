<?php
//版权所有(C) 2014 www.ilinei.com

namespace user\control;

use admin\model\_log;
use user\model\_group;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/user/lang.php';

//等级
class group{
	//默认
	public function index(){
		global $_var;
		
		$_log = new _log();
		$_group = new _group();
		
		if(is_array($_var['gp_newcname'])){
			$insert_count = 0;
			foreach ($_var['gp_newcname'] as $key => $val){
				if($_var['gp_newcname'][$key]){
					$_group->insert(array(
					'SCORELOW' => $_var['gp_newscorelow'][$key] + 0, 
					'SCOREHIGH' => $_var['gp_newscorehigh'][$key] + 0,
					'STARS' => $_var['gp_newstars'][$key] + 0,
					'CNAME' => utf8substr($_var['gp_newcname'][$key], 0, 30),
					'IDENTITY' => utf8substr($_var['gp_newidentity'][$key], 0, 30), 
					'PERCENT' => $_var['gp_newpercent'][$key] + 0, 
					'DELIVERFREE' => $_var['gp_newdeliverfree'][$key] + 0, 
					));
					
					$insert_count++;
				}
			}
			
			if($insert_count > 0) $_log->insert($GLOBALS['lang']['user.group.log.add'], $GLOBALS['lang']['user.group']);
		}
		
		if(is_array($_var['gp_cname'])){
			foreach ($_var['gp_cname'] as $key => $val){
				$_group->update($key, array(
				'STARS' => $_var['gp_stars'][$key] + 0, 
				'CNAME' => $_var['gp_cname'][$key], 
				'IDENTITY' => $_var['gp_identity'][$key], 
				'PERCENT' => $_var['gp_percent'][$key] + 0, 
				'SCORELOW' => $_var['gp_scorelow'][$key] + 0, 
				'SCOREHIGH' => $_var['gp_scorehigh'][$key] + 0, 
				'DELIVERFREE' => $_var['gp_deliverfree'][$key] + 0, 
				));
			}
		}
		
		if($_var['gp_do'] == 'delete_list' && is_array($_var['gp_cbxItem'])){
			$group_names = '';
			foreach ($_var['gp_cbxItem'] as $key => $val){
				$group = $_group->get_by_id($val);
				if(!$group) continue;
				
				$_group->delete($group['GROUPID']);
				
				$group_names .= $group['CNAME'].',';
				
				unset($group);
			}
			
			if($group_names) $_log->insert($GLOBALS['lang']['user.group.log.delete']."({$group_names})", $GLOBALS['lang']['user.group']);
		}
		
		$groups = $_group->get_all();
		
		include_once view('/module/user/view/group');
	}
	
}
?>