<?php
//版权所有(C) 2014 www.ilinei.com

namespace poll\model;

/**
 * 投票记录
 * @author sigmazel
 * @since v1.0.2
 */
class _poll_vote{
	//搜索
	public function search(){
		global $_var;
		
		$querystring = $wheresql = $deletesql = '';
		
		if($_var['gp_txtBeginDate']) {
			$querystring .= '&txtBeginDate='.$_var['gp_txtBeginDate'];
			$wheresql .= " AND a.EDITTIME >= '{$_var[gp_txtBeginDate]}'";
			$deletesql .= " AND EDITTIME >= '{$_var[gp_txtBeginDate]}'";
		}
		
		if($_var['gp_txtEndDate']) {
			$querystring .= '&txtEndDate='.$_var['gp_txtEndDate'];
			$wheresql .= " AND a.EDITTIME <= '{$_var[gp_txtEndDate]}'";
			$deletesql .= " AND aEDITTIME <= '{$_var[gp_txtEndDate]}'";
		}
		
		if($_var['gp_txtKeyword']) {
			$_var['gp_txtKeyword'] = trim($_var['gp_txtKeyword']);
			$querystring .= '&txtKeyword='.$_var['gp_txtKeyword'];
			$_var['gp_sltType'] = $_var['gp_sltType'] + 0;
			
			if($_var['gp_sltType'] == 0) {
				$wheresql .= " AND CONCAT(a.USERID, a.USERNAME, a.REALNAME, a.MOBILE, a.REMARK) LIKE '%{$_var[gp_txtKeyword]}%'";
				$deletesql .= " AND CONCAT(USERID, USERNAME, REALNAME, MOBILE, REMARK) LIKE '%{$_var[gp_txtKeyword]}%'";
			}elseif($_var['gp_sltType'] == 1){
				$wheresql .= " AND a.USERID LIKE '%{$_var[gp_txtKeyword]}%'";
				$deletesql .= " AND USERID LIKE '%{$_var[gp_txtKeyword]}%'";
			}elseif($_var['gp_sltType'] == 2){
				$wheresql .= " AND CONCAT(a.USERNAME, a.REALNAME) LIKE '%{$_var[gp_txtKeyword]}%'";
				$deletesql .= " AND CONCAT(USERNAME, REALNAME) LIKE '%{$_var[gp_txtKeyword]}%'";
			}elseif($_var['gp_sltType'] == 3){
				$wheresql .= " AND a.MOBILE LIKE '%{$_var[gp_txtKeyword]}%'";
				$deletesql .= " AND MOBILE LIKE '%{$_var[gp_txtKeyword]}%'";
			}elseif($_var['gp_sltType'] == 4){
				$wheresql .= " AND a.REMARK LIKE '%{$_var[gp_txtKeyword]}%'";
				$deletesql .= " AND REMARK LIKE '%{$_var[gp_txtKeyword]}%'";
			}
		}
		
		if($_var['gp_sltIsAward']) {
			$querystring .= '&sltIsAward='.$_var['gp_sltIsAward'];
			
			if($_var['gp_sltIsAward'] == 1) $wheresql .= " AND a.POLL_AWARDID > 0";
			elseif($_var['gp_sltIsAward'] == 2) $wheresql .= " AND a.POLL_AWARDID = 0";
		}
		
		if($_var['gp_hdnSearchShow']) $querystring .= '&hdnSearchShow='.$_var['gp_hdnSearchShow'];
		
		return array('querystring' => $querystring, 'wheresql' => $wheresql, 'deletesql' => $deletesql);
	}
	
	//根据ID获取记录
	public function get_by_id($id){
		global $db;
		
		return $db->fetch_first("SELECT a.*, b.CNAME FROM tbl_poll_vote a LEFT JOIN tbl_poll_award b ON a.POLL_AWARDID = b.POLL_AWARDID WHERE a.POLL_VOTEID = '{$id}'");
	}
	
	//获取数量
	public function get_count($wheresql = ''){
		global $db;
		
		return $db->result_first("SELECT COUNT(1) FROM tbl_poll_vote a LEFT JOIN tbl_poll_award b ON a.POLL_AWARDID = b.POLL_AWARDID WHERE 1 {$wheresql}");
	}
	
	//获取列表
	public function get_list($start, $perpage, $wheresql = '', $ordersql = ''){
		global $db;
		
		!$ordersql && $ordersql = "ORDER BY a.EDITTIME DESC";
		$perpage > 0 && $limitsql = "LIMIT $start, $perpage";
		
		$rows = array();
		
		$temp_query = $db->query("SELECT a.*, b.CNAME AS AWARD FROM tbl_poll_vote a LEFT JOIN tbl_poll_award b ON a.POLL_AWARDID = b.POLL_AWARDID WHERE 1 {$wheresql} {$ordersql} {$limitsql}");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	//添加
	public function insert($data){
		global $db;
		
		$db->insert('tbl_poll_vote', $data);
		
		return $db->insert_id();
	}
	
	//修改
	public function update($id, $data){
		global $db;
		
		$db->update('tbl_poll_vote', $data, "POLL_VOTEID = '{$id}'");
	}
	
	//批量修改
	public function update_batch($where, $data){
		global $db;
		
		$db->update('tbl_poll_vote', $data, $where);
	}
	
	//删除
	public function delete($id){
		global $db;
		
		$db->delete('tbl_poll_vote', "POLL_VOTEID = '{$id}'");
	}
	
	//批量删除
	public function delete_batch($where){
		global $db;
		
		$db->delete('tbl_poll_vote', $where);
	}
	
}
?>