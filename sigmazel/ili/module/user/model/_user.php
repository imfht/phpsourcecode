<?php
//版权所有(C) 2014 www.ilinei.com

namespace user\model;

/**
 * 用户
 * @author sigmazel
 * @since v1.0.2
 */
class _user{
	//搜索
	public function search(){
		global $_var;
		
		$querystring = '';
		$wheresql = ' ';
		$ordersql = 'ORDER BY a.CREATETIME DESC';
		
		if($_var['gp_txtBeginDate']){
			$querystring .= '&txtBeginDate='.$_var['gp_txtBeginDate'];
			$wheresql .= " AND a.CREATETIME >= '{$_var[gp_txtBeginDate]}'";
		}
		
		if($_var['gp_txtEndDate']){
			$querystring .= '&txtEndDate='.$_var['gp_txtEndDate'];
			$wheresql .= " AND a.CREATETIME <= '{$_var[gp_txtEndDate]}'";
		}
		
		if($_var['gp_txtMinCredit']){
			$querystring .= '&txtMinCredit='.$_var['gp_txtMinCredit'];
			$wheresql .= " AND a.CREDIT >= '{$_var[gp_txtMinCredit]}'";
		}
		
		if($_var['gp_txtMaxCredit']){
			$querystring .= '&txtMaxCredit='.$_var['gp_txtMaxCredit'];
			$wheresql .= " AND a.CREDIT <= '{$_var[gp_txtMaxCredit]}'";
		}
		
		if($_var['gp_txtMinScore']){
			$querystring .= '&txtMinScore='.$_var['gp_txtMinScore'];
			$wheresql .= " AND a.SCORE >= '{$_var[gp_txtMinScore]}'";
		}
		
		if($_var['gp_txtMaxScore']){
			$querystring .= '&txtMaxScore='.$_var['gp_txtMaxScore'];
			$wheresql .= " AND a.SCORE <= '{$_var[gp_txtMaxScore]}'";
		}
		
		if($_var['gp_txtKeyword']){
			$_var['gp_txtKeyword'] = trim($_var['gp_txtKeyword']);
			$querystring .= '&txtKeyword='.$_var['gp_txtKeyword'];
			$_var['gp_sltType'] = $_var['gp_sltType'] + 0;
			
			if($_var['gp_sltType'] == 0) $wheresql .= " AND CONCAT(a.USERID, a.USERNAME, a.REALNAME, a.EMAIL, a.MOBILE, a.PHONE, a.COMMENT) LIKE '%{$_var[gp_txtKeyword]}%'";
			elseif($_var['gp_sltType'] == 1) $wheresql .= " AND a.USERID = '{$_var[gp_txtKeyword]}'";
			elseif($_var['gp_sltType'] == 2) $wheresql .= " AND a.USERNAME LIKE '%{$_var[gp_txtKeyword]}%'";
			elseif($_var['gp_sltType'] == 3) $wheresql .= " AND a.REALNAME LIKE '%{$_var[gp_txtKeyword]}%'";
			elseif($_var['gp_sltType'] == 4) $wheresql .= " AND a.EMAIL LIKE '%{$_var[gp_txtKeyword]}%'";
			elseif($_var['gp_sltType'] == 5) $wheresql .= " AND a.MOBILE LIKE '%{$_var[gp_txtKeyword]}%'";
			elseif($_var['gp_sltType'] == 6) $wheresql .= " AND a.PHONE LIKE '%{$_var[gp_txtKeyword]}%'";
			elseif($_var['gp_sltType'] == 7) $wheresql .= " AND a.COMMENT LIKE '%{$_var[gp_txtKeyword]}%'";
			
			if($_var['gp_sltType'] > 0) $querystring .= '&sltType='.$_var['gp_sltType'];
		}
		
		if($_var['gp_sltGroupID']){
			$querystring .= '&sltGroupID='.$_var['gp_sltGroupID'];
			$wheresql .= " AND a.GROUPID = '{$_var[gp_sltGroupID]}'";
		}
		
		if($_var['gp_sltIsAudit']){
			$querystring .= '&sltIsAudit='.$_var['gp_sltIsAudit'];
			
			if($_var['gp_sltIsAudit'] == 1) $wheresql .= " AND a.ISAUDIT = 1";
			elseif($_var['gp_sltIsAudit'] == 2) $wheresql .= " AND a.ISAUDIT = 0";
		}
		
		if($_var['gp_sltSort']){
			$querystring .= '&sltSort='.$_var['gp_sltSort'];
			
			if($_var['gp_sltSort'] == 1) $ordersql = "ORDER BY a.CREATETIME DESC";
			elseif($_var['gp_sltSort'] == 2) $ordersql = "ORDER BY a.LOGINTIME DESC";
			elseif($_var['gp_sltSort'] == 3) $ordersql = "ORDER BY a.CREDIT DESC";
			elseif($_var['gp_sltSort'] == 4) $ordersql = "ORDER BY a.SCORE DESC";
		}
		
		if($_var['gp_hdnSearchShow']) $querystring .= '&hdnSearchShow='.$_var['gp_hdnSearchShow'];
		
		return array('querystring' => $querystring, 'wheresql' => $wheresql, 'ordersql' => $ordersql);
	}
	
