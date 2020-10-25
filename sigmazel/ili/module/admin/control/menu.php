<?php
//版权所有(C) 2014 www.ilinei.com

namespace admin\control;

use admin\model\_log;
use admin\model\_menu;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/admin/lang.php';

//菜单
class menu{
	//默认
	public function index(){
		global $_var;
		
		$_log = new _log();
		$_menu = new _menu();
		
		$micro_icons = $_menu->get_micons();
		$operation_list = $_menu->get_operations();
		
		$wheresql = ' AND PARENTID = 0';
	
		$_var['gp_parentid'] = $_var['gp_parentid'] + 0;
		if($_var['gp_parentid'] > 0) {
			$parent = $_menu->get_by_id($_var['gp_parentid']);
			$_var['gp_parentid'] = $parent ? $parent['MENUID'] : 0;
			if($_var['gp_parentid']) {
				$crumbs = $_menu->get_crumbs($parent);
				$wheresql = " AND PARENTID = '{$_var[gp_parentid]}'";
			}
		}
		
		if(is_array($_var['gp_displayorder'])){
			foreach ($_var['gp_displayorder'] as $key => $val){
				$children = $_menu->get_count("AND PARENTID = '{$key}'");
				
				$_menu->update($key, array(
				'DISPLAYORDER' => $_var['gp_displayorder'][$key], 
				'CHILDREN' => $children
				));
				
				unset($children);
			}
		}
		
		if($_var['gp_do'] == 'delete' && $_var['gp_id'] > 0){
			$menu = $_menu->get_by_id($_var['gp_id']);
			
			if($menu){
				$_menu->delete($menu['MENUID']);
				
				if($parent){
					$parent['CHILDREN'] = $parent['CHILDREN'] - 1;
					$_menu->update($parent['MENUID'], array('CHILDREN' => $parent['CHILDREN']));
				}
				
				$_log->insert($GLOBALS['lang']['admin.menu.log.delete']."({$menu[CNAME]})", $GLOBALS['lang']['admin.menu']);
			}
		}elseif($_var['gp_do'] == 'delete_list' && is_array($_var['gp_cbxItem'])){
			$menu_names = '';
			$menu_count = 0;
			
			foreach ($_var['gp_cbxItem'] as $key => $val){
				$menu = $_menu->get_by_id($val);
				
				if(!$menu) continue;
				
				$_menu->delete($menu['MENUID']);
				
				$menu_count++;
				$menu_names .= $menu['CNAME'].', ';
				
				unset($menu);
			}
			
			if($parent){
				$parent['CHILDREN'] = $parent['CHILDREN'] - $menu_count;
				$_menu->update($parent['MENUID'], array('CHILDREN' => $parent['CHILDREN']));
			}
			
			$_log->insert($GLOBALS['lang']['admin.menu.log.delete.list']."({$menu_names})", $GLOBALS['lang']['admin.menu']);
		}
		
		$count = $_menu->get_count($wheresql);
		if($count){
			$menus = $_menu->get_list($wheresql);
		}
		
		include_once view('/module/admin/view/menu');
	}
	
	//移动
	public function _move(){
		global $_var;
		
		$_log = new _log();
		$_menu = new _menu();
		
		$menu_list = $_menu->get_children(0);
	
		if($_var['gp_formsubmit'] && $_var['gp_hdnMoveMenuID'] + 0 > 0){
			$move_menu = $_menu->get_by_id($_var['gp_hdnMoveMenuID']);
			if($move_menu){
				$menu_names = '';
				
				foreach ($_var['gp_cbxItem'] as $key => $val){
					$menu = $_menu->get_by_id($val);
					if(!$menu) continue;
					
					$_menu->update($menu['MENUID'], array(
					'PARENTID' => $move_menu['MENUID'], 
					'PATH' => $move_menu['PATH'].','.$menu['MENUID'].','
					));
					
					$parent = $menu['PARENTID'] ? $_menu->get_by_id($menu['PARENTID']) : null;
					
					if($parent){
						$parent['CHILDREN'] = $parent['CHILDREN'] - 1;
						$_menu->update($parent['MENUID'], array('CHILDREN' => $parent['CHILDREN']));
					}
					
					$move_menu['CHILDREN'] = $move_menu['CHILDREN'] + 1;
					$_menu->update($move_menu['MENUID'], array('CHILDREN' => $move_menu['CHILDREN']));
					
					$menu_names .= $menu['CNAME'].', ';
					
					unset($menu);
					unset($parent);
				}
				
				$_log->insert($GLOBALS['lang']['admin.menu.log.move.list']."({$menu_names})", $GLOBALS['lang']['admin.menu']);
			}
			
			show_message($GLOBALS['lang']['admin.menu.message.move.list'], "{ADMIN_SCRIPT}/admin/menu&parentid={$_var[gp_parentid]}");
		}
		
		include_once view('/module/admin/view/menu.move');
	}
	
