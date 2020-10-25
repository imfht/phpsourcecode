<?php
//版权所有(C) 2014 www.ilinei.com

namespace user\model;

/**
 * 邀请
 * @author sigmazel
 * @since v1.0.2
 */
class _invite{
	public function get_by_userid($userid){
		global $db;
		
		return $db->fetch_first("SELECT * FROM tbl_invite WHERE USERID = '{$userid}'");
	}
	
	public function get_by_openid($openid){
		global $db;
		
		return $db->fetch_first("SELECT * FROM tbl_invite WHERE SRCDATA = '{$openid}'");
	}
	
	public function get_count($wheresql){
		global $db;
		
		return $db->result_first("SELECT COUNT(1) FROM tbl_invite a, tbl_user b WHERE a.USERID = b.USERID {$wheresql}") + 0;
	}
	
	public function get_list($start, $perpage, $wheresql){
		global $db;
		
		$perpage > 0 && $limitsql = "LIMIT $start, $perpage";
		
		$rows = array();
		
		$temp_query = $db->query("SELECT b.*, a.SRCID, a.SRCTYPE, a.EDITTIME, a.SRCDATA FROM tbl_invite a, tbl_user b WHERE a.USERID = b.USERID {$wheresql} ORDER BY a.EDITTIME DESC {$limitsql}");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row['MOBILE'] = format_mobile_privacy($row['MOBILE']);
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	public function insert($data){
		global $db;
		
		$db->insert('tbl_invite', $data);
		
		return $db->insert_id();
	}
	
	public function friend($user1, $user2){
		global $db;
		
		if(!$user1 || !$user2) return null;
		
		$db->delete('tbl_invite', "SRCID = '{$user1[USERID]}' AND SRCTYPE = 'friend' AND USERID = '{$user2[USERID]}'");
		$db->delete('tbl_invite', "SRCID = '{$user2[USERID]}' AND SRCTYPE = 'friend' AND USERID = '{$user1[USERID]}'");
		
		$db->insert('tbl_invite', array(
		'SRCID' => $user1['USERID'], 
		'SRCTYPE' => 'friend', 
		'USERID' => $user2['USERID'], 
		'USERNAME' => $user2['WX_FANSID'] ? $user2['REALNAME'] : $user2['USERNAME'], 
		'EDITTIME' => date('Y-m-d H:i:s')
		));
		
		$db->insert('tbl_invite', array(
		'SRCID' => $user2['USERID'], 
		'SRCTYPE' => 'friend', 
		'USERID' => $user1['USERID'], 
		'USERNAME' => $user1['WX_FANSID'] ? $user1['REALNAME'] : $user1['USERNAME'], 
		'EDITTIME' => date('Y-m-d H:i:s')
		));
		
		return true;
	}

}
?>