<?php

/**
 * ==========================================
 * Created by Pocket Knife Technology.
 * Author: ZhiHua_W <zhihua_wei@foxmail.com>
 * Date: 2016/11/03 0001
 * Time: 下午 3:19
 * Project: Pkadmin后台管理系统
 * Version: 1.0.0
 * Power: 后台登录模型
 * ==========================================
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Login_model extends CI_Model {
	const TBL_ADMIN = 'admin';
	const TBL_LOG = 'admin_log';

	/**
	 * 函数：获取登录用户信息
	 * @param string $username 用户名
	 * @param string $password 加密后的密码
	 * @return array 登录用户信息
	 */
	public function get_admin_info($username, $password) {
		$condition = array('username' => $username, 'password' => $password, );
		$result = $this -> db -> where($condition) -> get(self::TBL_ADMIN);
		return $result -> row_array();
	}

	/**
	 * 函数：设置登录用户的认证信息
	 * @param int $admin_id 管理员用户id
	 * @param string $identifier 用户标识
	 * @param string $token 用户令牌
	 * @param string $salt 随机字符
	 */
	public function set_admin_auth($admin_id, $identifier, $token, $salt) {
		$condition['admin_id'] = $admin_id;
		$params = array('identifier' => $identifier, 'token' => $token, 'salt' => $salt, );
		$this -> db -> where($condition) -> update(self::TBL_ADMIN, $params);
	}

	/**
	 * 函数：获取日志记录总条数
	 * @param int $admin_id 管理员id
	 * @return int 日志纪录条数
	 */
	public function get_log_count($admin_id) {
		if ($admin_id == 1) {
			return $this -> db -> count_all(self::TBL_LOG);
		} else {
			return $this -> db -> where("admin_id = {$admin_id}") -> count_all(self::TBL_LOG);
		}
	}

	/**
	 * 函数：获取管理员操作日志列表
	 * @param int $admin_id 管理员id
	 * @param int $limit 每页显示数
	 * @param int $offset 偏移量
	 * @return array 操作日志列表
	 */
	public function get_admin_log_list($admin_id, $limit, $offset) {
		if ($admin_id == 1) {
			return $this -> db -> order_by('log_id', 'DESC') -> limit($limit, $offset) -> get(self::TBL_LOG) -> result_array();
		} else {
			return $this -> db -> where("admin_id = {$admin_id}") -> order_by('log_id', 'DESC') -> limit($limit, $offset) -> get(self::TBL_LOG) -> result_array();
		}
	}

	/**
	 * 函数：删除30天前的操作日志记录
	 * @param time $time 时间(当前时间减去30天)
	 */
	public function del_month_ago_log($time) {
		$this -> db -> where("time < {$time}") -> delete(self::TBL_LOG);
	}

}
