<?php
//版权所有(C) 2014 www.ilinei.com

namespace user\model;

/**
 * 用户分类
 * @author sigmazel
 * @since v1.0.2
 */
class _user_category{
	//添加
	public function insert($data){
		global $db;
		
		$db->insert('tbl_user_category', $data);
	}
	
	//修改
	public function update($id, $data){
		global $db;
	
		$db->update('tbl_user_category', $data, "USER_CATEGORYID = '{$id}'");
	}
	
	//删除
	public function delete($id){
		global $db;
		
		$db->delete('tbl_user_category', "USER_CATEGORYID = '{$id}'");
	}
	
}
?>