<?php
//版权所有(C) 2014 www.ilinei.com

namespace admin\model;

/**
 * 角色
 * @author sigmazel
 * @since v1.0.2
 */
class _role{
	//根据ID获取记录
	public function get_by_id($id){
		global  $db;
		
		return $db->fetch_first("SELECT * FROM tbl_role WHERE ROLEID = '{$id}'");
	}
	
	//获得列表
	public function get_menus($id){
		global $db;
		
		$temparr = array();
		$temp_query = $db->query("SELECT * FROM tbl_role_menu WHERE ROLEID = '{$id}'");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row['OPERATIONS'] = unserialize($row['OPERATIONS']);
			$temparr[$row['MENUID']] = $row;
		}
		
		return $temparr;
	}
	
	//获取所有记录
	public function get_all(){
		global $db;
		
		$temparr = array();
		$temp_query = $db->query("SELECT * FROM tbl_role ORDER BY DISPLAYORDER ASC");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$temparr[$row['ROLEID']] = $row;
		}
		
		return $temparr;
	}
	
	//添加
	public function insert($data){
		global $db;
		
		$db->insert('tbl_role', $data);
		
		return $db->insert_id();
	}
	
	//添加菜单
	public function insert_menu($id, $menus){
		global $db;
		
		$db->query("DELETE FROM tbl_role_menu WHERE ROLEID = '{$id}'");
				
		if(is_array($menus)){
			foreach ($menus as $key => $val){
				$db->insert('tbl_role_menu', array(
				'ROLEID' => $id, 
				'MENUID' => $key, 
				'OPERATIONS' => $val
				));
			}
		}
	}
	
	//修改
	public function update($id, $data){
		global $db;
		
		$db->update('tbl_role', $data, "ROLEID = '{$id}'");
	}
	
	//删除
	public function delete($id){
		global $db;
		
		$db->delete('tbl_role', "ROLEID = '{$id}'");
		$db->delete('tbl_role_menu', "ROLEID = '{$id}'");
	}
	
}
?>