	//根据ID获取记录
	public function get_by_id($id){
		global $db;
		
		$user = $db->fetch_first("SELECT * FROM tbl_user WHERE USERID = '{$id}'");
		
		if($user){
			$user['NAME'] = $user['USERNAME'];
			
			if($user['REALNAME']) $user['NAME'] = $user['REALNAME'];
			elseif($user['MOBILE']) $user['NAME'] = $user['MOBILE'];
			elseif($user['EMAIL']) $user['NAME'] = $user['EMAIL'];
			
			$user['CREDIT'] = format_price($user['CREDIT']);
		}
		
		return $user;
	}
	
	//根据手机号码获取记录
	public function get_by_mobile($mobile){
		global $db;
		
		$user = $db->fetch_first("SELECT * FROM tbl_user WHERE MOBILE = '{$mobile}'");
		if($user){
			$user['NAME'] = $user['USERNAME'];
			
			if($user['REALNAME']) $user['NAME'] = $user['REALNAME'];
			elseif($user['MOBILE']) $user['NAME'] = $user['MOBILE'];
			elseif($user['EMAIL']) $user['NAME'] = $user['EMAIL'];
			
			$user['CREDIT'] = format_price($user['CREDIT']);
		}
		
		return $user;
	}
	
	//根据EMAIL获取记录
	public function get_by_email($email){
		global $db;
		
		$user = $db->fetch_first("SELECT * FROM tbl_user WHERE EMAIL = '{$email}'");
		if($user){
			$user['NAME'] = $user['USERNAME'];
			
			if($user['REALNAME']) $user['NAME'] = $user['REALNAME'];
			elseif($user['MOBILE']) $user['NAME'] = $user['MOBILE'];
			elseif($user['EMAIL']) $user['NAME'] = $user['EMAIL'];
			
			$user['CREDIT'] = format_price($user['CREDIT']);
		}
		
		return $user;
	}
	
	//根据认证获取记录
	public function get_by_auth($auth){
		global $db, $setting;
		
		$auth = str_decrypt($auth);
		$autharray = explode('|', $auth);
		
		if(count($autharray) != 3) return null;
		
		$autharray[0] = str_replace("'", '', $autharray[0]);
		$autharray[1] = str_replace("'", '', $autharray[1]);
		$autharray[2] = str_replace("'", '', $autharray[2]);
		
		if($autharray[0] == -1){
			if($autharray[1] == $setting['AdminPassword']) $user = array('USERID' => -1, 'USERNAME' => 'administrator', 'PASSWD' => $setting['AdminPassword'], 'REALNAME' => '超级系统管理', 'LOGINTIME' => date('Y-m-d H:i:s'));
		}else{
			$user = $db->fetch_first("SELECT a.* FROM tbl_user a WHERE a.USERID = '{$autharray[0]}' AND a.PASSWD = '{$autharray[1]}'");
			
			if($user){
				if($user){
					$user['NAME'] = $user['USERNAME'];
					
					if($user['REALNAME']) $user['NAME'] = $user['REALNAME'];
					elseif($user['MOBILE']) $user['NAME'] = $user['MOBILE'];
					elseif($user['EMAIL']) $user['NAME'] = $user['EMAIL'];
					
					$user['CREDIT'] = format_price($user['CREDIT']);
				}
				
				if(hexdec($autharray[2]) != hexdec($user['SALT'])) return null;
				
				$this->flash_state($user['USERID']);
			}
		}
		
		return $user;
	}
	
	//根据随机数获取记录
	public function get_by_salt($userid, $salt){
		global $db;
		
		$user = $db->fetch_first("SELECT * FROM tbl_user WHERE USERID = '{$userid}' AND SALT = '{$salt}'");
		if($user){
			$user['NAME'] = $user['USERNAME'];
			
			if($user['REALNAME']) $user['NAME'] = $user['REALNAME'];
			elseif($user['MOBILE']) $user['NAME'] = $user['MOBILE'];
			elseif($user['EMAIL']) $user['NAME'] = $user['EMAIL'];
			
			$user['CREDIT'] = format_price($user['CREDIT']);
		}
		
		return $user;
	}
	
