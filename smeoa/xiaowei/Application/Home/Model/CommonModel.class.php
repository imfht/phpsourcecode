<?php

namespace Home\Model;
use Think\Model;

class CommonModel extends Model {
	protected $_auto = array(
		array('is_del', '0', self::MODEL_INSERT),
		array('create_time', 'time', self::MODEL_INSERT, 'function'),
		array('update_time', 'time', self::MODEL_UPDATE, 'function'),
		array('user_id', 'get_user_id', self::MODEL_INSERT, 'callback'),
		array('user_name', 'get_user_name', self::MODEL_INSERT, 'callback'),
		array('dept_id', 'get_dept_id', self::MODEL_INSERT, 'callback'),
		array('dept_name', 'get_dept_name', self::MODEL_INSERT, 'callback'),
	 );

	function get_user_id() {
		$user_id = session(C('USER_AUTH_KEY'));
		return isset($user_id) ? $user_id : 0;
	}

	function get_user_name() {
		$user_name = session('user_name');
		return isset($user_name) ? $user_name : 0;
	}

	function get_dept_id() {
		$user_name = session('dept_id');
		return isset($user_name) ? $user_name : 0;
	}

	function get_dept_name() {
		$result = M("Dept") -> find(session("dept_id"));
		return $result['name'];
	}
}
?>