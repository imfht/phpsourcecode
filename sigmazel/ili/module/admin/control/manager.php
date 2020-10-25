<?php
//版权所有(C) 2014 www.ilinei.com

namespace admin\control;

use admin\model\_log;
use admin\model\_role;
use admin\model\_manager;
use cms\model\_category;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/admin/lang.php';

//管理员
class manager{
	//默认
	public function index(){
		global $_var;
		
		$_log = new _log();
		$_role = new _role();
		$_manager = new _manager();
		
		$roles = $_role->get_all();
		
		if($_var['gp_do'] == 'delete' && $_var['gp_id'] > 0){
			$manager = $_manager->get_by_id($_var['gp_id']);
			if($manager && $manager['ISMANAGER'] == 1){
				$_manager->delete($manager);
				
				$_log->insert($GLOBALS['lang']['admin.manager.log.delete']."({$manager[USERNAME]})", $GLOBALS['lang']['admin.manager']);
			}
		}
		
		if($_var['gp_do'] == 'delete_list' && is_array($_var['gp_cbxItem'])){
			$manager_names = '';
			foreach ($_var['gp_cbxItem'] as $key => $val){
				$manager = $_manager->get_by_id($val);
				if(!$manager || $manager['ISMANAGER'] == 0) continue;
				
				$_manager->delete($manager);
				
				$manager_names .= $manager['USERNAME'].', ';
				
				unset($manager);
			}
			
			$_log->insert($GLOBALS['lang']['admin.manager.log.delete.list']."({$manager_names})", $GLOBALS['lang']['admin.manager']);
		}
		
		$count = $_manager->get_count();
		if($count){
			$perpage = $_var['psize'];
			$pages = @ceil($count / $perpage);
			$_var['page'] = $_var['page'] > $pages ? $pages : $_var['page'];
			$start = ($_var['page'] - 1) * $perpage;
			
			$managers = $_manager->get_list($start, $perpage);
			
			$pager = pager($count, $perpage, $_var['page'], "{ADMIN_SCRIPT}/admin/manager", $perpage);
		}
		
		include_once view('/module/admin/view/manager');
	}
	
	//添加
	public function _add(){
		global $_var;
		
		$_log = new _log();
		$_role = new _role();
		$_manager = new _manager();
		$_category = new _category();
		
		$roles = $_role->get_all();
		
		$categories = $_category->get_all(0, 'article');
		foreach($categories as $key => $category) $categories[$key]['CNAME'] = str_replace('\'', '‘', $category['CNAME']);
		
		if($_var['gp_formsubmit']){
			$_var['msg'] = '';
			
			if(empty($_var['gp_txtUserName'])) $_var['msg'] .= $GLOBALS['lang']['admin.manager_edit.validate.username']."<br/>";
			if($_var['gp_sltRole'] == 0) $_var['msg'] .= $GLOBALS['lang']['admin.manager_edit.validate.role']."<br/>";
			
			if(empty($_var['msg'])){
				$manager = $_manager->get_by_name($_var['gp_txtUserName']);
				if($manager) $_var['msg'] .= $GLOBALS['lang']['admin.manager_edit.validate.username.exists']."<br/>";
				else{
					$managerid = $_manager->insert(array(
					'USERNAME' => utf8substr($_var['gp_txtUserName'], 0, 30),
					'REALNAME' => utf8substr($_var['gp_txtRealName'], 0, 30),
					'MOBILE' => utf8substr($_var['gp_txtMobile'], 0, 30),
					'EMAIL' => utf8substr($_var['gp_txtEmail'], 0, 50),
					'PASSWD' => md5($_var['gp_txtPassword']),
					'ROLEID' => $_var['gp_sltRole'], 
					'GROUPID' => 0,
					'CREATETIME' => date('Y-m-d H:i:s'),
					'COMMENT' => utf8substr($_var['gp_txtComment'], 0, 200), 
					'ISAUDIT' => 0, 
					'ISMANAGER' => 1, 
					'SALT' => substr(uniqid(rand()), -6)
					));
					
					$_manager->insert_category($managerid, explode(',', $_var['gp_hdnCategoryIdString']));
					
					$_log->insert($GLOBALS['lang']['admin.manager.log.add']."({$_var[gp_txtUserName]})", $GLOBALS['lang']['admin.manager']);
					
					show_message($GLOBALS['lang']['admin.manager.message.add'], "{ADMIN_SCRIPT}/admin/manager");
				}
			}
		}
		
		include_once view('/module/admin/view/manager_edit');
	}
	
	//修改
	public function _update(){
		global $_var;
		
		$_log = new _log();
		$_role = new _role();
		$_manager = new _manager();
		$_category = new _category();
		
		$roles = $_role->get_all();
		
		$id = $_var['gp_id'] + 0;
		if($id == 0) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/admin/manager");
		
		$manager = $_manager->get_by_id($id);
		if($manager == null) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/admin/manager");
		
		$categories = $_category->get_all(0, 'article');
		foreach($categories as $key => $category) $categories[$key]['CNAME'] = str_replace('\'', '‘', $category['CNAME']);
		
		$manager_categories = $_manager->get_category($id, 'article');
		$category_id_string = '';
		foreach($manager_categories as $key => $category) $category_id_string .= ','.$category['CATEGORYID'].',';
		
		if($_var['gp_formsubmit']){
			$_var['msg'] = '';
			
			if(empty($_var['gp_txtUserName'])) $_var['msg'] .= $GLOBALS['lang']['admin.manager_edit.validate.username']."<br/>";
			if($_var['gp_sltRole'] == 0) $_var['msg'] .= $GLOBALS['lang']['admin.manager_edit.validate.role']."<br/>";
			
			if(empty($_var['msg'])){
				$manager = $_manager->get_by_name($_var['gp_txtUserName']);
				if($manager && $manager['USERID'] != $id) $_var['msg'] .= $GLOBALS['lang']['admin.manager_edit.validate.username.exists']."<br/>";
				else{
					$_manager->update($id, array(
					'USERNAME' => utf8substr($_var['gp_txtUserName'], 0, 30),
					'REALNAME' => utf8substr($_var['gp_txtRealName'], 0, 30),
					'MOBILE' => utf8substr($_var['gp_txtMobile'], 0, 30),
					'EMAIL' => utf8substr($_var['gp_txtEmail'], 0, 50),
					'PASSWD' => $_var['gp_txtPassword'] ? md5($_var['gp_txtPassword']) : $manager['PASSWD'],
					'ROLEID' => $_var['gp_sltRole'], 
					'COMMENT' => utf8substr($_var['gp_txtComment'], 0, 200)
					));
					
					$_manager->insert_category($id, explode(',', $_var['gp_hdnCategoryIdString']));
					
					$_log->insert($GLOBALS['lang']['admin.manager.log.update']."({$_var[gp_txtUserName]})", $GLOBALS['lang']['admin.manager']);
					
					show_message($GLOBALS['lang']['admin.manager.message.update'], "{ADMIN_SCRIPT}/admin/manager&page={$_var[page]}&psize={$_var[psize]}");
				}
			}
		}
		
		include_once view('/module/admin/view/manager_edit');
	}
	
}
?>