	//根据账号获取记录
	public function get_by_name($username){
		global  $db;
		
		$user = $db->fetch_first("SELECT * FROM tbl_user WHERE USERNAME = '{$username}'");
		if($user){
			$user['NAME'] = $user['USERNAME'];
			
			if($user['REALNAME']) $user['NAME'] = $user['REALNAME'];
			elseif($user['MOBILE']) $user['NAME'] = $user['MOBILE'];
			elseif($user['EMAIL']) $user['NAME'] = $user['EMAIL'];
			
			$user['CREDIT'] = format_price($user['CREDIT']);
		}
		
		return $user;
	}
	
	//根据粉丝ID获取记录
	public function get_by_fansid($fansid){
		global $db;
		
		$user = $db->fetch_first("SELECT * FROM tbl_user WHERE WX_FANSID = '{$fansid}'");
		if($user){
			$user['NAME'] = $user['USERNAME'];
			
			if($user['REALNAME']) $user['NAME'] = $user['REALNAME'];
			elseif($user['MOBILE']) $user['NAME'] = $user['MOBILE'];
			elseif($user['EMAIL']) $user['NAME'] = $user['EMAIL'];
			
			$user['CREDIT'] = format_price($user['CREDIT']);
		}
		
		return $user;
	}
	
	//获取数量
	public function get_count($wheresql = ''){
		global $db;
		
		return $db->result_first("SELECT COUNT(1) FROM tbl_user a WHERE a.ISMANAGER = 0 {$wheresql}") + 0;
	}
	
	//获取数量+等级
	public function get_count_of_group($wheresql = ''){
		global $db;
		
		return $db->result_first("SELECT COUNT(1) FROM tbl_user a LEFT JOIN tbl_group b ON a.GROUPID = b.GROUPID WHERE a.ISMANAGER = 0 {$wheresql}") + 0;
	}
	
	//获取列表
	public function get_list($start, $perpage, $wheresql = '', $ordersql = ''){
		global $db;
		
		!$ordersql && $ordersql = "ORDER BY a.CREATETIME DESC";
		$perpage > 0 && $limitsql = "LIMIT $start, $perpage";
		
		$rows = array();
	
		$temp_query = $db->query("SELECT a.* FROM tbl_user a WHERE a.ISMANAGER = 0 {$wheresql} {$ordersql} {$limitsql}");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row['NAME'] = $row['USERNAME'];
			
			if($row['REALNAME']) $row['NAME'] = $row['REALNAME'];
			elseif($row['MOBILE']) $row['NAME'] = $row['MOBILE'];
			elseif($row['EMAIL']) $row['NAME'] = $row['EMAIL'];
			
			$row['CREDIT'] = format_price($row['CREDIT']);
			
			$rows[] = $row;
		}
	
