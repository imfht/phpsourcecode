<?php

/**
 * ==========================================
 * Created by Pocket Knife Technology.
 * Author: ZhiHua_W <zhihua_wei@foxmail.com>
 * Date: 2016/11/22 0024
 * Time: 下午 3:36
 * Project: Pkadmin后台管理系统
 * Version: 1.0.0
 * Power: 后台角色权限组管理控制器
 * ==========================================
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Authgroup extends Pkadmin_Controller {

	public function __construct() {
		parent::__construct();
		$this -> load -> model('admingroup_model', 'ag');
	}

	/**
	 * 角色权限用户组管理首页
	 */
	public function index() {
		$data = $this -> data;
		$data['auth_group'] = $this -> ag -> get_auth_group_list();
		$this -> load -> view('authgroup.html', $data);
	}

	/**
	 * 删除角色权限
	 */
	public function del($id) {
		$data = $this -> data;
		//角色下存在用户，不允许删除
		if ($this -> ag -> get_admincount_of_authgroup($id)) {
			$error['msg'] = "此角色权限下存在管理员用户，不允许删除！";
			$error['url'] = site_url("Pkadmin/Authgroup/index");
			$error['wait'] = 3;
			$data['error'] = $error;
			$this -> load -> view('error.html', $data);
			return;
		}
		if ($this -> ag -> del_auth_group($id)) {
			$this -> pk -> add_log('删除角色权限组，ID：' . $id, $this -> ADMINISTRSTORS['admin_id'], $this -> ADMINISTRSTORS['username']);
			$success['msg'] = "角色权限组删除成功！";
			$success['url'] = site_url("Pkadmin/Authgroup/index");
			$success['wait'] = 3;
			$data['success'] = $success;
			$this -> load -> view('success.html', $data);
		} else {
			$error['msg'] = "角色权限组删除失败！";
			$error['url'] = site_url("Pkadmin/Authgroup/index");
			$error['wait'] = 3;
			$data['error'] = $error;
			$this -> load -> view('error.html', $data);
		}
	}

	/**
	 * 修改角色
	 */
	public function edit($id) {
		$data = $this -> data;
		//获得所有启用的操作菜单
		$auth_rule = $this -> ag -> get_all_auth_rule();
		$data['auth_rule_tree'] = $this -> get_menu_tree($auth_rule);
		$auth_group = $this -> ag -> get_auth_group_info($id);
		if (!$auth_group) {
			$error['msg'] = "参数错误，请检查！";
			$error['url'] = site_url("Pkadmin/Authgroup/index");
			$error['wait'] = 3;
			$data['error'] = $error;
			$this -> load -> view('error.html', $data);
			return;
		}
		$auth_group['rules'] = explode(',', $auth_group['rules']);
		$data['auth_group'] = $auth_group;
		$this -> load -> view('authgroup_edit.html', $data);
	}

	/**
	 * 添加角色
	 */
	public function add() {
		$data = $this -> data;
		$auth_rule = $this -> ag -> get_all_auth_rule();
		$data['auth_rule_tree'] = $this -> get_menu_tree($auth_rule);
		$this -> load -> view('authgroup_add.html', $data);
	}

	/**
	 * 新增或修改角色角色信息
	 */
	public function update() {
		$data = $this -> data;
		$id = $this -> input -> post('id');
		$params['title'] = $this -> input -> post('title');
		$status = $this -> input -> post('status');
		if ($status == 'on') {
			$params['status'] = 1;
		} else {
			$params['status'] = 0;
		}
		$params['note'] = $this -> input -> post('note');
		$rules = $this -> input -> post('rules');
		if (is_array($rules)) {
			foreach ($rules as $k => $v) {
				$rules[$k] = intval($v);
			}
			$rules = implode(',', $rules);
		}
		$params['rules'] = $rules;
		if ($id) {
			//修改角色信息
			if ($this -> ag -> update_auth_group($id, $params)) {
				$this -> pk -> add_log('修改角色权限组：' . $params['title'], $this -> ADMINISTRSTORS['admin_id'], $this -> ADMINISTRSTORS['username']);
				$success['msg'] = "修改角色权限组成功！";
				$success['url'] = site_url("Pkadmin/Authgroup/index");
				$success['wait'] = 3;
				$data['success'] = $success;
				$this -> load -> view('success.html', $data);
			} else {
				$error['msg'] = "修改角色权限组失败！";
				$error['url'] = site_url("Pkadmin/Authgroup/index");
				$error['wait'] = 3;
				$data['error'] = $error;
				$this -> load -> view('error.html', $data);
			}
		} else {
			//插入角色信息
			if ($this -> ag -> insert_auth_group($params)) {
				$this -> pk -> add_log('新增角色权限组：' . $params['title'], $this -> ADMINISTRSTORS['admin_id'], $this -> ADMINISTRSTORS['username']);
				$success['msg'] = "新增角色权限组成功！";
				$success['url'] = site_url("Pkadmin/Authgroup/index");
				$success['wait'] = 3;
				$data['success'] = $success;
				$this -> load -> view('success.html', $data);
			} else {
				$error['msg'] = "新增角色权限组失败！";
				$error['url'] = site_url("Pkadmin/Authgroup/index");
				$error['wait'] = 3;
				$data['error'] = $error;
				$this -> load -> view('error.html', $data);
			}
		}
	}

}
