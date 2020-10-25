<?php
//版权所有(C) 2014 www.ilinei.com

namespace user\model;

/**
 * 第三方登录
 * @author sigmazel
 * @since v1.0.2
 */
class _third{
	//根据ID获取记录
	public function get_by_id($id, $type = ''){
		global $db;
		
		if($type) return $db->fetch_first("SELECT * FROM tbl_third WHERE ABOUTID = '{$id}' AND ABOUTTYPE = '{$type}'");
		else{
			$rows = array();
			$temp_query = $db->query("SELECT * FROM tbl_third WHERE ABOUTID = '{$id}'");
			while(($row = $db->fetch_array($temp_query)) !== false){
				$rows[$row['ABOUTTYPE']] = $row;
			}
		
			return $rows;
		}
	}
	
	//根据用户ID获取记录或列表
	public function get_by_userid($userid, $type = ''){
		global $db;
		
		if($type) return $db->fetch_first("SELECT * FROM tbl_third WHERE USERID = '{$userid}' AND ABOUTTYPE = '{$type}'");
		else{
			$rows = array();
			$temp_query = $db->query("SELECT * FROM tbl_third WHERE USERID = '{$userid}'");
			while(($row = $db->fetch_array($temp_query)) !== false){
				$rows[$row['ABOUTTYPE']] = $row;
			}
		
			return $rows;
		}
	}
	
	//添加
	public function insert($id, $type){
		global $db;
		
		$db->insert('tbl_third', array('ABOUTID' => $id, 'ABOUTTYPE' => $type));
	}
	
	//修改
	public function update($id, $type, $userid){
		global $db;
		
		$db->update('tbl_third', array('USERID' => $userid), "ABOUTID = '{$id}' AND ABOUTTYPE = '{$type}'");
	}
	
	//删除
	public function delete($id, $type){
		global $db;
		
		$db->delete('tbl_third', "ABOUTID = '{$id}' AND ABOUTTYPE = '{$type}'");
	}
	
}
?>