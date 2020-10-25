<?php
//版权所有(C) 2014 www.ilinei.com

namespace user\model;

use ilinei\httpclient;

/**
 * 短信
 * @author sigmazel
 * @since v1.0.2
 */
class _sms{
	//搜索
	public function search(){
		global $_var;
		
		$querystring = '';
		$wheresql = ' ';
	
		if($_var['gp_txtBeginDate']) {
			$querystring .= '&txtBeginDate='.$_var['gp_txtBeginDate'];
			$wheresql .= " AND a.CREATETIME >= '{$_var[gp_txtBeginDate]}'";
		}
		
		if($_var['gp_txtEndDate']) {
			$querystring .= '&txtEndDate='.$_var['gp_txtEndDate'];
			$wheresql .= " AND a.CREATETIME <= '{$_var[gp_txtEndDate]}'";
		}
		
		if($_var['gp_txtKeyword']) {
			$_var['gp_txtKeyword'] = trim($_var['gp_txtKeyword']);
			$querystring .= '&txtKeyword='.$_var['gp_txtKeyword'];
			$_var['gp_sltType'] = $_var['gp_sltType'] + 0;
			
			if($_var['gp_sltType'] == 0) $wheresql .= " AND CONCAT(a.MOBILE, a.MESSAGE) LIKE '%{$_var[gp_txtKeyword]}%'";
			elseif ($_var['gp_sltType'] == 1) $wheresql .= " AND a.MOBILE LIKE '%{$_var[gp_txtKeyword]}%'";
			elseif ($_var['gp_sltType'] == 2) $wheresql .= " AND a.MESSAGE LIKE '%{$_var[gp_txtKeyword]}%'";
		}
		
		if($_var['gp_hdnSearchShow']) $querystring .= '&hdnSearchShow='.$_var['gp_hdnSearchShow'];
		
		return array('querystring' => $querystring, 'wheresql' => $wheresql);
	}
	
	//根据ID获取记录
	public function get_by_id($id){
		global $db;
		
		return $db->fetch_first("SELECT * FROM tbl_sms WHERE SMSID = '{$id}'");
	}
	
	//获取数量
	public function get_count($wheresql = ''){
		global $db;
	
		return $db->result_first("SELECT COUNT(1) FROM tbl_sms a WHERE 1 {$wheresql}") + 0;
	}
	
	//获取列表
	public function get_list($start, $perpage, $wheresql = '', $ordersql = ''){
		global $db;
		
		!$ordersql && $ordersql = "ORDER BY a.CREATETIME DESC";
		$perpage > 0 && $limitsql = "LIMIT $start, $perpage";
		
		$rows = array();
	
		$temp_query = $db->query("SELECT a.* FROM tbl_sms a WHERE 1 {$wheresql} {$ordersql} {$limitsql}");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	//添加
	public function insert($data){
		global $db;
	
		$db->insert('tbl_sms', $data);
	}
	
	//修改
	public function update($id, $data){
		global $db;
		
		$db->update('tbl_sms', $data, "SMSID = '{$id}'");
	}
	
	//删除
	public function delete($id){
		global $db;
		
		$db->delete('tbl_sms', "SMSID = '{$id}'");
	}
	
	//检查短信
	public function check($mobile, $date){
		global $db;
		
		return $db->fetch_first("SELECT a.* FROM tbl_sms a WHERE a.MOBILE = '{$mobile}' AND DATE(a.CREATETIME) = '{$date}'");
	}
	
	//发送
	public function send($mobile, $message){
		global $setting;
		
		if(!is_mobile($mobile) || !$setting['ThirdSms'] || !$setting['SmsKey']) return 100;
		
		$message = $setting['SmsSuffix'].$message;
		
		$params['apikey'] = $setting['SmsKey'];
		$params['mobile'] = $mobile;
		$params['text'] = $message;
		
		$httpClient = new httpclient();
		
		$rtn = $httpClient->post("https://sms.yunpian.com/v2/sms/single_send.json", $params);
		$rtn = json_decode($rtn, 1);
		
		if($rtn['code'] != 0) log_debug('sms:'.$rtn['msg'].',message:'.$message);
		
		return $rtn['code'];
	}
	
}
?>