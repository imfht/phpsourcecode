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

class  RoleModel extends CommonModel {
	public $_validate = array( array('name', 'require', '名称必须'), );

	function get_node_list($role_id) {
		$rs = $this -> db -> query('select * from ' . $this -> tablePrefix . 'role_node as a  where a.role_id=' . $role_id . ' ');
		return $rs;
	}

	function del_node($role_id, $node_list) {
		if (empty($node_list)) {
			return true;
		}
		if (is_array($node_list)) {
			$node_list = array_filter($node_list);
		} else {
			$node_list = explode(",", $node_list);
			$node_list = array_filter($node_list);
		}
		$node_list = implode(",", $node_list);
		$table = $this -> tablePrefix . 'role_node';
		//dump('delete from '.$table.' where role_id='.$role_id.' and node_id in ('.$node_list.')');

		$result = $this -> db -> execute('delete from ' . $table . ' where role_id=' . $role_id . ' and node_id in (' . $node_list . ')');

		if ($result === false) {
			return false;
		} else {
			return true;
		}
	}

	function set_node($role_id, $node_list) {
		if (empty($node_list)) {
			return true;
		}
		if (is_array($node_list)) {
			$node_list = array_filter($node_list);
		} else {
			$node_list = explode(",", $node_list);
			$node_list = array_filter($node_list);
		}

		foreach ($node_list as $node) {
			$result = $this -> db -> execute('INSERT INTO ' . $this -> tablePrefix . 'role_node (role_id,node_id) values(' . $role_id . ',' . $node . ')');
			if ($result === false) {
				return false;
			}
		}
		return true;
	}

	function get_role_list($user_id) {
		$table = $this -> tablePrefix . 'role_user';
		$rs = $this -> db -> query('select a.role_id from ' . $table . ' as a where a.user_id=' . $user_id . ' ');
		return $rs;
	}

	function del_role($user_list) {
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

		$table = $this -> tablePrefix . 'role_user';

		$result = $this -> db -> execute('delete from ' . $table . ' where user_id in (' . $user_list . ')');
		if ($result === false) {
			return false;
		} else {
			return true;
		}
	}

	function set_role($user_list, $role_list) {

		if (empty($user_list)) {
			return true;
		}
		if (empty($role_list)) {
			return true;
		}
		if (is_array($user_list)) {
			$user_list = array_filter($user_list);
		} else {
			$user_list = explode(",", $user_list);
			$user_list = array_filter($user_list);
		}
		$user_list = implode(",", $user_list);

		if (is_array($role_list)) {
			$role_list = array_filter($role_list);
		} else {
			$role_list = explode(",", $role_list);
			$role_list = array_filter($role_list);
		}
		$role_list = implode(",", $role_list);

		$where = 'a.id in (' . $user_list . ') AND b.id in(' . $role_list . ')';
		$sql = 'INSERT INTO ' . $this -> tablePrefix . 'role_user (user_id,role_id) ';
		$sql .= ' SELECT a.id, b.id FROM ' . $this -> tablePrefix . 'user a, ' . $this -> tablePrefix . 'role b WHERE ' . $where;
		$result = $this -> execute($sql);
		if ($result === false) {
			return false;
		} else {
			return true;
		}
	}

	function get_duty_list($role_list) {
		if (is_array($role_list)) {
			$role_list = array_filter($role_list);
		} else {
			$role_list = explode(",", $role_list);
			$role_list = array_filter($role_list);
		}
		$role_list = implode(",", $role_list);
		$rs = $this -> db -> query('select distinct duty_id from ' . $this -> tablePrefix . 'role_duty as a where a.role_id in(' . $role_list . ')');
		return $rs;
	}

	function del_duty($role_list) {
		if (empty($role_list)) {
			return true;
		}
		if (is_array($role_list)) {
			$role_list = array_filter($role_list);
		} else {
			$role_list = explode(",", $role_list);
			$role_list = array_filter($role_list);
		}
		$role_list = implode(",", $role_list);

		$table = $this -> tablePrefix . 'role_duty';

		$result = $this -> db -> execute('delete from ' . $table . ' where role_id in (' . $role_list . ')');
		if ($result === false) {
			return false;
		} else {
			return true;
		}
	}

	function set_duty($role_list, $duty_list) {
		if (empty($role_list)) {
			return true;
		}
		//dump($role_id);
		if (is_array($role_list)) {
			$role_list = array_filter($role_list);
		} else {
			$role_list = array_filter(explode(",", $role_list));
		}
		$role_list = implode(",", $role_list);

		if (empty($duty_list)) {
			return true;
		}
		if (is_array($duty_list)) {
			$duty_list = array_filter($duty_list);
		} else {
			$duty_list = array_filter(explode(",", $duty_list));
		}
		$duty_list = implode(",", $duty_list);

		$where = 'a.id in(' . $role_list . ') AND b.id in(' . $duty_list . ')';
		$sql = 'INSERT INTO ' . $this -> tablePrefix . 'role_duty (role_id,duty_id)';
		$sql .= ' SELECT a.id, b.id FROM ' . $this -> tablePrefix . 'role a, ' . $this -> tablePrefix . 'duty b WHERE ' . $where;
		$result = $this -> execute($sql);
		return result;
	}

	function get_auth($module_name,$user_id=null) {
		if(empty($user_id)){
			$user_id=get_user_id();
		}
		$access_list = D("Node") -> access_list($user_id);
		$access_list = array_filter($access_list, array($this, 'filter_module'));
		$access_list = rotate($access_list);

		$module_list = $access_list['url'];
		$module_list = array_map(array($this, "get_module"), $module_list);
		$module_list = str_replace("_", "", $module_list);

		$access_list_admin = array_filter(array_combine($module_list, $access_list['admin']));
		$access_list_write = array_filter(array_combine($module_list, $access_list['write']));
		$access_list_read = array_filter(array_combine($module_list, $access_list['read']));

		$auth['admin'] = array_key_exists($module_name, $access_list_admin) || array_key_exists("##" . $module_name, $access_list_admin);

		$auth['write'] = array_key_exists($module_name, $access_list_write) || array_key_exists("##" . $module_name, $access_list_write);

		$auth['read'] = array_key_exists($module_name, $access_list_read) || array_key_exists("##" . $module_name, $access_list_read);

		if ($auth['admin'] == true) {
			$auth['write'] = true;
		}
		if ($auth['write'] == true) {
			$auth['read'] = true;
		}
		return $auth;
	}

	function get_module($str) {
		$arr_str = explode("/", $str);
		return $arr_str[0];
	}

	function filter_module($str) {
		if (strpos($str['url'], '##') !== false) {
			return true;
		}
		if (empty($str['admin']) && empty($str['write']) && empty($str['read'])) {
			return false;
		}
		if (strpos($str['url'], 'index')) {
			return true;
		}
		return false;
	}

	function check_duty($duty_no, $user_id = null) {
		if (empty($user_id)) {
			$user_id = get_user_id();
		}

		$role_list = $this -> get_role_list($user_id);
		$role_list = rotate($role_list);
		$role_list = $role_list['role_id'];

		$duty_list = $this -> get_duty_list($role_list);
		$duty_list = rotate($duty_list);
		$duty_list = $duty_list['duty_id'];

		$where_duty_id['duty_no'] = array('eq', $duty_no);
		$show_log_duty_id = M("Duty") -> where($where_duty_id) -> getField('id');

		if (in_array($show_log_duty_id, $duty_list)) {
			return true;
		} else {
			return false;
		}
	}

}
?>