	//添加
	public function _add(){
		global $_var;
		
		$_log = new _log();
		$_menu = new _menu();
		
		$micro_icons = $_menu->get_micons();
		$operation_list = $_menu->get_operations();
		
		$_var['gp_parentid'] = $_var['gp_parentid'] + 0;
		if($_var['gp_parentid'] > 0) {
			$parent = $_menu->get_by_id($_var['gp_parentid']);
			$_var['gp_parentid'] = $parent ? $parent['MENUID'] : 0;
		}
		
		if($_var['gp_formsubmit']){
			
			$_var['msg'] = '';
			if(empty($_var['gp_txtDisplayOrder'])) $_var['msg'] .= $GLOBALS['lang']['admin.menu_edit.validate.no']."<br/>";
			if(empty($_var['gp_txtCName'])) $_var['msg'] .= $GLOBALS['lang']['admin.menu_edit.validate.cname']."<br/>";
			
			if(empty($_var['msg'])){
				$menuid = $_menu->insert(array(
				'PARENTID' => $_var['gp_parentid'],
				'CNAME' => utf8substr($_var['gp_txtCName'], 0, 30),
				'URL' => utf8substr($_var['gp_txtUrl'], 0, 100),
				'REMARK' => utf8substr($_var['gp_txtRemark'], 0, 100),
				'DISPLAYORDER' => $_var['gp_txtDisplayOrder'] + 0,
				'TYPE' => $_var['gp_rdoType'] + 0, 
				'EDITER' => $_var['current']['USERNAME'], 
				'EDITTIME' => date('Y-m-d H:i:s'), 
				'OPERATIONS' => serialize($_var['gp_operations']), 
				'ICON' => $_var['gp_rdoIcon'].''
				));
				
				$_menu->update($menuid, array('PATH' => ($parent ? $parent['PATH'].','.$menuid.',' : ','.$menuid.',')));
				if($parent) $_menu->update($parent['MENUID'], array('CHILDREN' => $parent['CHILDREN'] + 1));
				
				$_log->insert($GLOBALS['lang']['admin.menu.log.add']."({$_var[gp_txtCName]})", $GLOBALS['lang']['admin.menu']);
				
				show_message($GLOBALS['lang']['admin.menu.message.add'], "{ADMIN_SCRIPT}/admin/menu&parentid={$_var[gp_parentid]}");
			}
		}
		
		include_once view('/module/admin/view/menu_edit');
	}
	
	//修改
	public function _update(){
		global $_var;
		
		$_log = new _log();
		$_menu = new _menu();
		
		$micro_icons = $_menu->get_micons();
		$operation_list = $_menu->get_operations();
		
		$_var['gp_parentid'] = $_var['gp_parentid'] + 0;
		if($_var['gp_parentid'] > 0) {
			$parent = $_menu->get_by_id($_var['gp_parentid']);
			$_var['gp_parentid'] = $parent ? $parent['MENUID'] : 0;
		}
		
		$id = $_var['gp_id'] + 0;
		if($id == 0) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/admin/menu");
		
		$menu = $_menu->get_by_id($id);
		if($menu == null) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/admin/menu"); 
		
		if($_var['gp_formsubmit']){
			$_var['msg'] = '';
			if(empty($_var['gp_txtDisplayOrder'])) $_var['msg'] .= $GLOBALS['lang']['admin.menu_edit.validate.no']."<br/>";
			if(empty($_var['gp_txtCName'])) $_var['msg'] .= $GLOBALS['lang']['admin.menu_edit.validate.cname']."<br/>";
			
			if(empty($_var['msg'])){
				$_menu->update($id, array(
				'CNAME' => utf8substr($_var['gp_txtCName'], 0, 30),
				'URL' => utf8substr($_var['gp_txtUrl'], 0, 100),
				'REMARK' => utf8substr($_var['gp_txtRemark'], 0, 100),
				'DISPLAYORDER' => $_var['gp_txtDisplayOrder'] + 0,
				'TYPE' => $_var['gp_rdoType'] + 0, 
				'EDITER' => $_var['current']['USERNAME'], 
				'EDITTIME' => date('Y-m-d H:i:s'), 
				'OPERATIONS' => serialize($_var['gp_operations']), 
				'ICON' => $_var['gp_rdoIcon'].''
				));
				
				$_log->insert($GLOBALS['lang']['admin.menu.log.update']."({$_var[gp_txtCName]})", $GLOBALS['lang']['admin.menu']);
				
				show_message($GLOBALS['lang']['admin.menu.message.update'], "{ADMIN_SCRIPT}/admin/menu&parentid={$_var[gp_parentid]}");
			}
		}
		
		include_once view('/module/admin/view/menu_edit');
	}
	
	//子菜单
	public function _children(){
		global $_var;
		
		$_menu = new _menu();
		
		$parentid = $_var['gp_parentid'] + 0;
		$child_menus = $parentid > 0 ? $_menu->get_children($_var['gp_parentid']) : array();
		
		echo(json_encode($child_menus));
	}
	
}
?>