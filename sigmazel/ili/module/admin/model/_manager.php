<?php
//版权所有(C) 2014 www.ilinei.com

namespace admin\model;

/**
 * 管理员
 * @author sigmazel
 * @since v1.0.2
 */
class _manager{
	//根据ID获取记录
	public function get_by_id($id){
		global $db;
		
		return $db->fetch_first("SELECT m.*, r.CNAME AS ROLENAME FROM tbl_user m LEFT JOIN tbl_role r ON m.ROLEID = r.ROLEID WHERE m.USERID = '{$id}'");
	}
	
	//根据用户名获取记录
	public function get_by_name($username){
		global  $db;
		
		return $db->fetch_first("SELECT m.*, r.CNAME AS ROLENAME FROM tbl_user m LEFT JOIN tbl_role r ON m.ROLEID = r.ROLEID WHERE m.USERNAME = '{$username}'");
	}
	
	//获取数量 
	public function get_count($wheresql = ''){
		global $db;
		
		return $db->result_first("SELECT COUNT(1) FROM tbl_user m LEFT JOIN tbl_role r ON m.ROLEID = r.ROLEID WHERE m.ISMANAGER = 1 {$wheresql}") + 0;
	}
	
	//获取列表
	public function get_list($start, $perpage, $wheresql = '', $ordersql = ''){
		global $db;
		
		!$ordersql && $ordersql = "ORDER BY m.CREATETIME DESC";
		$perpage > 0 && $limitsql = "LIMIT $start, $perpage";
		
		$rows = array();
	
		$temp_query = $db->query("SELECT m.*, r.CNAME AS ROLENAME FROM tbl_user m LEFT JOIN tbl_role r ON m.ROLEID = r.ROLEID WHERE m.ISMANAGER = 1 {$wheresql} {$ordersql} {$limitsql}");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$rows[] = $row;
		}
	
		return $rows;
	}
	
	//获取分类列表
	public function get_category($id, $type = '', $wheresql = ''){
		global $db;
		
		$rows = array();
		
		if($type) $wheresql .= " AND a.TYPE = '{$type}'";
		
		$temp_query = $db->query("SELECT a.* FROM tbl_category a, tbl_user_category b WHERE a.CATEGORYID = b.CATEGORYID AND b.USERID = '{$id}' {$wheresql} ORDER BY a.PARENTID ASC, a.DISPLAYORDER ASC");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row['URL'] = str_replace('{ID}', $row['CATEGORYID'], $row['URL']);
			$row['URL'] = str_replace('{NO}', $row['IDENTITY'], $row['URL']);
			
			$row = format_row_files($row);
			
			$rows[$row['CATEGORYID']] = $row;
		}
		
		return $rows;
	}
	
	//添加
	public function insert($data){
		global $db;
		
		$db->insert('tbl_user', $data);
		
		return $db->insert_id();
	}
	
	//添加分类
	public function insert_category($id, $categories){
		global $db;
		
		$db->delete('tbl_user_category', "USERID = '{$id}'");
		
		$tmparr = array();
		foreach($categories as $key => $val){
			if($val + 0 > 0 && !in_array($val, $tmparr)) $tmparr[] = $val;
		}
		
		foreach($tmparr as $key => $val){
			$db->insert('tbl_user_category', array('USERID' => $id, 'CATEGORYID' => $val));
		}
	}
	
	//修改
	public function update($id, $data){
		global  $db;
		
		$db->update('tbl_user', $data, "USERID = '{$id}'");
	}
	
	//删除
	public function delete($manager){
		global $db;
		
		$db->delete('tbl_user', "USERID = '{$manager[USERID]}'");
		$db->delete('tbl_user_category', "USERID = '{$manager[USERID]}'");
		$db->delete('tbl_wx_fans', "WX_FANSID = '{$manager[WX_FANSID]}'");
		$db->delete('tbl_third', "USERID = '{$manager[USERID]}'");
		$db->delete('tbl_invite', "INVITEID = '{$manager[INVITEID]}'");
		$db->delete('tbl_invite', "SRCID = '{$manager[USERID]}' AND SRCTYPE = 'share'");
	}
	
	//刷新状态
	public function flash_state($id, $salt = ''){
		global  $db;
		
		if($salt) $db->update('tbl_user', array('LOGINTIME' => date('Y-m-d H:i:s'), 'SALT' => $salt), " USERID = '{$id}'");
		else $db->update('tbl_user', array('LOGINTIME' => date('Y-m-d H:i:s')), " USERID = '{$id}'");
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
	
}
?>