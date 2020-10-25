<?php
//版权所有(C) 2014 www.ilinei.com

namespace poll\model;

/**
 * 投票奖项
 * @author sigmazel
 * @since v1.0.2
 */
class _poll_award{
	//根据ID获取记录
	public function get_by_id($id){
		global $db;
		
		return $db->fetch_first("SELECT * FROM tbl_poll_award WHERE POLL_AWARDID = '{$id}'");
	}
	
	//获取列表
	public function get_list($start , $perpage, $wheresql = '', $ordersql = ''){
		global $db, $setting;
		
		$rows = array();
		
		!$ordersql && $ordersql = "ORDER BY a.DISPLAYORDER ASC";
		$perpage > 0 && $limitsql = "LIMIT $start, $perpage";
		
		$temp_query = $db->query("SELECT a.* FROM tbl_poll_award a WHERE 1 {$wheresql} {$ordersql} {$limitsql}");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row = format_row_files($row);
			
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	//获取所有记录
	public function get_all($pollid){
		global $db;
		
		$rows = array();
		$temp_query = $db->query("SELECT a.* FROM tbl_poll_award a WHERE a.POLLID = '{$pollid}' ORDER BY a.DISPLAYORDER DESC");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row = format_row_files($row);
			$row['COUNT'] = $db->result_first("SELECT COUNT(1) FROM tbl_poll_vote a WHERE a.POLLID = '{$pollid}' AND a.POLL_AWARDID = '{$row[POLL_AWARDID]}'") + 0;
			
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	//添加
	public function insert($data){
		global $db;
		
		$db->insert('tbl_poll_award', $data);
		
		return $db->insert_id();
	}
	
	//修改
	public function update($id, $data){
		global $db;
		
		$db->update('tbl_poll_award', $data, "POLL_AWARDID = '{$id}'");
	}
	
	//批量修改
	public function update_batch($where, $data){
		global $db;
		
		$db->update('tbl_poll_award', $data, $where);
	}
	
	//删除
	public function delete($id){
		global $db;
		
		$db->delete('tbl_poll_award', "POLL_AWARDID = '{$id}'");
	}
	
	//批量删除
	public function delete_batch($where){
		global $db;
		
		$db->delete('tbl_poll_award', $where);
	}
	
}
?>