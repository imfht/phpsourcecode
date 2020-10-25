<?php
//版权所有(C) 2014 www.ilinei.com

namespace wx\model;

/**
 * 微信粉丝
 * @author sigmazel
 * @since v1.0.2
 */
class _wx_fans{
	//搜索
	public function search(){
		global $_var;
	
		$querystring = '';
		$wheresql = ' ';
		
		if($_var['gp_txtBeginDate']) {
			$querystring .= '&txtBeginDate='.$_var['gp_txtBeginDate'];
			$wheresql .= " AND a.SUBSCRIBE_TIME >= '{$_var[gp_txtBeginDate]}'";
		}
		
		if($_var['gp_txtEndDate']) {
			$querystring .= '&txtEndDate='.$_var['gp_txtEndDate'];
			$wheresql .= " AND a.SUBSCRIBE_TIME <= '{$_var[gp_txtEndDate]}'";
		}
		
		if($_var['gp_txtKeyword']) {
			$_var['gp_txtKeyword'] = trim($_var['gp_txtKeyword']);
			$querystring .= '&txtKeyword='.$_var['gp_txtKeyword'];
			$wheresql .= " AND CONCAT(a.NICKNAME, a.CITY, a.COUNTRY, a.PROVINCE, a.OPENID) LIKE '%{$_var[gp_txtKeyword]}%'";
		}
		
		if($_var['gp_sltType']) {
			$subtimer = strtotime('1970-08-01');
			
			$querystring .= '&sltType='.$_var['gp_sltType'];
			if($_var['gp_sltType'] == 1) $wheresql .= " AND a.BIND  = 1";
			elseif($_var['gp_sltType'] == 2) $wheresql .= " AND a.BIND  = 0";
			elseif($_var['gp_sltType'] == 3) $wheresql .= " AND a.BIND  = 0 AND a.SUBSCRIBE_TIME > '{$subtimer}'";
		}
		
		if($_var['gp_hdnSearchShow']) $querystring .= '&hdnSearchShow='.$_var['gp_hdnSearchShow'];
		
		return array('querystring' => $querystring, 'wheresql' => $wheresql);
	}
	
	//根据ID获取记录
	public function get_by_id($id){
		global  $db;
	
		return $db->fetch_first("SELECT * FROM tbl_wx_fans WHERE WX_FANSID = '{$id}'");
	}
	
	//根据OPENID获取记录
	public function get_by_openid($openid){
		global $db;
	
		return $db->fetch_first("SELECT * FROM tbl_wx_fans WHERE OPENID = '{$openid}' ");
	}
	
	//根据AUTH获取记录
	public function get_by_auth($auth){
		global  $db;
	
		return $db->fetch_first("SELECT * FROM tbl_wx_fans WHERE AUTH = '{$auth}'");
	}
	
	//获取数量
	public function get_count($wheresql = ''){
		global $db;
		
		return $db->result_first("SELECT COUNT(1) FROM tbl_wx_fans a WHERE 1 {$wheresql}") + 0;
		
	}
	
	//获取列表
	public function get_list($start, $perpage, $wheresql = '', $ordersql = ''){
		global $db;
		
		$rows = array();
		
		!$ordersql && $ordersql = "ORDER BY a.SUBSCRIBE_TIME DESC";
		$perpage > 0 && $limitsql = "LIMIT $start, $perpage";
		
		$temp_query = $db->query("SELECT a.* FROM tbl_wx_fans a WHERE 1 {$wheresql} {$ordersql} {$limitsql}");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	//获取所有记录
	public function get_all($wheresql = '', $ordersql = ''){
		global $db;
		
		$rows = array();
		
		!$ordersql && $ordersql = "ORDER BY a.SUBSCRIBE_TIME DESC";
		
		$temp_query = $db->query("SELECT a.* FROM tbl_wx_fans a WHERE 1 {$wheresql} {$ordersql}");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	//添加
	public function insert($data){
		global $db;
		
		$db->insert('tbl_wx_fans', $data);
		
		return $db->insert_id();
	}
	
	//修改
	public function update($id, $data){
		global $db;
		
		$db->update('tbl_wx_fans', $data, "WX_FANSID = '{$id}'");
	}
	
	//删除
	public function delete($where = ''){
		global $db;
		
		$db->delete('tbl_wx_fans', $where);
	}
	
}
?>