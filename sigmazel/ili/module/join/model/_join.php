<?php
//版权所有(C) 2014 www.ilinei.com

namespace join\model;

/**
 * 加盟
 * @author sigmazel
 * @since v1.0.2
 */
class _join{
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
			if($_var['gp_sltType'] == 0) $wheresql .= " AND CONCAT(a.TITLE, a.CONTENT, a.ADDRESS, a.USERNAME) LIKE '%{$_var[gp_txtKeyword]}%'";
			elseif ($_var['gp_sltType'] == 1) $wheresql .= " AND a.TITLE LIKE '%{$_var[gp_txtKeyword]}%'";
			elseif ($_var['gp_sltType'] == 2) $wheresql .= " AND a.CONTENT LIKE '%{$_var[gp_txtKeyword]}%'";
			elseif ($_var['gp_sltType'] == 3) $wheresql .= " AND a.ADDRESS LIKE '%{$_var[gp_txtKeyword]}%'";
			elseif ($_var['gp_sltType'] == 4) $wheresql .= " AND a.USERNAME LIKE '%{$_var[gp_txtKeyword]}%'";
		}
		
		if($_var['gp_sltStatus']) {
			$querystring .= '&sltStatus='.$_var['gp_sltStatus'];
			if($_var['gp_sltStatus'] == 1) $wheresql .= " AND a.STATUS = 0";
			elseif($_var['gp_sltStatus'] == 2) $wheresql .= " AND a.STATUS = 1";
			elseif($_var['gp_sltStatus'] == 3) $wheresql .= " AND a.STATUS = 2";
		}
		
		if($_var['gp_hdnSearchShow']) $querystring .= '&hdnSearchShow='.$_var['gp_hdnSearchShow'];
		
		return array('querystring' => $querystring, 'wheresql' => $wheresql);
	}
	
	//获取状态值
	public function get_status(){
		return array(
		0 => '待处理', 
		1 => '受理审批中', 
		2 => '已处理', 
		);
	}
	
	//根据ID获取记录
	public function get_by_id($joinid){
		global $db;
		
		return $db->fetch_first("SELECT * FROM tbl_join WHERE JOINID = '{$joinid}'");
	}
	
	//获取数量
	public function get_count($wheresql = ''){
		global $db;
		
		return $db->result_first("SELECT COUNT(1) FROM tbl_join a WHERE 1 {$wheresql}") + 0;
		
	}
	
	//获取列表
	public function get_list($start , $perpage, $wheresql = '', $ordersql = ''){
		global $db, $setting;
		
		$rows = array();
		
		!$ordersql && $ordersql = "ORDER BY a.EDITTIME DESC";
		$perpage > 0 && $limitsql = "LIMIT $start, $perpage";
		
		$temp_query = $db->query("SELECT a.* FROM tbl_join a WHERE 1 {$wheresql} {$ordersql} {$limitsql}");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	//添加
	public function insert($data){
		global $db;
		
		$db->insert('tbl_join', $data);
		
		return $db->insert_id();
	}
	
	//修改
	public function update($id, $data){
		global $db;
		
		$db->update('tbl_join', $data, "JOINID = '{$id}'");
	}
	
	//删除
	public function delete($id){
		global $db;
		
		$db->delete('tbl_join', "JOINID = '{$id}'");
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
			$module_data['identity'] = 'join';
		}
		
		include_once view('/module/join/view/module');
	}
}
?>