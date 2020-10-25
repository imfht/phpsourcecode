<?php
//版权所有(C) 2014 www.ilinei.com

namespace admin\model;

/**
 * 日志
 * @author sigmazel
 * @since v1.0.2
 */
class _log{
	//搜索
	public function search(){
		global $_var;
	
		$querystring = $wheresql = '';
		if($_var['gp_txtBeginDate']) {
			$querystring .= '&txtBeginDate='.$_var['gp_txtBeginDate'];
			$wheresql .= " AND a.CREATETIME >= '{$_var[gp_txtBeginDate]}'";
		}
	
		if($_var['gp_txtEndDate']) {
			$querystring .= '&txtEndDate='.$_var['gp_txtEndDate'];
			$wheresql .= " AND a.CREATETIME <= '{$_var[gp_txtEndDate]}'";
		}
	
		if($_var['gp_txtKeyword']) {
			$querystring .= '&txtKeyword='.$_var['gp_txtKeyword'];
			if($_var['gp_sltType'] == 0) $wheresql .= " AND CONCAT(a.TYPE, a.REMARK, a.USERNAME) LIKE '%{$_var[gp_txtKeyword]}%'";
			elseif ($_var['gp_sltType'] == 1) $wheresql .= " AND a.TYPE LIKE '%{$_var[gp_txtKeyword]}%'";
			elseif ($_var['gp_sltType'] == 2) $wheresql .= " AND a.REMARK LIKE '%{$_var[gp_txtKeyword]}%'";
			elseif ($_var['gp_sltType'] == 3) $wheresql .= " AND a.USERNAME LIKE '%{$_var[gp_txtKeyword]}%'";
		}
		
		if($_var['gp_hdnSearchShow']) $querystring .= '&hdnSearchShow='.$_var['gp_hdnSearchShow'];
	
		return array('querystring' => $querystring, 'wheresql' => $wheresql);
	}
	
	//获取数量
	public function get_count($wheresql = ''){
		global $db;
		
		return $db->result_first("SELECT COUNT(1) FROM tbl_log a WHERE 1 {$wheresql}") + 0;
		
	}
	
	//获取列表
	public function get_list($start, $perpage, $wheresql = '', $ordersql = ''){
		global $db;
		
		$rows = array();
		
		!$ordersql && $ordersql = "ORDER BY a.CREATETIME DESC";
		$perpage > 0 && $limitsql = "LIMIT $start, $perpage";
		
		$temp_query = $db->query("SELECT a.* FROM tbl_log a WHERE 1 {$wheresql} {$ordersql} {$limitsql}");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	//写入日志
	public function insert($remark, $type = '', $level = 0){
		global $_var, $db;
		
		$db->insert('tbl_log', array(
		'USERID' => $_var['current'] ? $_var['current']['USERID'] : 0,
		'USERNAME' => $_var['current'] ? $_var['current']['USERNAME'] : 0,
		'TYPE' => $type,
		'LEVEL' => $level,
		'REMARK' => $remark,
		'CREATETIME' => date('Y-m-d H:i:s'),
		'ADDRESS' => $_var['clientip']
		));
	}

	/**
	 * 删除
	 * @param string $where 查询条件
	 */
	public function delete($where = ''){
		global $db;
		
		$db->delete('tbl_log', $where);
	}
	
}
?>