<?php
//版权所有(C) 2014 www.ilinei.com

namespace user\model;

/**
 * 积分
 * @author sigmazel
 * @since v1.0.2
 */
class _credit{
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
		
		if($_var['gp_txtMinCredit']) {
			$querystring .= '&txtMinCredit='.$_var['gp_txtMinCredit'];
			$wheresql .= " AND a.CREDIT >= '{$_var[gp_txtMinCredit]}'";
		}
		
		if($_var['gp_txtMaxCredit']) {
			$querystring .= '&txtMaxCredit='.$_var['gp_txtMaxCredit'];
			$wheresql .= " AND a.CREDIT <= '{$_var[gp_txtMaxCredit]}'";
		}
		
		if($_var['gp_txtMinScore']) {
			$querystring .= '&txtMinScore='.$_var['gp_txtMinScore'];
			$wheresql .= " AND a.SCORE >= '{$_var[gp_txtMinScore]}'";
		}
		
		if($_var['gp_txtMaxScore']) {
			$querystring .= '&txtMaxScore='.$_var['gp_txtMaxScore'];
			$wheresql .= " AND a.SCORE <= '{$_var[gp_txtMaxScore]}'";
		}
		
		if($_var['gp_txtKeyword']) {
			$_var['gp_txtKeyword'] = trim($_var['gp_txtKeyword']);
			$querystring .= '&txtKeyword='.$_var['gp_txtKeyword'];
			$_var['gp_sltType'] = $_var['gp_sltType'] + 0;
			
			if($_var['gp_sltType'] == 0) $wheresql .= " AND CONCAT(a.TITLE, a.REMARK, a.USERNAME, a.ABOUTTYPE) LIKE '%{$_var[gp_txtKeyword]}%'";
			elseif ($_var['gp_sltType'] == 1) $wheresql .= " AND a.TITLE LIKE '%{$_var[gp_txtKeyword]}%'";
			elseif ($_var['gp_sltType'] == 2) $wheresql .= " AND a.REMARK LIKE '%{$_var[gp_txtKeyword]}%'";
			elseif ($_var['gp_sltType'] == 3) $wheresql .= " AND a.USERNAME LIKE '%{$_var[gp_txtKeyword]}%'";
		}
		
		if($_var['gp_hdnSearchShow']) $querystring .= '&hdnSearchShow='.$_var['gp_hdnSearchShow'];
		
