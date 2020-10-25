<?php

/**
 * ==========================================
 * Created by Pocket Knife Technology.
 * Author: ZhiHua_W <zhihua_wei@foxmail.com>
 * Date: 2016/11/04 0011
 * Time: 下午 5:19
 * Project: Pkadmin后台管理系统
 * Version: 1.0.0
 * Power: Pkadmin管理系统模型
 * ==========================================
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Pkadmin_model extends CI_Model {
	const TBL_LOG = 'admin_log';
	const TBL_ADMIN = 'admin';
	const TBL_SETTING = 'setting';

	/**
	 * 函数：获取系统设置信息
	 * @param string $key 可选，配置key值
	 * @return array 系统配置信息
	 */
	public function get_setting($key = '') {
		if ($key == '') {
			$setting = $this -> db -> select("key,val") -> get(self::TBL_SETTING) -> result_array();
			foreach ($setting as $k => $v) {
				$config[$v['key']] = $v['val'];
			}
			return $config;
		} else {
			$setting = $this -> db -> where("key = {$key}") -> get(self::TBL_SETTING) -> row_array();
			return $setting['val'];
		}
	}

	/**
	 * 函数：添加系统操作记录日志
	 * @param string $log 日志描述
	 * @param int $admin_id 管理员id
	 * @param string $username 操作管理员姓名
	 */
	public function add_log($log, $admin_id, $username) {
		$params['admin_id'] = intval($admin_id);
		$params['username'] = $username;
		$params['time'] = time();
		$params['ip'] = GetHostByName($_SERVER['SERVER_NAME']);
		$params['log'] = $log;
		$this -> db -> insert(self::TBL_LOG, $params);
	}

	/**
	 * 函数：根据用户标识获取用户信息
	 * @param string $identifier 用户标识
	 * @return array 登录用户信息
	 */
	public function get_admin_info_by($identifier) {
		$condition = array('identifier' => $identifier);
		return $this -> db -> where($condition) -> get(self::TBL_ADMIN) -> row_array();
	}

	/**
	 * 函数：获取管理员操作权限
	 * @param int $admin_id 管理员id
	 * @return array 管理员操作权限
	 */
	public function get_admin_auth_group($admin_id) {
		$prefix = $this -> db -> dbprefix;
		$sql = "SELECT * 
				FROM {$prefix}auth_group g 
				LEFT JOIN {$prefix}auth_group_access a on g.id=a.group_id 
				WHERE a.admin_id={$admin_id}";
		return $this -> db -> query($sql) -> row_array();
	}

	/**
	 * 函数：获取当前操作权限信息
	 * @param string $controller 控制器名称
	 * @param string $action 操作方法名称
	 * @return array 当前操作权限信息
	 */
	public function get_current_rules($controller, $action) {
		$prefix = $this -> db -> dbprefix;
		$sql = "SELECT s.id,s.title,s.name,s.tips,s.pid,p.pid as ppid,p.title as ptitle 
				FROM {$prefix}auth_rule s 
				LEFT JOIN {$prefix}auth_rule p on p.id=s.pid
				where s.name='" . $controller . '/' . $action . "'";
		return $this -> db -> query($sql) -> row_array();
	}

	/**
	 * 函数：获取操作菜单信息
	 * @param string $condition 查询条件
	 */
	public function get_menu_rules($condition) {
		if (!empty($condition)) {
			return $this -> db -> select('id,title,pid,name,icon') -> where("islink = 1 AND id !=1 {$condition}") -> order_by('sort', 'ASC') -> get('auth_rule') -> result_array();
		} else {
			return $this -> db -> select('id,title,pid,name,icon') -> where("islink = 1 AND id !=1") -> order_by('sort', 'ASC') -> get('auth_rule') -> result_array();
		}
	}

	/**
	 * 函数：根据id，密码查找管理员信息
	 * @param int $admin_id 管理员id
	 * @param string $password 登录密码
	 * @return array 管理员信息
	 */
	public function seach_admin_by($admin_id, $password) {
		$condition = array('admin_id' => $admin_id, 'password' => $password, );
		$result = $this -> db -> where($condition) -> get(self::TBL_ADMIN);
		return $result -> row_array();
	}

	/**
	 * 函数：用户修改登录密码
	 * @param int $admin_id 管理员id
	 * @param string $password 新登录密码
	 * @return bool
	 */
	public function set_admin_password($admin_id, $password) {
		$condition['admin_id'] = $admin_id;
		$params = array('password' => $password);
		return $this -> db -> where($condition) -> update(self::TBL_ADMIN, $params);
	}

	/**
	 * 函数：用户修改个人信息
	 * @param int $admin_id 管理员id
	 * @param array $params 用户信息
	 * @return bool
	 */
	 public function set_admin_profile($admin_id, $params){
	 	$condition['admin_id'] = $admin_id;
		return $this -> db -> where($condition) -> update(self::TBL_ADMIN, $params);
	 }

}
