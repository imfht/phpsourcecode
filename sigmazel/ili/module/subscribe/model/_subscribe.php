<?php
//版权所有(C) 2014 www.ilinei.com

namespace subscribe\model;

/**
 * 订阅
 * @author sigmazel
 * @since v1.0.2
 */
class _subscribe{
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
			if($_var['gp_sltType'] == 0) $wheresql .= " AND CONCAT(a.EMAIL, a.ADDRESS) LIKE '%{$_var[gp_txtKeyword]}%'";
			elseif ($_var['gp_sltType'] == 1) $wheresql .= " AND a.EMAIL LIKE '%{$_var[gp_txtKeyword]}%'";
			elseif ($_var['gp_sltType'] == 2) $wheresql .= " AND a.ADDRESS LIKE '%{$_var[gp_txtKeyword]}%'";
		}
		
		if($_var['gp_hdnSearchShow']) $querystring .= '&hdnSearchShow='.$_var['gp_hdnSearchShow'];
		
		return array('querystring' => $querystring, 'wheresql' => $wheresql);
	}
	
	//根据ID获取记录
	public function get_by_id($subscribeid){
		global $db;
		
		return $db->fetch_first("SELECT * FROM tbl_subscribe WHERE SUBSCRIBEID = '{$subscribeid}'");
	}
	
	//根据EMAIL获取记录
	public function get_by_email($email){
		global $db;
		
		return $db->fetch_first("SELECT * FROM tbl_subscribe WHERE EMAIL = '{$email}'");
	}
	
	//获取数量
	public function get_count($wheresql = ''){
		global $db;
		
		return $db->result_first("SELECT COUNT(1) FROM tbl_subscribe a WHERE 1 {$wheresql}") + 0;
		
	}
	
	//获取列表
	public function get_list($start , $perpage, $wheresql = '', $ordersql = ''){
		global $db, $setting;
		
		$rows = array();
		
		!$ordersql && $ordersql = "ORDER BY a.EDITTIME DESC";
		$perpage > 0 && $limitsql = "LIMIT $start, $perpage";
		
		$temp_query = $db->query("SELECT a.* FROM tbl_subscribe a WHERE 1 {$wheresql} {$ordersql} {$limitsql}");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	//添加
	public function insert($subscribe){
		global $db;
		
		$db->insert('tbl_subscribe', $subscribe);
	}
	
	//删除
	public function delete($id){
		global $db;
		
		$db->delete('tbl_subscribe', "SUBSCRIBEID = '{$id}'");
	}
	
	//模块扩展
	public function module($module){
		$module_data = array();
		$tmparr = explode('|', $module);
		
		if(count($tmparr) >= 3){
			$module_data['identity'] = $tmparr[0];
			$module_data['begintime'] = $tmparr[1];
			$module_data['endtime'] = $tmparr[2];
		}else{
			$module_data['identity'] = 'subscribe';
		}
		
		include_once view('/module/subscribe/view/module');
	}
	
}
?>