		return array('querystring' => $querystring, 'wheresql' => $wheresql);
	}
	
	//获取数量
	public function get_count($wheresql){
		global $db;
		
		return $db->result_first("SELECT COUNT(1) FROM tbl_credit a WHERE 1 {$wheresql}");
	}
	
	//获取列表
	public function get_list($start, $perpage, $wheresql = '', $ordersql = ''){
		global $db;
		
		!$ordersql && $ordersql = "ORDER BY a.CREATETIME DESC";
		$perpage > 0 && $limitsql = "LIMIT $start, $perpage";
		
		$rows = array();
		
		$temp_query = $db->query("SELECT a.* FROM tbl_credit a WHERE 1 {$wheresql} {$ordersql} {$limitsql}");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	//获取统计
	public function get_stat($wheresql){
		global $db;
		
		return $db->fetch_first("SELECT SUM(CREDIT) AS CREDIT, SUM(SCORE) AS SCORE FROM tbl_credit a WHERE 1 {$wheresql}");
	}
	
	//添加
	public function insert($data){
		global $db;
		
		$db->insert('tbl_credit', $data);
		
		return $db->insert_id();
	}
	
	//修改
	public function update($id, $data){
		global $db;
		
		$db->update('tbl_credit', $data, "CREDITID = '{$id}'");
	}
	
	//删除
	public function delete($credit){
		global $db;
		
		$db->query("DELETE FROM tbl_credit WHERE CREDITID = '{$credit[CREDITID]}'");
		$db->query("DELETE FROM tbl_credit_record WHERE ABOUTTYPE = 'credit' AND ABOUTID = '{$credit[CREDITID]}'");
		$db->query("UPDATE tbl_user SET CREDIT = CREDIT - '{$credit[CREDIT]}', SCORE = SCORE - '{$credit[SCORE]}' WHERE USERID = '{$credit[USERID]}'");
	}
	
	//刷新积分
	public function flash_by_action($userid, $action, $params = array()){
		global $db;
		
		$_user = new _user();
		$_invite = new _invite();
		
		//更新：第一个参数支持用户数组
		$user = !is_array($userid) && $userid ? $_user->get_by_id($userid) : $userid;
		if(!$user) return null;
		
		//如果当前的动作为邀请
		if($action == 'invite'){
			$invite_rnd = cookie_get('invite');
			
			//删除COOKIE
			cookie_set('invite', '1', time() + 1000);
			
			if(!$invite_rnd) return $user;
			
			if($invite_rnd) $invite_rnd = str_decrypt($invite_rnd);
			if(!$invite_rnd) return $user;
			
			$temparr = explode('|', $invite_rnd);
			if(count($temparr) != 2 || $user['USERID'] == $temparr[0] + 0) return $user;
			
			//1：添加邀请记录
			$inviteid = $_invite->insert(array(
			'SRCID' => $temparr[0],
			'SRCTYPE' => $temparr[1],
			'USERID' => $user['USERID'],
			'USERNAME' => $user['WX_FANSID'] ? $user['REALNAME'] : $user['USERNAME'],
			'EDITTIME' => date('Y-m-d H:i:s'),
			'SRCDATA' => $temparr[0]
			));
			
			//2：更新用户的INVITEID
			$db->update('tbl_user', array('INVITEID' => $inviteid), "USERID = '{$user[USERID]}'");
			
			$inviter = $_user->get_by_id($temparr[0]);
			if(!$inviter) return $user;
			
			$invite = $_invite->get_by_userid($user['USERID']);
			if($invite){
				$_invite->friend($inviter, $user);
				return $user;
			}
			
			//3：互相添加好友
			$_invite->friend($inviter, $user);
			
			//4：邀请人刷新积分
			$this->flash_by_action_in($inviter, 'invite', array('ABOUTID' => $user['USERID'], 'ABOUTTYPE' => 'share'));
			
			return $user;
		}
		
		return $this->flash_by_action_in($user, $action, $params);
	}
	
	//执行刷新积分
	private function flash_by_action_in($user, $action, $params = array()){
		global $db;
		
		$_user = new _user();
		$_credit_rule = new _credit_rule();
		$_credit_record = new _credit_record();
		
		$credit_rule = $_credit_rule->get_by_action($action);
		
		if(!$credit_rule || !$credit_rule['NUM'] || $credit_rule['ENABLED'] + 0 == 0) return $user;
		if(!($credit_rule['SCORE'] || $credit_rule['CREDIT'])) return $user;
		
		$nowdate = date('Y-m-d');
		$nowmonth = date('Y-m');
		
		$wheresql = " AND USERID = '{$user[USERID]}' AND ACTION = '{$action}'";
		$appendsql = '';
		
		if(isset($params['ABOUTID'])) $appendsql .= " AND ABOUTID = '{$params[ABOUTID]}'";
		if(isset($params['ABOUTTYPE'])) $appendsql .= " AND ABOUTTYPE = '{$params[ABOUTTYPE]}'";
		
		switch($credit_rule['TYPE']){
			case 1://一次
				$credit = $db->fetch_first("SELECT * FROM tbl_credit_record WHERE 1 {$wheresql} {$appendsql}");
				if(!$credit) $user = $_credit_record->insert_by_rule($user, $credit_rule, $params);
				
				break;
			case 2://每天
				$credit_count = $db->result_first("SELECT COUNT(1) FROM tbl_credit_record WHERE DATE_FORMAT(EDITTIME, '%Y-%m-%d') = '{$nowdate}' {$wheresql}") + 0;
				if($credit_count < $credit_rule['NUM']){
					$credit_rule['NUM'] = 1;
					$user = $_credit_record->insert_by_rule($user, $credit_rule, $params);
				}
				
				break;
			case 3://每月
				$credit_count = $db->result_first("SELECT COUNT(1) FROM tbl_credit_record WHERE DATE_FORMAT(EDITTIME, '%Y-%m') = '{$nowmonth}' {$wheresql}") + 0;
				if($credit_count < $credit_rule['NUM']){
					$credit_rule['NUM'] = 1;
					$user = $_credit_record->insert_by_rule($user, $credit_rule, $params);
				}
				
				break;
			case 4://不限
				$user = $_credit_record->insert_by_rule($user, $credit_rule, $params);
				break;
		}
		
		if(!$user || !is_array($user['CREDIT_RECORD'])) return $user;
		
		$_user->update($user['USERID'], array('SCORE' => $user['SCORE'], 'CREDIT' => $user['CREDIT']));
		$_user->flash_group($user);
		
		return $user;
	}
}
?>