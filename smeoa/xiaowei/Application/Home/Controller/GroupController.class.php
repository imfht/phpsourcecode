<?php
/*--------------------------------------------------------------------
 小微OA系统 - 让工作更轻松快乐

 Copyright (c) 2013 http://www.smeoa.com All rights reserved.

 Author:  jinzhu.yin<smeoa@qq.com>

 Support: https://git.oschina.net/smeoa/xiaowei
--------------------------------------------------------------*/

// 用户组模块
namespace Home\Controller;

class GroupController extends HomeController {
	protected $config = array('app_type' => 'master');

	//过滤查询字段
	function _search_filter(&$map) {
		$map['is_del'] = array('eq', '0');
		$keyword = I('keyword');
		if (!empty($keyword)) {
			$map['User.name|emp_no|Position.name|Dept.name'] = array('like', "%" . $keyword . "%");
		}
	}

	public function index() {
		$list = M("Group") -> order('sort asc') -> select();
		$this -> assign('list', $list);
		$this -> display();
	}

	public function del($id) {
		$model = M("Group");
		$where_group['id'] = array('eq', $id);
		$model -> where($where_group) -> delete();

		$model = M("GroupUser");
		$where_group_user['id'] = array('eq', $id);
		$model -> where($where_group_user) -> delete();
		$this -> success('删除成功');
	}

	public function get_node_list() {
		$role_id = $_POST["role_id"];
		$model = D("Role");
		$data = $model -> get_node_list($role_id);
		if ($data !== false) {// 读取成功
			$return['data'] = $data;
			$return['status'] = 1;
			$this -> ajaxReturn($return);
		}
	}

	public function user($id) {
		$map = $this -> _search();
		if (method_exists($this, '_search_filter')) {
			$this -> _search_filter($map);
		}

		$row_info = M("Group") -> find($id);
		$this -> assign('row_info', $row_info);

		$where_group_user['group_id'] = array('eq', $id);
		$group_user = M("GroupUser") -> where($where_group_user) -> getField('user_id', true);

		if (!empty($group_user)) {
			$where_user_list['id'] = array('in', $group_user);
		} else {
			$where_user_list['_string'] = '1=2';
		}

		$user_list = D("UserView") -> where($where_user_list) -> select();
		$this -> assign("user_list", $user_list);

		$this -> display();
	}

	public function add_user($group_id) {
		$map = $this -> _search();
		if (method_exists($this, '_search_filter')) {
			$this -> _search_filter($map);
		}
		$this -> assign('group_id', $group_id);

		$model = D("Group");
		$user_list = $model -> get_user_list($group_id);

		if (!empty($user_list)) {
			$map['id'] = array('not in', $user_list);
		}
		$user_list = D("UserView") -> where($map) -> select();
		$this -> assign("user_list", $user_list);
		$this -> display();
	}

	public function del_user($group_id, $user_id) {
		$model = D("Group");
		$result = $model -> del_user($group_id, $user_id);
		if ($result === false) {
			$this -> error('操作失败！');
		} else {
			$this -> assign('jumpUrl', get_return_url());
			$this -> success('操作成功！');
		}
	}

	public function save_user($group_id, $user_id) {
		$model = D("Group");
		$result = $model -> save_user($user_id, $group_id);
		if ($result === false) {
			$this -> error('操作失败！');
		} else {
			$this -> assign('jumpUrl', get_return_url());
			$this -> success('操作成功！');
		}
	}
}
?>