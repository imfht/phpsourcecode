<?php
//版权所有(C) 2014 www.ilinei.com

namespace user\control;

use admin\model\_log;
use user\model\_group;
use user\model\_user;
use user\model\_third;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/user/lang.php';

//用户
class index{
	//默认
	public function index(){
		global $_var;
		
		$_log = new _log();
		$_group = new _group();
		$_user = new _user();
		
		$groups = $_group->get_all();
		$search = $_user->search();
		
		if($_var['gp_do'] == 'delete'){
			$user = $_user->get_by_id($_var['gp_id']);
			if($user){
				$_user->delete($user);
				
				$_log->insert($GLOBALS['lang']['user.index.log.delete']."({$user[USERNAME]})", $GLOBALS['lang']['user.index']);
			}
		}
		
		if($_var['gp_do'] == 'disable_list' && is_array($_var['gp_cbxItem'])){
			$user_names = '';
			foreach ($_var['gp_cbxItem'] as $key => $val){
				$user = $_user->get_by_id($val);
				if(!$user) continue;
				
				$_user->update($user['USERID'], array('ISAUDIT' => 1));
				
				$user_names .= $user['USERNAME'].',';
				
				unset($user);
			}
			
			if($user_names) $_log->insert($GLOBALS['lang']['user.index.log.disable.list']."({$user_names})", $GLOBALS['lang']['user.index']);
		}
		
		if($_var['gp_do'] == 'enable_list' && is_array($_var['gp_cbxItem'])){
			$user_names = '';
			foreach ($_var['gp_cbxItem'] as $key => $val){
				$user = $_user->get_by_id($val);
				if(!$user) continue;
				
				$_user->update($user['USERID'], array('ISAUDIT' => 0));
				
				$user_names .= $user['USERNAME'].',';
				
				unset($user);
			}
			
			if($user_names) $_log->insert($GLOBALS['lang']['user.index.log.enable.list']."({$user_names})", $GLOBALS['lang']['user.index']);
		}
		
		if($_var['gp_do'] == 'delete_list' && is_array($_var['gp_cbxItem'])){
			$user_names = '';
			foreach ($_var['gp_cbxItem'] as $key => $val){
				$user = $_user->get_by_id($val);
				if(!$user) continue;
				
				$_user->delete($user);
				
				$user_names .= $user['USERNAME'].',';
				
				unset($user);
			}
			
			if($user_names) $_log->insert($GLOBALS['lang']['user.index.log.delete.list']."({$user_names})", $GLOBALS['lang']['user.index']);
		}
		
		$count = $_user->get_count_of_group($search['wheresql']);
		if($count){
			$perpage = $_var['psize'];
			$pages = @ceil($count / $perpage);
			$_var['page'] = $_var['page'] > $pages ? $pages : $_var['page'];
			$start = ($_var['page'] - 1) * $perpage;
			
			$users = $_user->get_list_of_group($start, $perpage, $search['wheresql'], $search['ordersql']);
			
			$pager = pager($count, $perpage, $_var['page'], "{ADMIN_SCRIPT}/user{$search[querystring]}", $perpage);
		}
		
		include_once view('/module/user/view/index');
	}
	
	//添加
	public function _add(){
		global $_var;
		
		$_log = new _log();
		$_group = new _group();
		$_user = new _user();
		
		$groups = $_group->get_all();
		
		if($_var['gp_formsubmit']){
			$_var['msg'] = '';
			
			$_var['gp_txtUserName'] = utf8substr($_var['gp_txtUserName'], 0, 30);
			$_var['gp_txtEmail'] = utf8substr($_var['gp_txtEmail'], 0, 50);
			$_var['gp_txtMobile'] = utf8substr($_var['gp_txtMobile'], 0, 30);
			
			if(empty($_var['gp_txtUserName']))$_var['msg'] .= $GLOBALS['lang']['user.index_edit.validate.username']."<br/>";
			elseif(empty($_var['gp_txtEmail']) && empty($_var['gp_txtMobile']))$_var['msg'] .= $GLOBALS['lang']['user.index_edit.validate.account']."<br/>";
			
			if(empty($_var['msg'])){
				$userofemail = $_var['gp_txtEmail'] ? $_user->get_by_email($_var['gp_txtEmail']) : null;
				$userofmobile = $_var['gp_txtMobile'] ? $_user->get_by_mobile($_var['gp_txtMobile']) : null;
				
				if($userofemail) $_var['msg'] .= $GLOBALS['lang']['user.index_edit.validate.exists.email']."<br/>";
				elseif($userofmobile) $_var['msg'] .= $GLOBALS['lang']['user.index_edit.validate.exists.mobile']."<br/>";
				else{
					$_user->insert(array(
					'USERNAME' => $_var['gp_txtUserName'],
					'REALNAME' => utf8substr($_var['gp_txtRealName'], 0, 30), 
					'EMAIL' => $_var['gp_txtEmail'],
					'PASSWD' => md5($_var['gp_txtPassword']),
					'GROUPID' => $_var['gp_sltGroup'], 
					'CREATETIME' => date('Y-m-d H:i:s'),
					'COMMENT' => utf8substr($_var['gp_txtComment'], 0, 100), 
					'MOBILE' => $_var['gp_txtMobile'], 
					'PHONE' => utf8substr($_var['gp_txtPhone'], 0, 30), 
					'ISMANAGER' => 0, 
					'SALT' => substr(uniqid(rand()), -6)
					));
					
					$_log->insert($GLOBALS['lang']['user.index.edit.log.add']."({$_var[gp_txtUserName]})", $GLOBALS['lang']['user.index']);
					
					show_message($GLOBALS['lang']['user.index_edit.message.add'], "{ADMIN_SCRIPT}/user");
				}
			}
		}
		
		include_once view('/module/user/view/index_edit');
	}
	
