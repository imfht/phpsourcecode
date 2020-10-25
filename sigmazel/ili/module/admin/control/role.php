<?php
//版权所有(C) 2014 www.ilinei.com

namespace admin\control;

use admin\model\_log;
use admin\model\_role;
use admin\model\_menu;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/admin/lang.php';

//角色
class role{
	//默认
	public function index(){
		global $_var;
		
		$_log = new _log();
		$_role = new _role();
		
		if(is_array($_var['gp_cname'])){
			foreach ($_var['gp_cname'] as $key => $val){
				$_role->update($key, array('CNAME' => $val, 'DISPLAYORDER' => $_var['gp_displayorder'][$key]));
			}
		}
		
		if($_var['gp_do'] == 'delete' && $_var['gp_id'] > 0){
			$role = $_role->get_by_id($_var['gp_id']);
			if($role){
				$_role->delete($role['ROLEID']);
				
				$_log->insert($GLOBALS['lang']['admin.role.log.delete']."({$role[CNAME]})", $GLOBALS['lang']['admin.role']);
			}
		}elseif($_var['gp_do'] == 'delete_list' && is_array($_var['gp_cbxItem'])){
			$role_names = '';
			foreach ($_var['gp_cbxItem'] as $key => $val){
				$role = $_role->get_by_id($val);
				if(!$role) continue;
				
				$_role->delete($role['ROLEID']);
				
				$role_names .= $role['CNAME'].', ';
				
				unset($role);
			}
			
			$_log->insert($GLOBALS['lang']['admin.role.log.delete.list']."({$role_names})", $GLOBALS['lang']['admin.role']);
		}
		
		$roles = $_role->get_all();
		
		include_once view('/module/admin/view/role');
	}
	
	//添加
	public function _add(){
		global $_var;
		
		$_log = new _log();
		$_role = new _role();
		$_menu = new _menu();
		
		$menus = $_menu->get_all($_var['current']['ROLEID']);
		$operation_list = $_menu->get_operations();
		
		if($_var['gp_formsubmit']){
			$_var['msg'] = '';
			if(empty($_var['gp_txtDisplayOrder'])) $_var['msg'] .= $GLOBALS['lang']['admin.role_edit.validate.no']."<br/>";
			if(empty($_var['gp_txtCName'])) $_var['msg'] .= $GLOBALS['lang']['admin.role_edit.validate.cname']."<br/>";
			
			if(empty($_var['msg'])){
				$insertid = $_role->insert(array(
				'CNAME' => utf8substr($_var['gp_txtCName'], 0, 30),
				'COMMENT' => utf8substr($_var['gp_txtComment'], 0, 100),
				'DISPLAYORDER' => $_var['gp_txtDisplayOrder'] + 0,
				'EDITER' => $_var['current']['USERNAME'],
				'EDITTIME' => date('Y-m-d H:i:s')
				));
				
				$menus = array();
				if(is_array($_var['gp_cbxItem'])){
					foreach ($_var['gp_cbxItem'] as $key => $val){
						$menus[$key] = serialize($_var['gp_operations_'.$key]);
					}
				}
				
				$_role->insert_menu($insertid, $menus);
				
				$_log->insert($GLOBALS['lang']['admin.role.log.add']."({$_var[gp_txtCName]})", $GLOBALS['lang']['admin.role']);
				
				show_message($GLOBALS['lang']['admin.role.message.add'], "{ADMIN_SCRIPT}/admin/role");
			}
		}
		
		include_once view('/module/admin/view/role_edit');
	}
	
	//修改
	public function _update(){
		global $_var;
		
		$_log = new _log();
		$_role = new _role();
		$_menu = new _menu();
		
		$id = $_var['gp_id'] + 0;
		if($id == 0) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/admin/role");
		
		$role = $_role->get_by_id($id);
		if($role == null) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/admin/role"); 
		
		$menus = $_menu->get_all($_var['current']['ROLEID']);
		$operation_list = $_menu->get_operations();
		$role_menus = $_role->get_menus($role['ROLEID']);
		
		if($_var['gp_formsubmit']){
			$_var['msg'] = '';
			if(empty($_var['gp_txtDisplayOrder'])) $_var['msg'] .= $GLOBALS['lang']['admin.role_edit.validate.no']."<br/>";
			if(empty($_var['gp_txtCName'])) $_var['msg'] .= $GLOBALS['lang']['admin.role_edit.validate.cname']."<br/>";
			
			if(empty($_var['msg'])){
				$_role->update($id, array(
				'CNAME' => utf8substr($_var['gp_txtCName'], 0, 30),
				'COMMENT' => utf8substr($_var['gp_txtComment'], 0, 100),
				'DISPLAYORDER' => $_var['gp_txtDisplayOrder'] + 0,
				'EDITER' => $_var['current']['USERNAME'],
				'EDITTIME' => date('Y-m-d H:i:s')
				));
				
				$menus = array();
				if(is_array($_var['gp_cbxItem'])){
					foreach ($_var['gp_cbxItem'] as $key => $val){
						$menus[$key] = serialize($_var['gp_operations_'.$key]);
					}
				}
				
				$_role->insert_menu($id, $menus);
				
				$_log->insert($GLOBALS['lang']['admin.role.log.update']."({$_var[gp_txtCName]})", $GLOBALS['lang']['admin.role']);
				
				show_message($GLOBALS['lang']['admin.role.message.update'], "{ADMIN_SCRIPT}/admin/role");
			}
		}
		
		include_once view('/module/admin/view/role_edit');
	}
	
}
?>