<?php
//版权所有(C) 2014 www.ilinei.com

namespace wx\model;

/**
 * 自定义菜单
 * @author sigmazel
 * @since v1.0.2
 */
class _wx_menu{
	//根据ID获取记录
	public function get_by_id($id){
		global $db;
		
		return $db->fetch_first("SELECT * FROM tbl_wx_menu a WHERE a.WX_MENUID = '{$id}'");
	}
	
	//根据KEY获取记录
	public function get_by_key($key){
		global $db;
		
		return $db->fetch_first("SELECT * FROM tbl_wx_menu WHERE URL LIKE '{$key}%'");
	}
	
	//获取菜单树
	public function get_tree(){
		global $db;
		
		$menus = array();
		
		$temp_query = $db->query("SELECT * FROM tbl_wx_menu WHERE PARENTID = 0 ORDER BY DISPLAYORDER ASC");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row['CHILDREN'] = array();
			$menus[] = $row;
		}
		
		$temp_query = $db->query("SELECT * FROM tbl_wx_menu WHERE PARENTID > 0 ORDER BY PARENTID ASC, DISPLAYORDER ASC");
		while(($row = $db->fetch_array($temp_query)) !== false){
			foreach($menus as $key => $menu){
				if($menu['WX_MENUID'] == $row['PARENTID']) $menus[$key]['CHILDREN'][] = $row;
			}
		}
		
		return $menus;
	}
	
	//添加
	public function insert($data){
		global $db;
		
		$db->insert('tbl_wx_menu', $data);
		
		return $db->insert_id();
	}
	
	//修改
	public function update($id, $data){
		global $db;
		
		$db->update('tbl_wx_menu', $data, "WX_MENUID = '{$id}'");
	}
	
	//删除
	public function delete($id){
		global $db;
		
		$db->delete('tbl_wx_menu', "WX_MENUID = '{$id}'");
		$db->delete('tbl_wx_menu', "PARENTID = '{$id}'");
	}
	
}
?>