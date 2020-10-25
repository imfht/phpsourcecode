<?php
//版权所有(C) 2014 www.ilinei.com

namespace note\model;

/**
 * 留言记录
 * @author sigmazel
 * @since v1.0.2
 */
class _note_record{
	//搜索
	public function search(){
		global $_var;
		
		$querystring = $wheresql = '';
		
		if($_var['gp_txtBeginDate']) {
			$querystring .= '&txtBeginDate='.$_var['gp_txtBeginDate'];
			$wheresql .= " AND a.EDITTIME >= '{$_var[gp_txtBeginDate]}'";
		}
		
		if($_var['gp_txtEndDate']) {
			$querystring .= '&txtEndDate='.$_var['gp_txtEndDate'];
			$wheresql .= " AND a.EDITTIME <= '{$_var[gp_txtEndDate]}'";
		}
		
		if($_var['gp_txtKeyword']) {
			$querystring .= '&txtKeyword='.$_var['gp_txtKeyword'];
			$_var['gp_sltType'] = $_var['gp_sltType'] + 0;
			if($_var['gp_sltType'] == 0) $wheresql .= " AND CONCAT(a.USERNAME, a.TITLE, a.DEPARTMENT, a.PLACE, a.EMAIL, a.CONNECT) LIKE '%{$_var[gp_txtKeyword]}%'";
			elseif ($_var['gp_sltType'] == 1) $wheresql .= " AND a.USERNAME LIKE '%{$_var[gp_txtKeyword]}%'";
			elseif ($_var['gp_sltType'] == 2) $wheresql .= " AND a.TITLE LIKE '%{$_var[gp_txtKeyword]}%'";
			elseif ($_var['gp_sltType'] == 3) $wheresql .= " AND a.DEPARTMENT LIKE '%{$_var[gp_txtKeyword]}%'";
			elseif ($_var['gp_sltType'] == 4) $wheresql .= " AND a.PLACE LIKE '%{$_var[gp_txtKeyword]}%'";
			elseif ($_var['gp_sltType'] == 5) $wheresql .= " AND a.EMAIL LIKE '%{$_var[gp_txtKeyword]}%'";
			elseif ($_var['gp_sltType'] == 6) $wheresql .= " AND a.CONNECT LIKE '%{$_var[gp_txtKeyword]}%'";
		}
		
		if($_var['gp_sltSNoteId']) {
			$querystring .= '&sltSNoteId='.$_var['gp_sltSNoteId'];
			$wheresql .= " AND a.NOTEID  = '{$_var[gp_sltSNoteId]}'";
		}
		
		if($_var['gp_sltIsReply']) {
			$querystring .= '&sltIsReply='.$_var['gp_sltIsReply'];
			if($_var['gp_sltIsReply'] == 1) $wheresql .= " AND LENGTH(a.REPLY) > 0";
			elseif($_var['gp_sltIsReply'] == 2) $wheresql .= " AND LENGTH(a.REPLY) = 0";
		}
		
		if($_var['gp_sltIsOpen']) {
			$querystring .= '&sltIsOpen='.$_var['gp_sltIsOpen'];
			if($_var['gp_sltIsOpen'] == 1) $wheresql .= " AND a.ISOPEN = 1";
			elseif($_var['gp_sltIsOpen'] == 2) $wheresql .= " AND a.ISOPEN = 0";
		}
		
		if($_var['gp_hdnSearchShow']) $querystring .= '&hdnSearchShow='.$_var['gp_hdnSearchShow'];
		
		return array('querystring' => $querystring, 'wheresql' => $wheresql);
	}
	
	//根据ID获取记录
	public function get_by_id($recordid){
		global $db;
		
		return $db->fetch_first("SELECT a.*, b.TITLE AS NOTENAME, b.REPLY AS ISREPLY FROM tbl_note_record a, tbl_note b WHERE a.NOTEID = b.NOTEID AND a.NOTE_RECORDID = '{$recordid}'");
	}
	
	//获取数量
	public function get_count($wheresql = ''){
		global $db;
		
		return $db->result_first("SELECT COUNT(1) FROM tbl_note_record a, tbl_note b WHERE a.NOTEID = b.NOTEID {$wheresql}") + 0;
	}
	
	//获取列表
	public function get_list($start, $perpage, $wheresql = '', $ordersql = ''){
		global $db;
		
		!$ordersql && $ordersql = "ORDER BY a.ISCOMMEND DESC, a.EDITTIME DESC";
		$perpage > 0 && $limitsql = "LIMIT $start, $perpage";
		
		$rows = array();
		$temp_query = $db->query("SELECT a.*, b.TITLE AS NOTENAME, b.REPLY AS ISREPLY FROM tbl_note_record a , tbl_note b WHERE a.NOTEID = b.NOTEID {$wheresql} {$ordersql} {$limitsql}");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$rows[] = $row;
		}
	
		return $rows;
	}
	
	//添加
	public function insert($data){
		global $db;
		
		$db->insert('tbl_note_record', $data);
		
		return $db->insert_id();
	}
	
	//修改
	public function update($id, $data){
		global $db;
		
		$db->update('tbl_note_record', $data, "NOTE_RECORDID = '{$id}'");
	}
	
	//批量修改
	public function update_batch($where, $data){
		global $db;
		
		$db->update('tbl_note_record', $data, $where);
	}
	
	//删除
	public function delete($id){
		global $db;
		
		$db->delete('tbl_note_record', "PARENTID = '{$id}'");
		$db->delete('tbl_note_record', "NOTE_RECORDID = '{$id}'");
	}
	
	
	//更新点击数
	public function flash_hits($id){
		global $db;
	
		$db->query("UPDATE tbl_note_record SET HITS = HITS + 1 WHERE NOTE_RECORDID = '{$id}'");
	}
}
?>