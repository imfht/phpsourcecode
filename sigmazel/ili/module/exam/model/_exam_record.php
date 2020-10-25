<?php
//版权所有(C) 2014 www.ilinei.com

namespace exam\model;

/**
 * 答题记录
 * @author sigmazel
 * @since v1.0.2
 */
class _exam_record{
	//根据ID获取记录
	public function get_by_id($id){
		global $db;
		
		return $db->fetch_first("SELECT * FROM tbl_exam_record WHERE EXAM_RECORDID = '{$id}'");
	}
	
	//获取数量
	public function get_count($wheresql = ''){
		global $db;
		
		return $db->result_first("SELECT COUNT(1) FROM tbl_exam_record a WHERE 1 {$wheresql}") + 0;
	}
	
	//获取列表
	public function get_list($start , $perpage, $wheresql = '', $ordersql = ''){
		global $db, $setting;
		
		$rows = array();
		
		!$ordersql && $ordersql = "ORDER BY a.ANSWERTIME ASC";
		$perpage > 0 && $limitsql = "LIMIT $start, $perpage";
		
		$temp_query = $db->query("SELECT a.* FROM tbl_exam_record a WHERE 1 {$wheresql} {$ordersql} {$limitsql}");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	//获取记录+题目
	public function get_list_of_option($start , $perpage, $wheresql = '', $ordersql = ''){
		global $db;
		
		$_exam_option = new _exam_option();
		
		!$ordersql && $ordersql = "ORDER BY a.ANSWERTIME ASC";
		$perpage > 0 && $limitsql = "LIMIT $start, $perpage";
		
		$rows = array();
		
		$temp_query = $db->query("SELECT a.*, b.RETYPE, b.TITLE, b.FILE01, b.FILE02, b.FILE03, b.FILE04, b.FILE05, b.FILE06 FROM tbl_exam_record a, tbl_exam_option b WHERE a.EXAM_OPTIONID = b.EXAM_OPTIONID {$wheresql} {$ordersql} {$limitsql}");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row = $_exam_option->format($row);
			
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	//添加
	public function insert($data){
		global $db;
		
		$db->insert('tbl_exam_record', $data);
		
		return $db->insert_id();
	}
	
	//修改
	public function update($id, $data){
		global $db;
		
		$db->update('tbl_exam_record', $data, "EXAM_RECORDID = '{$id}'");
	}
	
	//删除
	public function delete($id){
		global $db;
		
		$db->delete('tbl_exam_record', "EXAM_RECORDID = '{$id}'");
	}
	
	//批量删除
	public function delete_batch($where){
		global $db;
		
		$db->delete('tbl_exam_record', $where);
	}
	

}
?>