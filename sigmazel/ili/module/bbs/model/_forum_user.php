<?php
//版权所有(C) 2014 www.ilinei.com

namespace bbs\model;

/**
 * 成员
 * @author sigmazel
 * @since v1.0.2
 */
class _forum_user{
	//根据ID获取记录
	public function get_by_id($forumid, $userid){
		global $db;
		
		return $db->fetch_first("SELECT a.*, b.REALNAME, b.EMAIL, b.MOBILE FROM tbl_forum_user a, tbl_user b WHERE a.USERID = b.USERID AND a.FORUMID = '{$forumid}' AND a.USERID = '{$userid}'");
	}
	
	//获取数量
	public function get_count($wheresql = ''){
		global $db;
	
		return $db->result_first("SELECT COUNT(1) FROM tbl_forum_user a, tbl_user b WHERE a.USERID = b.USERID {$wheresql} ");
	}
	
	//获取列表
	public function get_list($start, $perpage, $wheresql = '', $ordersql = ''){
		global $db;
		
		!$ordersql && $ordersql = "ORDER BY a.EDITTIME ASC";
		$perpage > 0 && $limitsql = "LIMIT $start, $perpage";
		
		$rows = array();
	
		$temp_query = $db->query("SELECT a.*, b.REALNAME, b.EMAIL, b.MOBILE FROM tbl_forum_user a, tbl_user b WHERE a.USERID = b.USERID {$wheresql} {$ordersql} {$limitsql}");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	//添加
	public function insert($data){
		global $db;
		
		$db->insert('tbl_forum_user', $data);
		
		return $db->insert_id();
	}
	
	//删除
	public function delete($where){
		global $db;
		
		$db->delete('tbl_forum_user', $where);
	}
}
?>