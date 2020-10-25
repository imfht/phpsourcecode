<?php
/*--------------------------------------------------------------------
 小微OA系统 - 让工作更轻松快乐

 Copyright (c) 2013 http://www.smeoa.com All rights reserved.

 Author:  jinzhu.yin<smeoa@qq.com>

 Support: https://git.oschina.net/smeoa/xiaowei
--------------------------------------------------------------*/

namespace Home\Controller;

class DutyController extends HomeController {
	protected $config = array('app_type' => 'master');

	public function index() {

		$list = M("Duty") -> order('sort asc') -> select();
		$this -> assign('list', $list);
		$this -> display();
	}	
	
	public function _search_filter(&$map) {
		$pid=I('pid');
		if (!empty($pid)) {
			$map['pid'] = $pid;
		}
	}

	public function del() {
		$role_id = $_POST['id'];
		$where['role_id'] = $role_id;
		$model = M("RoleDuty");
		$model -> where($where) -> delete();
		$this -> _destory($role_id);
	}

	public function user() {
		$keyword=I('keyword');

		$user_list = D("User") -> get_user_list($keyword);
		$this -> assign("user_list", $user_list);

		$role = M("Duty");
		$where['is_del']=array('eq',0);
		$duty_list = $role ->where($where)-> order('sort asc') -> select();
		$this -> assign("duty_list", $duty_list);
		$this -> display();
	}

}
?>