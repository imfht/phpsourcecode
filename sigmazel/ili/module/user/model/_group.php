<?php
//版权所有(C) 2014 www.ilinei.com

namespace user\model;

/**
 * 等级
 * @author sigmazel
 * @since v1.0.2
 */
class _group{
	//根据ID获取记录
	public function get_by_id($id){
		global  $db;
		
		return $db->fetch_first("SELECT * FROM tbl_group WHERE GROUPID = '{$id}'");
	}
	
	//获取所有记录
	public function get_all(){
		global $db;
		
		$temparr = array();
		$temp_query = $db->query("SELECT * FROM tbl_group ORDER BY STARS ASC");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$temparr[$row['GROUPID']] = $row;
		}
		
		return $temparr;
	}
	
	//添加
	public function insert($data){
		global $db;
		
		$db->insert('tbl_group', $data);
		
		return $db->insert_id();
	}
	
	//修改
	public function update($id, $data){
		global $db;
		
		$db->update('tbl_group', $data, "GROUPID = '{$id}'");
	}
	
	//删除
	public function delete($id){
		global $db;
		
		$db->delete('tbl_group', "GROUPID = '{$id}'");
	}
	
}
?>