	//修改
	public function _update(){
		global $_var;
		
		$_log = new _log();
		$_group = new _group();
		$_user = new _user();
		$_third = new _third();
		
		$groups = $_group->get_all();
		$search = $_user->search();
		
		$id = $_var['gp_id'] + 0;
		if($id == 0) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/user");
		
		$user = $_user->get_by_id($id);
		if($user == null) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/user");
		
		$third = $_third->get_by_userid($user['USERID']);
		
		if($_var['gp_formsubmit']){
			$_var['msg'] = '';
			
			$_var['gp_txtUserName'] = utf8substr($_var['gp_txtUserName'], 0, 30);
			$_var['gp_txtEmail'] = utf8substr($_var['gp_txtEmail'], 0, 50);
			$_var['gp_txtMobile'] = utf8substr($_var['gp_txtMobile'], 0, 30);
			
			if(empty($_var['gp_txtUserName']))$_var['msg'] .= $GLOBALS['lang']['user.index_edit.validate.username']."<br/>";
			elseif(!$third && empty($_var['gp_txtEmail']) && empty($_var['gp_txtMobile']))$_var['msg'] .= $GLOBALS['lang']['user.index_edit.validate.account']."<br/>";
			
			if(empty($_var['msg'])){
				$userofemail = $_var['gp_txtEmail'] ? $_user->get_by_email($_var['gp_txtEmail']) : null;
				$userofmobile = $_var['gp_txtMobile'] ? $_user->get_by_mobile($_var['gp_txtMobile']) : null;
				
				if(!$third && $userofemail && $user['USERID'] != $userofemail['USERID']) $_var['msg'] .= $GLOBALS['lang']['user.index_edit.validate.exists.email']."<br/>";
				elseif(!$third && $userofmobile && $user['USERID'] != $userofmobile['USERID']) $_var['msg'] .= $GLOBALS['lang']['user.index_edit.validate.exists.mobile']."<br/>";
				else{
					$_user->update($id, array(
					'USERNAME' => $user['USERNAME'] ? $_var['gp_txtUserName'] : $user['USERNAME'],
					'REALNAME' => utf8substr($_var['gp_txtRealName'], 0, 30),
					'EMAIL' => $_var['gp_txtEmail'],
					'PASSWD' => $_var['gp_txtPassword'] ? md5($_var['gp_txtPassword']) : $user['PASSWD'],
					'GROUPID' => $_var['gp_sltGroup'], 
					'COMMENT' => utf8substr($_var['gp_txtComment'], 0, 200), 
					'MOBILE' => $_var['gp_txtMobile'], 
					'PHONE' => utf8substr($_var['gp_txtPhone'], 0, 30) 
					));
					
					$_log->insert($GLOBALS['lang']['user.index.edit.log.update']."({$_var[gp_txtUserName]})", $GLOBALS['lang']['user.index']);
					
					show_message($GLOBALS['lang']['user.index_edit.message.update'], "{ADMIN_SCRIPT}/user&page={$_var[page]}&psize={$_var[psize]}{$search[querystring]}");
				}
			}
		}
		
		include_once view('/module/user/view/index_edit');
	}
	
	//变更
	public function _modify(){
		global $_var;
		
		$_group = new _group();
		$_user = new _user();

        $search = $_user->search();
		$groups = $_group->get_all();
		
		if($_var['gp_formsubmit']){
			$groupid = $_var['gp_sltBGroupID'];
			if(!$groupid) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/user&page={$_var[page]}&psize={$_var[psize]}{$search[querystring]}");
			
			$group = $_group->get_by_id($groupid);
			if(!$group) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/user&page={$_var[page]}&psize={$_var[psize]}{$search[querystring]}");
			
			$userids = explode(',', $_var['gp_hdnUserIds']);
			
			foreach ($userids as $key => $val){
				$tempdata = $_user->get_by_id($val);
				if($tempdata) $_user->update($val, array('GROUPID' => $group['GROUPID']));
				
				unset($temparr);
			}
			
			show_message($GLOBALS['lang']['user.index.message.modify'], "{ADMIN_SCRIPT}/user&page={$_var[page]}&psize={$_var[psize]}{$search[querystring]}");
		}
		
		include_once view('/module/user/view/index_modify');
	}
	
	//导出excel
	public function _excel(){
		global $dispatches;
		
		$_user = new _user();
        $search = $_user->search();

		$user_title = $GLOBALS['lang']['user.index.excel'];

        if(empty($dispatches['operations']['export'])){
			$user_list = $_user->get_list_of_group(0, 0, $search['wheresql']);
		}
		
		$user_title = '用户列表';
		
		header("Content-type:application/vnd.ms-excel");
		header("Content-Disposition:attachment;filename={$user_title}.xls");
		
		include_once view('/module/user/view/index_excel');
	}
}
?>