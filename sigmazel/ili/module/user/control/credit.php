<?php
//版权所有(C) 2014 www.ilinei.com

namespace user\control;

use admin\model\_log;
use user\model\_user;
use user\model\_credit;
use user\model\_credit_record;
use user\model\_credit_rule;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/user/lang.php';

//积分
class credit{
	//默认
	public function index(){
		global $_var;
		
		$_log = new _log();
		$_credit = new _credit();
		
		$search = $_credit->search();
		
		if($_var['gp_do'] == 'delete'){
			$credit = $_credit->get_by_id($_var['gp_id'] + 0);
			
			if($credit){
				$_credit->delete($credit);
				
				$_log->insert($GLOBALS['lang']['user.credit.log.delete']."({$credit[TITLE]})", $GLOBALS['lang']['user.credit']);
			}
		}
		
		if($_var['gp_do'] == 'delete_list' && is_array($_var['gp_cbxItem'])){
			$credit_titles = '';
			
			foreach ($_var['gp_cbxItem'] as $key => $val){
				$credit = $_credit->get_by_id($val);
				if(!$credit) continue;
				
				$_credit->delete($credit);
				
				$credit_titles .= $credit['TITLE'].'， ';
				
				unset($credit);
			}
			
			if($credit_titles) $_log->insert($GLOBALS['lang']['user.credit.log.delete.list']."({$credit_titles})", $GLOBALS['lang']['user.credit']);
		}
		
		$count = $_credit->get_count($search['wheresql']);
		if($count){
			$perpage = $_var['psize'];
			$pages = @ceil($count / $perpage);
			$_var['page'] = $_var['page'] > $pages ? $pages : $_var['page'];
			$start = ($_var['page'] - 1) * $perpage;
			
			$credits = $_credit->get_list($start, $perpage, $search['wheresql']);
			
			$pager = pager($count, $perpage, $_var['page'], "{ADMIN_SCRIPT}/user/score{$search[querystring]}", $perpage);
		}
		
		include_once view('/module/user/view/credit');
	}
	
	//添加
	public function _add(){
		global $_var;
		
		$_log = new _log();
		$_user = new _user();
		$_credit = new _credit();
		$_credit_record = new _credit_record();
		
		if($_var['gp_formsubmit']){
			$user = $_user->get_by_id($_var['gp_txtUserName']);
			if($user == null) $user = $_user->get_by_mobile($_var['gp_txtUserName']);
			if($user == null) $user = $_user->get_by_email($_var['gp_txtUserName']);
			if($user == null) show_message($GLOBALS['lang']['user.credit_edit.validate.user'], "{ADMIN_SCRIPT}/user/score/_add");
			
			$_var['gp_txtCredit'] = $_var['gp_txtCredit'] + 0;
			$_var['gp_txtScore'] = $_var['gp_txtScore'] + 0;
			
			$creditid = $_credit->insert(array(
			'TITLE' => utf8substr($_var['gp_txtTitle'], 0, 50),
			'REMARK' => utf8substr($_var['gp_txtRemark'], 0, 200),
			'CREATETIME' => date('Y-m-d H:i:s'), 
			'ADDRESS' => $_var['clientip'], 
			'USERID' => $user['USERID'],
			'USERNAME' => $user['NAME'], 
			'EDITTIME' => date('Y-m-d H:i:s'), 
			'CREDIT' => $_var['gp_txtCredit'] + 0, 
			'SCORE' => $_var['gp_txtScore'] + 0
			));
			
			if($creditid){
				$_user->update($user['USERID'], array(
				'SCORE' => $user['SCORE'] + $_var['gp_txtScore'] + 0, 
				'CREDIT' => $user['CREDIT'] + $_var['gp_txtCredit'] + 0
				));
				
				$_credit_record->insert(array(
				'TITLE' => $GLOBALS['lang']['user.credit_edit.log.add']."：".utf8substr($_var['gp_txtTitle'], 0, 50), 
				'CREDIT1' => $user['CREDIT'], 
				'CREDIT2' => $_var['gp_txtCredit'] + 0, 
				'SCORE1' => $user['SCORE'], 
				'SCORE2' => $_var['gp_txtScore'] + 0, 
				'ADDRESS' => $_var['clientip'], 
				'AGENT' => cutstr($_SERVER['HTTP_USER_AGENT'], 200, ''), 
				'USERID' => $user['USERID'], 
				'USERNAME' => $user['NAME'], 
				'EDITTIME' => date('Y-m-d H:i:s'), 
				'ABOUTTYPE' => 'credit', 
				'ABOUTID' => $creditid
				));
				
				if($_var['gp_txtScore'] > 0){
					$user['SCORE'] += $_var['gp_txtScore'];
					$_user->flash_group($user);
				}
			}
			
			$_log->insert($user['NAME'].$GLOBALS['lang']['user.credit_edit.log.add']."({$_var[gp_txtTitle]})", $GLOBALS['lang']['user.credit']);
			
			show_message($user['NAME'].$GLOBALS['lang']['user.credit_edit.message.add'], "{ADMIN_SCRIPT}/user/score");
		}
		
		include_once view('/module/user/view/credit_edit');
	}
	
	//规则
	public function _rule(){
		global $_var;
		
		$_log = new _log();
		$_credit_rule = new _credit_rule();
		
		$insert_count = 0;
		
		foreach ($_var['gp_newname'] as $key => $val){
			if($_var['gp_newname'][$key] && $_var['gp_newaction'][$key]){
				$_credit_rule->insert(array(
				'ENABLED' => 0, 
				'ACTION' => $_var['gp_newaction'][$key], 
				'TYPE' => $_var['gp_newtype'][$key] + 0,
				'NUM' => $_var['gp_newnum'][$key] + 0, 
				'SCORE' => $_var['gp_newscore'][$key],
				'CREDIT' => $_var['gp_newcredit'][$key],
				'NAME' => utf8substr($_var['gp_newname'][$key], 0, 30)
				));
				
				$insert_count++;
			}
		}
		
		if($insert_count > 0) $_log->insert($GLOBALS['lang']['user.credit_rule.log.add'], $GLOBALS['lang']['user.credit_rule']);
		
		if(is_array($_var['gp_name'])){
			foreach ($_var['gp_name'] as $key => $val){
				$_credit_rule->update($key, array(
				'ENABLED' => $_var['gp_enabled'][$key] + 0, 
				'ACTION' => $_var['gp_action'][$key], 
				'NAME' => $_var['gp_name'][$key], 
				'TYPE' => $_var['gp_type'][$key] + 0, 
				'NUM' => $_var['gp_num'][$key] + 0, 
				'SCORE' => $_var['gp_score'][$key], 
				'CREDIT' => $_var['gp_credit'][$key]
				));
			}
		}
		
		if($_var['gp_do'] == 'delete_list' && is_array($_var['gp_cbxItem'])){
			$rule_names = '';
			foreach ($_var['gp_cbxItem'] as $key => $val){
				$credit_rule = $_credit_rule->get_by_id($val);
				if(!$credit_rule) continue;
				
				$_credit_rule->delete($credit_rule['CREDIT_RULEID']);
				
				$rule_names .= $credit_rule['NAME'].',';
				
				unset($credit_rule);
			}
			
			if($rule_names) $_log->insert($GLOBALS['lang']['user.credit_rule.log.delete.list']."({$rule_names})", $GLOBALS['lang']['user.credit_rule']);
		}
	
		$credit_rules = $_credit_rule->get_all();
		
		include_once view('/module/user/view/credit_rule');
	}

}
?>