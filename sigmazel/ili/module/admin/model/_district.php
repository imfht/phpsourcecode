<?php
//版权所有(C) 2014 www.ilinei.com

namespace admin\model;

/**
 * 地区
 * @author sigmazel
 * @since v1.0.2
 */
class _district{
	//根据ID获取记录
	public function get_by_id($id){
		global $db;
		
		return $db->fetch_first("SELECT * FROM tbl_district WHERE DISTRICTID = '{$id}'");
	}
	
	//根据标识号获取记录
	public function get_by_identity($identity){
		global $db;
		
		return $db->fetch_first("SELECT * FROM tbl_district WHERE IDENTITY = '{$identity}'");
	}
	
	//根据名称获取记录
	public function get_by_cname($cname){
		global $db;
		
		return $db->fetch_first("SELECT * FROM tbl_district WHERE CNAME = '{$cname}'");
	}
	
	//获取路径
	public function get_crumbs($id){
		global $db;
		
		$district = $db->fetch_first("SELECT PATH FROM tbl_district WHERE DISTRICTID = '{$id}'");
		
		$crumbs = array();
		$temp_query = $db->query("SELECT DISTRICTID, CNAME FROM tbl_district WHERE INSTR('{$district[PATH]}', PATH) > 0 ORDER BY DISTRICTID ASC");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$crumbs[] = $row;
		}
		
		return $crumbs;
	}
	
	//获取子地区列表
	public function get_children($parentid = 0){
		global $db;
		
		$rows = array();
		$temp_query = $db->query("SELECT * FROM tbl_district WHERE PARENTID = {$parentid} AND ENABLED = 1 ORDER BY DISPLAYORDER ASC");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	//获取数量 
	public function get_count($wheresql = ''){
		global $db;
		
		return $db->result_first("SELECT COUNT(1) FROM tbl_district WHERE 1 {$wheresql}") + 0;
	}
	
	//获取列表
	public function get_list($wheresql = ''){
		global $db;
		
		$rows = array();
	
		$temp_query = $db->query("SELECT * FROM tbl_district WHERE 1 {$wheresql} ORDER BY DISPLAYORDER ASC");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$rows[] = $row;
		}
	
		return $rows;
	}
	
	//获取所有记录
	public function get_all(){
		global $db;
		
		$rows = array();
		$temp_query = $db->query("SELECT * FROM tbl_district ORDER BY DISTRICTID ASC");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$rows[$row['DISTRICTID']] = $row;
		}
		
		return $rows;
	}
	
	//添加
	public function insert($data){
		global $db;
		
		$db->insert('tbl_district', $data);
		
		return $db->insert_id();
	}
	
	//修改
	public function update($id, $data){
		global $db;
		
		$db->update('tbl_district', $data, "DISTRICTID = '{$id}'");
	}
	
	//删除
	public function delete($id){
		global $db;
		
		$db->delete('tbl_district', "DISTRICTID = '{$id}'");
		$db->delete('tbl_district', "PARENTID = '{$id}'");
	}
	
}
?>