		return $rows;
	}
	
	//获取列表+等级
	public function get_list_of_group($start, $perpage, $wheresql = '', $ordersql = ''){
		global $db;
		
		!$ordersql && $ordersql = "ORDER BY a.CREATETIME DESC";
		$perpage > 0 && $limitsql = "LIMIT $start, $perpage";
		
		$rows = array();
	
		$temp_query = $db->query("SELECT a.*, b.CNAME FROM tbl_user a LEFT JOIN tbl_group b ON a.GROUPID = b.GROUPID WHERE a.ISMANAGER = 0 {$wheresql} {$ordersql} {$limitsql}");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$rows[] = $row;
		}
	
		return $rows;
	}
	
	//获取重设密码邮件
	public function get_lost_password_email($user, $ismobile = 0){
		global $db, $setting, $_var;
		
		$salt = random(10);
		$db->update('tbl_user', array('SALT' => $salt), "USERID = '{$user[USERID]}'");
		
		$rnd = str_encrypt($user['USERID'].'|'.$salt);
		
		$email_subject = "重设{$user[USERNAME]}({$user[EMAIL]})在{$setting[SiteName]}的密码?";
		
		$resetpwd_url = $ismobile ? "{$setting[SiteHost]}mobile.do?ac=member&op=lostpwd&do=resetpwd&rnd={$rnd}&ref={$_var[gp_ref]}" : "{$setting[SiteHost]}member.do?op=lostpwd&do=resetpwd&rnd={$rnd}&ref={$_var[gp_ref]}";
		
		$emailHTML = "
		亲爱的{$user[USERNAME]}({$user[EMAIL]})：\r\n<br/>
		您的密码重设要求已经得到验证。请点击以下链接输入您新的密码：\r\n<br/>
		(pleae click on the following link to reset your password:)\r\n<br/>
		<a href=\"{$resetpwd_url}\" target=\"_blank\">{$resetpwd_url}</a>\r\n<br/>
		如果您的email程序不支持链接点击，请将上面的地址拷贝至您的浏览器(例如IE)的地址栏进入{$setting[SiteName]}。\r\n<br/>
		感谢对{$setting[SiteName]}的支持。\r\n<br/>{$setting[SiteName]} {$setting[SiteHost]}/\r\n<br/>
		这是一封自动产生的email，请勿回复.";
		
		return array('SUBJECT' => $email_subject, 'BODY' => $emailHTML);
	}
	
	//添加
	public function insert($user){
		global $db;
		
		$db->insert('tbl_user', $user);
		
		return $db->insert_id();
	}

	//修改
	public function update($id, $data){
		global  $db;
		
		$db->update('tbl_user', $data, "USERID = '{$id}'");
	}
	
	//删除
	public function delete($user){
		global $db;
		
		$db->delete('tbl_user', "USERID = '{$user[USERID]}'");
		$db->delete('tbl_user_category', "USERID = '{$user[USERID]}'");
		$db->delete('tbl_wx_fans', "WX_FANSID = '{$user[WX_FANSID]}'");
		$db->delete('tbl_third', "USERID = '{$user[USERID]}'");
		$db->delete('tbl_invite', "INVITEID = '{$user[INVITEID]}'");
		$db->delete('tbl_invite', "SRCID = '{$user[USERID]}' AND SRCTYPE = 'share'");
	}
	
	//登录
	public function login($username, $password){
		global  $db;
		
		$user = $db->fetch_first("SELECT * FROM tbl_user WHERE USERNAME = '{$username}'");
		if($user && md5($password) == $user['PASSWD']){
			$user['CREDIT'] = format_price($user['CREDIT']);
			
			$_SESSION['_current'] = serialize($user);
			$db->update('tbl_user', array('LOGINTIME' => date('Y-m-d H:i:s')), "USERID = '{$user[USERID]}'");
			return $user;
		}else return  null;
	}
	
	//注册
	public function register($user){
		global  $db;
		
		$db->insert('tbl_user', $user);
		$user =  $db->fetch_first("SELECT * FROM tbl_user WHERE USERNAME = '{$user[USERNAME]}'");
		$user['CREDIT'] = format_price($user['CREDIT']);
		
		$_SESSION['_current'] = serialize($user);
		$db->update('tbl_user', array('LOGINTIME' => date('Y-m-d H:i:s')), " USERID = '{$user[USERID]}'");
	}
	
	//重置密码
	public function reset_password($user, $password){
		global $db;
		
		$db->update('tbl_user', array('PASSWD' => md5($password)), "USERID = '{$user[USERID]}'");
	}
	
	//刷新等级
	public function flash_group($user){
		global $db;
		
		$score = $user['SCORE'];
		$group = $db->fetch_first("SELECT * FROM tbl_group WHERE SCORELOW <= '{$score}' AND SCOREHIGH >= '{$score}'");
		
		if($group) $db->update('tbl_user', array('GROUPID' => $group['GROUPID']), " USERID = '{$user[USERID]}'");
	}
	
	//刷新状态
	public function flash_state($userid, $salt = ''){
		global  $db;
		
		if($salt) $db->update('tbl_user', array('LOGINTIME' => date('Y-m-d H:i:s'), 'SALT' => $salt), " USERID = '{$userid}'");
		else $db->update('tbl_user', array('LOGINTIME' => date('Y-m-d H:i:s')), " USERID = '{$userid}'");
	}
	
	//注销状态
	public function unset_state(){
		global $_var;
		
		$_SESSION['_wx_fans'] = null;
		$_SESSION['_current'] = null;
		
		unset($_var['current']);
		
		cookie_set('auth_member', '', time() - 3600);
		
		return ;
	}
	
	//格式化金额
	public function format_credit($credit){
		if(substring($credit, 0, -3) == '.00') return $credit + 0;
		elseif(substring($credit, 0, -2) == '.0') return $credit + 0;
		else return $credit;
	}
	
	//格式化头像 
	public function format_photo($user){
		if($user['PHOTO']){
			$tempsrc = $user['PHOTO'];
			$user['PHOTO'] = explode('|', $user['PHOTO']);
			$user['PHOTO'][4] = $tempsrc;
			$user['PHOTO'][3] = format_file_path($user['PHOTO'][0]);
			$user['PHOTO'][0] = format_file_path($user['PHOTO'][0], $user['PHOTO'][2]);
		}
		
		return $user;
	}
	
}
?>