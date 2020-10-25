<?php
//版权所有(C) 2014 www.ilinei.com

namespace exam\model;

/**
 * 题目分类
 * @author sigmael
 * @since v1.0.2
 */
class _exam_category{
	//根据ID获取记录
	public function get_by_id($id){
		global $db;
		
		return $db->fetch_first("SELECT * FROM tbl_exam_category WHERE EXAM_CATEGORYID = '{$id}'");
	}
	
	//获取数量
	public function get_count($wheresql = ''){
		global $db;
		
		return $db->result_first("SELECT COUNT(1) FROM tbl_exam_category a WHERE 1 {$wheresql}") + 0;
		
	}
	
	//获取列表
	public function get_list($start , $perpage, $wheresql = '', $ordersql = ''){
		global $db, $setting;
		
		$rows = array();
		
		!$ordersql && $ordersql = "ORDER BY a.EXAMID ASC, a.DISPLAYORDER ASC";
		$perpage > 0 && $limitsql = "LIMIT $start, $perpage";
		
		$temp_query = $db->query("SELECT a.* FROM tbl_exam_category a WHERE 1 {$wheresql} {$ordersql} {$limitsql}");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$rows[$row['EXAM_CATEGORYID']] = $row;
		}
		
		return $rows;
	}
	
	//添加
	public function insert($data){
		global $db;
		
		$db->insert('tbl_exam_category', $data);
		
		return $db->insert_id();
	}
	
	//修改
	public function update($id, $data){
		global $db;
		
		$db->update('tbl_exam_category', $data, "EXAM_CATEGORYID = '{$id}'");
	}
	
	//批量修改
	public function update_batch($where, $data){
		global $db;
		
		$db->update('tbl_exam_category', $data, $where);
	}
	
	//删除
	public function delete($id){
		global $db;
		
		$db->delete('tbl_exam_category', "EXAM_CATEGORYID = '{$id}'");
	}
	
	//批量删除
	public function delete_batch($where){
		global $db;
		
		$db->delete('tbl_exam_category', $where);
	}
	
}
?>