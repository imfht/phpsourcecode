<?php

/**
 * ==========================================
 * Created by Pocket Knife Technology.
 * Author: ZhiHua_W <zhihua_wei@foxmail.com>
 * Date: 2016/11/02 0031
 * Time: 上午 9:23
 * Project: Pkadmin后台管理系统
 * Version: 1.0.0
 * Power:  控制器扩展
 * ==========================================
 */

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 前台父控制器
 */
class Home_Controller extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this -> load -> set_home_view_dir();
	}

}

/**
 * 后台父类控制器
 */
class Pkadmin_Controller extends CI_Controller {

	public $data;

	public function __construct() {
		parent::__construct();
		$this -> load -> set_admin_view_dir();
		$this -> data['setting'] = $this -> pk -> get_setting();
		//判断是否已登录
		$flag = false;
		$auth = get_cookie('auth');
		if (!empty($auth)) {
			list($identifier, $token) = explode(',', $auth);
			if (ctype_alnum($identifier) && ctype_alnum($token)) {
				$admin = $this -> pk -> get_admin_info_by($identifier);
				if ($admin) {
					if ($token == $admin['token'] && $admin['identifier'] == password($admin['admin_id'] . md5($admin['username'] . $admin['salt']))) {
						$flag = true;
						$admin['head_pic'] = unserialize($admin['head_pic']);
						$this -> ADMINISTRSTORS = $admin;
					}
				}
			}
		}
		if (!$flag) {
			redirect('Pkadmin/Login/index');
			exit(0);
		}
		$admin_id = $this -> ADMINISTRSTORS['admin_id'];
		$admin_auth_group = $this -> pk -> get_admin_auth_group($admin_id);
		//不需要权限验证的控制器名称
		$allow_controller_name = array('Login');
		//不需要权限验证的函数名称
		$allow_action_name = array();
		//获取当前访问的控制器名称
		$controller = $this -> router -> fetch_class();
		//获取当前访问的方法名称
		$action = $this -> router -> fetch_method();
		//权限验证
		if ($admin_auth_group['group_id'] != 1 && !$this -> auth -> auth_chack($controller . '/' . $action, $admin_id) && !in_array($controller, $allow_controller_name) && !in_array($action, $allow_action_name)) {
			redirect('Pkadmin/Login/visit_error');
			exit(0);
		}
		//当前方法名称
		$current_action_name = ($action == "edit" ? "index" : $action);
		$current_rules = $this -> pk -> get_current_rules($controller, $current_action_name);
		$menu_access_id = $admin_auth_group['rules'];

		if ($admin_auth_group['group_id'] != 1) {
			$menu_where = "AND id in ($menu_access_id)";
		} else {
			$menu_where = null;
		}
		$menu = $this -> pk -> get_menu_rules($menu_where);
		$menu = $this -> get_menu_tree($menu);

		$this -> data['current'] = $current_rules;
		$this -> data['menu'] = $menu;
		$this -> data['admininfo'] = $this -> ADMINISTRSTORS;
	}

	/**
	 * 函数：操作菜单列表生成树状
	 * @param array $items 菜单数组
	 * @return array $tree 菜单树
	 */
	protected function get_menu_tree($items, $id = 'id', $pid = 'pid', $son = 'children') {
		$tree = array();
		$tmpMap = array();

		foreach ($items as $item) {
			$tmpMap[$item[$id]] = $item;
		}

		foreach ($items as $item) {
			if (isset($tmpMap[$item[$pid]])) {
				$tmpMap[$item[$pid]][$son][] = &$tmpMap[$item[$id]];
			} else {
				$tree[] = &$tmpMap[$item[$id]];
			}
		}
		return $tree;
	}

}
