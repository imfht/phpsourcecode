<?php
//版权所有(C) 2014 www.ilinei.com

namespace cms\model;

/**
 * 专题
 * @author sigmazel
 * @since v1.0.2
 */
class _subject{
	//搜索
	public function search(){
		global $_var;
		
		$querystring = $wheresql = '';
		
		if($_var['gp_txtKeyword']) {
			$_var['gp_txtKeyword'] = trim($_var['gp_txtKeyword']);
			$querystring .= '&txtKeyword='.$_var['gp_txtKeyword'];
			$wheresql .= " AND CONCAT(a.TITLE, a.SUMMARY) LIKE '%{$_var[gp_txtKeyword]}%'";
		}
		
		if($_var['gp_txtBeginDate']) {
			$querystring .= '&txtBeginDate='.$_var['gp_txtBeginDate'];
			$wheresql .= " AND a.PUBDATE >= '{$_var[gp_txtBeginDate]}'";
		}
		
		if($_var['gp_txtEndDate']) {
			$querystring .= '&txtEndDate='.$_var['gp_txtEndDate'];
			$wheresql .= " AND a.PUBDATE <= '{$_var[gp_txtEndDate]}'";
		}
		
		if($_var['gp_sltIsTop']) {
			$querystring .= '&sltIsTop='.$_var['gp_sltIsTop'];
			if($_var['gp_sltIsTop'] == 1) $wheresql .= " AND a.ISTOP  = 1";
			elseif($_var['gp_sltIsTop'] == 2) $wheresql .= " AND a.ISTOP  = 0";
		}
		
		if($_var['gp_sltIsCommend']) {
			$querystring .= '&sltIsCommend='.$_var['gp_sltIsCommend'];
			$wheresql .= " AND a.ISCOMMEND  = '{$_var[gp_sltIsCommend]}'";
		}
		
		if($_var['gp_sltIsAudit']) {
			$querystring .= '&sltIsAudit='.$_var['gp_sltIsAudit'];
			if($_var['gp_sltIsAudit'] == 1) $wheresql .= " AND a.ISAUDIT  = 1";
			elseif($_var['gp_sltIsAudit'] == 2) $wheresql .= " AND a.ISAUDIT  = 0";
		}
		
		return array('querystring' => $querystring, 'wheresql' => $wheresql);
	}
	
	//根据ID获取记录
	public function get_by_id($id){
		global $db;
		
		return $db->fetch_first("SELECT a.* FROM tbl_subject a WHERE a.SUBJECTID = '{$id}'");
	}
	
	//根据标识号获取记录
	public function get_by_identity($identity){
		global $db;
		
		return $db->fetch_first("SELECT a.* FROM tbl_subject a WHERE a.IDENTITY = '{$identity}'");
	}
	
	//获取数量
	public function get_count($wheresql = ''){
		global $db;
	
		return $db->result_first("SELECT COUNT(1) FROM tbl_subject a WHERE 1 {$wheresql}") + 0;
	}
	
	//获取列表
	public function get_list($start, $perpage, $wheresql = '', $ordersql = ''){
		global $db;
		
		!$ordersql && $ordersql = "ORDER BY a.ISTOP DESC, a.ISCOMMEND DESC, a.PUBDATE DESC";
		$perpage > 0 && $limitsql = "LIMIT $start, $perpage";
		
		$rows = array();
	
		$temp_query = $db->query("SELECT a.* FROM tbl_subject a WHERE 1 {$wheresql} {$ordersql} {$limitsql}");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row['EXPRIED'] = $row['EXPRIED'] > 0 ? date('Y-m-d H:i', strtotime($row['EXPRIED'])) : '';
			
			$row = format_row_files($row);
			
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	//获取所有记录
	public function get_all($isaudit = -1){
		global $db;
		
		if($isaudit == -1) $wheresql = '';
		elseif($isaudit == 0) $wheresql = 'AND a.ISAUDIT = 0';
		elseif($isaudit == 1) $wheresql = 'AND a.ISAUDIT = 1';
		else $wheresql = '';
		
		$rows = array();
		
		$temp_query = $db->query("SELECT a.* FROM tbl_subject a WHERE 1 {$wheresql} ORDER BY a.ISTOP DESC, a.ISCOMMEND DESC, a.PUBDATE DESC");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row['SUMMARY'] = nl2br($row['SUMMARY']);
			$row = format_row_files($row);
			
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	//获取文件
	public function get_files($subject, $filenum){
		$subject_files = array();
		for($i = 1; $i <= $filenum; $i++){
			if(is_array($subject['FILE'.sprintf('%02d', $i)])) $subject_files[] = $subject['FILE'.sprintf('%02d', $i)];
		}
	
		return $subject_files;
	}
	
	//添加
	public function insert($data){
		global $db;
		
		$db->insert('tbl_subject', $data);
		
		return $db->insert_id();
	}
	
	//修改
	public function update($id, $data){
		global $db;
		
		$db->update('tbl_subject', $data, "SUBJECTID = '{$id}'");
	}
	
	//删除
	public function delete($id){
		global $db;
		
		$db->delete('tbl_subject', "SUBJECTID = '{$id}'");
		$db->update('tbl_article', array('SUBJECTID' => 0), "SUBJECTID = '{$id}'");
	}
	
}
?>