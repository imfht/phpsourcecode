<?php
//版权所有(C) 2014 www.ilinei.com

namespace user\model;

/**
 * 动态
 * @author sigmazel
 * @since v1.0.2
 */
class _feed{
	public function get_count($wheresql){
		global $db;
		
		return $db->result_first("SELECT COUNT(1) FROM tbl_feed a, tbl_user b WHERE a.USERID = b.USERID {$wheresql}") + 0;
	}
	
	public function get_list($start, $perpage, $wheresql){
		global $db;
		
		$perpage > 0 && $limitsql = "LIMIT $start, $perpage";
		
		$rows = array();
		
		$temp_query = $db->query("SELECT a.*, b.REALNAME, b.PHOTO FROM tbl_feed a, tbl_user b WHERE a.USERID = b.USERID {$wheresql} ORDER BY a.EDITTIME DESC {$limitsql}");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row = format_row_files($row);
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	public function insert($data){
		global $db;
		
		$db->insert('tbl_feed', $data);
		
		return $db->insert_id();
	}
	
}
?>