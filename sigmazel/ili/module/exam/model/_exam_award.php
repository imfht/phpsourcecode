<?php
//版权所有(C) 2014 www.ilinei.com

namespace exam\model;

/**
 * 奖品
 * @author sigmazel
 * @since v1.0.2
 */
class _exam_award{
	//获取所有记录
	public function get_all($examid){
		global $db;
		
		$rows = array();
		
		$temp_query = $db->query("SELECT * FROM tbl_exam_award WHERE EXAMID = '{$examid}' AND `LOCK` = 0 ORDER BY DISPLAYORDER ASC");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row = format_row_files($row);
			
			$row['COUNT'] = $db->result_first("SELECT COUNT(1) FROM tbl_exam_user a WHERE a.EXAMID = '{$examid}' AND a.EXAM_AWARDID = '{$row[EXAM_AWARDID]}'") + 0;
			
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	//添加
	public function insert($data){
		global $db;
		
		$db->insert('tbl_exam_award', $data);
		
		return $db->insert_id();
	}
	
	//修改
	public function update($id, $data){
		global $db;
		
		$db->update('tbl_exam_award', $data, "EXAM_AWARDID = '{$id}'");
	}
	
	//批量修改
	public function update_batch($where, $data){
		global $db;
		
		$db->update('tbl_exam_award', $data, $where);
	}
	
	//删除
	public function delete($id){
		global $db;
		
		$db->delete('tbl_exam_award', "EXAM_AWARDID = '{$id}'");
	}
	
	//批量删除
	public function delete_batch($where){
		global $db;
		
		$db->delete('tbl_exam_award', $where);
	}
}
?>