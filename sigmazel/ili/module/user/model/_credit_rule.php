<?php
//版权所有(C) 2014 www.ilinei.com

namespace user\model;

/**
 * 积分规则 
 * @author sigmazel
 * @since v1.0.2
 */
class _credit_rule{
	//根据ID获取记录
	public function get_by_id($id){
		global $db;
		
		return $db->fetch_first("SELECT a.* FROM tbl_credit_rule a WHERE a.CREDIT_RULEID = '{$id}'");
	}
	
	//根据动作获取记录
	public function get_by_action($action){
		global $db;
		
		return $db->fetch_first("SELECT a.* FROM tbl_credit_rule a WHERE a.ACTION = '{$action}'");
	}
	
	//获取所有记录
	public function get_all(){
		global $db;
		
		$temparr = array();
		$temp_query = $db->query("SELECT * FROM tbl_credit_rule ORDER BY CREDIT_RULEID ASC");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$temparr[] = $row;
		}
		
		return $temparr;
	}
	
	//添加
	public function insert($data){
		global $db;
		
		$db->insert('tbl_credit_rule', $data);
		
		return $db->insert_id();
	}
	
	//修改
	public function update($id, $data){
		global $db;
		
		$db->update('tbl_credit_rule', $data, "CREDIT_RULEID = '{$id}'");
	}
	
	//删除
	public function delete($id){
		global $db;
		
		$db->delete('tbl_credit_rule', "CREDIT_RULEID = '{$id}'");
	}
	
}
?>