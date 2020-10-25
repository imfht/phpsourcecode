<?php
/*---------------------------------------------------------------------------
 小微OA系统 - 让工作更轻松快乐

 Copyright (c) 2013 http://www.smeoa.com All rights reserved.

 Author:  jinzhu.yin<smeoa@qq.com>

 Support: https://git.oschina.net/smeoa/xiaowei
 -------------------------------------------------------------------------*/

// 角色模型
namespace Home\Model;
use Think\Model;

class  GroupModel extends CommonModel {
	public $_validate = array( array('name', 'require', '名称必须'), );

	function get_group_list($user_id) {
		$table = $this -> tablePrefix . 'group_user';
		$rs = $this -> db -> query('select a.group_id from ' . $table . ' as a where a.user_id=' . $user_id . ' ');
		return $rs;
	}

	function get_user_list($group_id) {
		$where['group_id']=array('eq',$group_id);
		$rs=M("GroupUser")->where($where)->getField('user_id',true);		
		return $rs;
	}
	
	function del_user($group_id,$user_list){
		if (empty($user_list)) {
			return true;
		}
		if (is_array($user_list)) {
			$user_list = array_filter($user_list);
		} else {
			$user_list = explode(",", $user_list);
			$user_list = array_filter($user_list);
		}
		$user_list = implode(",", $user_list);

		$table = $this -> tablePrefix . 'group_user';
			
		$sql='delete from ' . $table . ' where user_id in (' . $user_list . ') and group_id=\''.$group_id.'\'';
		
		$result = $this -> db -> execute($sql);
		
		if ($result === false) {
			return false;
		} else {
			return true;
		}
	}

	function save_user($user_list, $group_list) {

		if (empty($user_list)) {
			return true;
		}
		if (empty($group_list)) {
			return true;
		}
		if (is_array($user_list)) {
			$user_list = array_filter($user_list);
		} else {
			$user_list = explode(",", $user_list);
			$user_list = array_filter($user_list);
		}
		$user_list = implode(",", $user_list);

		if (is_array($group_list)) {
			$group_list = array_filter($group_list);
		} else {
			$group_list = explode(",", $group_list);
			$group_list = array_filter($group_list);
		}
		$group_list = implode(",", $group_list);

		$where = 'a.id in (' . $user_list . ') AND b.id in(' . $group_list . ')';
		$sql = 'INSERT INTO ' . $this -> tablePrefix . 'group_user (user_id,group_id) ';
		$sql .= ' SELECT a.id, b.id FROM ' . $this -> tablePrefix . 'user a, ' . $this -> tablePrefix . 'group b WHERE ' . $where;
		$result = $this -> execute($sql);
		if ($result === false) {
			return false;
		} else {
			return true;
		}
	}
}
?>