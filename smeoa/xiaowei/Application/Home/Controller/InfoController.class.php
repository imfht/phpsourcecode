<?php
/*--------------------------------------------------------------------
 小微OA系统 - 让工作更轻松快乐

 Copyright (c) 2013 http://www.smeoa.com All rights reserved.

 Author:  jinzhu.yin<smeoa@qq.com>

 Support: https://git.oschina.net/smeoa/xiaowei
--------------------------------------------------------------*/

namespace Home\Controller;

class InfoController extends HomeController {

	protected $config = array('app_type' => 'folder', 'read' => 'my_sign,my_info,sign_info,folder,sign', 'admin' => 'mark,move_to,folder_manage', 'write' => 'sign_report,upload');

	//过滤查询字段
	function _search_filter(&$map) {
		$map['is_del'] = array('eq', '0');
		$keyword = I('keyword');
		if (!empty($keyword) && empty($map['name'])) {
			$map['name'] = array('like', "%" . $keyword . "%");
		}
	}

	public function index() {

		$plugin['date'] = true;
		$this -> assign("plugin", $plugin);

		$unread_info = $this -> _unread_info();
		$this -> assign("unread_info", $unread_info);

		$map = $this -> _search();
		if (method_exists($this, '_search_filter')) {
			$this -> _search_filter($map);
		}

		$user_id = get_user_id();
		$dept_id = get_dept_id();

		$map['_string'] = " Info.is_public=1 or Info.dept_id=$dept_id ";

		$where_scope['user_id'] = $user_id;
		$info_list = M("InfoScope") -> where($where_scope) -> getField('info_id', true);
		
		if (!empty($info_list)) {
			$info_list = implode(",", $info_list);
			$map['_string'] .= "or Info.id in ($info_list)";
		}

		$folder_list = D("SystemFolder") -> get_authed_folder();
		if ($folder_list) {
			$map['folder'] = array("in", $folder_list);
		} else {
			$map['_string'] = '1=2';
		}

		$model = D("InfoView");
		if (!empty($model)) {
			$this -> _list($model, $map, 'id desc');
		}

		$this -> display();
	}

	public function my_info() {

		$plugin['date'] = true;
		$this -> assign("plugin", $plugin);

		$map = $this -> _search();
		if (method_exists($this, '_search_filter')) {
			$this -> _search_filter($map);
		}

		$user_id = get_user_id();
		$map['user_id'] = array('eq', $user_id);

		$model = D("InfoView");
		if (!empty($model)) {
			$this -> _list($model, $map);
		}
		$this -> display();
	}

	public function my_sign() {

		$plugin['date'] = true;
		$this -> assign("plugin", $plugin);

		$map = $this -> _search();
		if (method_exists($this, '_search_filter')) {
			$this -> _search_filter($map);
		}

		$user_id = get_user_id();
		$where['user_id'] = array('eq', $user_id);
		$sign_list = M("InfoSign") -> where($where) -> getField('info_id', true);

		if ($sign_list) {
			$map['id'] = array('in', $sign_list);

			$model = D("InfoView");
			if (!empty($model)) {
				$this -> _list($model, $map);
			}
		}
		$this -> display();
	}

	public function del($id) {
		$this -> _del($id);
	}

	public function move_to($id, $val) {
		$where['id'] = array('in', $id);
		$folder = M("Info") -> distinct(true) -> where($where) -> field("folder") -> select();
		if (count($folder) == 1) {
			$auth = D("SystemFolder") -> get_folder_auth($folder[0]["folder"]);
			if ($auth['admin'] == true) {
				$field = 'folder';
				$this -> _set_field($id, $field, $val);
			}
			$return['info'] = '操作成功';
			$return['status'] = 1;
			$this -> ajaxReturn($return);
		} else {
			$return['info'] = '操作成功';
			$return['status'] = 1;
			$this -> ajaxReturn($return);
		}
	}

	function sign($id) {
		$user_id = get_user_id();

		$model = M("Info");
		$folder_id = $model -> where("id=$id") -> getField('folder');

		$Form = D('InfoSign');
		$data['info_id'] = $id;
		$data['user_id'] = $user_id;
		$data['folder'] = $folder_id;
		$data['user_name'] = get_user_name();
		$data['dept_id'] = get_dept_id();
		$data['dept_name'] = get_dept_name();

		$data['is_sign'] = '1';
		$data['sign_time'] = time();
		$result = $Form -> add($data);
		if ($result) {
			$this -> _readed($id);
			$return['status'] = 1;
			$return['info'] = "签收成功";
			$this -> ajaxReturn($return);
		} else {
			$return['status'] = 0;
			$return['info'] = "签收失败";
			$this -> ajaxReturn($return);
		}
	}

	function sign_info($id) {

		$row_info = M('Info') -> find($id);
		$this -> assign('row_info', $row_info);

		$model = M("InfoSign");
		$where['info_id'] = array('eq', $id);
		$where['user_id'] = array('eq', get_user_id());
		$list = $model -> where($where) -> find();
		$this -> assign('vo', $list);
		$this -> display();
	}

	function sign_report($id) {

		$row_info = M("Info") -> find($id);
		//dump($row_info);
		$this -> assign('row_info', $row_info);

		//签收人员
		$signed_user = M("InfoSign") -> where("info_id=$id") -> getField('user_id', true);

		//发布范围
		$sign_time = M("InfoSign") -> where("info_id=$id") -> getField('user_id,sign_time');
		$this->assign('sign_time',$sign_time);
		
		//发布范围
		$actor_user = M("InfoScope") -> where("info_id=$id") -> getField('user_id', true);

		//未签收人员
		if (!empty($signed_user)) {
			$un_sign_user = array_diff($actor_user, $signed_user);
		} else {
			$un_sign_user = $actor_user;
		}

		$model = D("UserView");
		if (!empty($signed_user)) {
			$where_signed['id'] = array('in', $signed_user);
			$signed_user_info = $model -> where($where_signed) -> select();
			$this -> assign('signed_user_info', $signed_user_info);
		}

		if (!empty($un_sign_user)) {
			$where_un_sign['id'] = array('in', $un_sign_user);
			$un_sign_user_info = $model -> where($where_un_sign) -> select();
			$this -> assign('un_sign_user_info', $un_sign_user_info);
		}

		$this -> display();
	}

	function add() {
		$plugin['uploader'] = true;
		$plugin['editor'] = true;
		$this -> assign("plugin", $plugin);

		$fid = I('fid');
		$this -> assign('folder', $fid);
		$folder_name = D("SystemFolder") -> get_folder_name($fid);
		$this -> assign("folder_name", $folder_name);
		$this -> display();
	}

	public function edit($id) {
		$plugin['uploader'] = true;
		$plugin['editor'] = true;
		$this -> assign("plugin", $plugin);
		$this -> _edit($id);
	}

	public function read($id) {
		$user_id = get_user_id();
		$this -> assign('user_id', $user_id);

		$model = M('Info');
		$vo = $model -> find($id);
		$this -> assign('vo', $vo);
	//	echo(del_html_tag($vo['content']));

		$where_scope['info_id'] = array('eq', $id);
		$scope_user = M("InfoScope") -> where($where_scope) -> getField('user_id', true);
		$this -> assign('is_sign', 0);
		if (!empty($scope_user)) {
			if (in_array($user_id, $scope_user)) {
				if ($vo['is_sign']) {
					$sign_info = D("InfoSign") -> get_info($id);
					$this -> assign('sign_info', $sign_info);
					$this -> assign('is_sign', 1);
				} else {
					$this -> _readed($id);
				}
			} else {
				$this -> _readed($id);
			}
		}

		$where['id'] = array('eq', $id);
		$folder_id = $model -> where($where) -> getField('folder');
		$auth = $auth = D("SystemFolder") -> get_folder_auth($folder_id);

		$this -> assign("auth", $auth);

		$admin = M("SystemFolder") -> where("id=$folder_id") -> getField('admin');

		$this -> assign('admin', $admin);

		$this -> display();
	}

	public function folder($fid) {
		$plugin['date'] = true;
		$this -> assign("plugin", $plugin);
		$this -> assign('auth', $this -> config['auth']);

		$this -> assign('fid', $fid);

		$unread_info = $this -> _unread_info();
		$this -> assign("unread_info", $unread_info);

		$model = D("InfoView");
		$map = $this -> _search();
		if (method_exists($this, '_search_filter')) {
			$this -> _search_filter($map);
		}

		$map['folder'] = array('eq', $fid);

		$dept_id = get_dept_id();
		$map['_string'] = " Info.is_public=1 or Info.dept_id=$dept_id ";

		$user_id = get_user_id();
		$where_scope['user_id'] = array('eq', $user_id);
		$scope_list = M("InfoScope") -> where($where_scope) -> getField('info_id', true);
		$scope_list = implode(",", $scope_list);

		if (!empty($scope_list)) {
			$map['_string'] .= "or Info.id in ($scope_list)";
		}

		if (!empty($model)) {
			$this -> _list($model, $map, 'id desc');
		}

		$this -> assign("folder_name", D("SystemFolder") -> get_folder_name($fid));
		$this -> _assign_folder_list();
		$this -> display();
	}

	public function folder_manage() {
		$this -> _system_folder_manage("信息管理");
	}

	public function upload() {
		$this -> _upload();
	}

	function down($attach_id) {
		$this -> _down($attach_id);
	}

	private function _unread_info() {

		$map['is_del'] = array('eq', '0');
		$map['create_time'] = array("egt", time() - 3600 * 24 * 30);

		$user_id = get_user_id();
		$where_scope['user_id'] = array('eq', $user_id);
		$scope_list = M("InfoScope") -> where($where_scope) -> getField('info_id', TRUE);

		if (!empty($scope_list)) {
			$map['id'] = array('in', $scope_list);
		} else {
			$map['_string'] = " 1=2";
		}

		$model = D("InfoView");
		$info_list = $model -> where($map) -> getField('id', true);

		$readed_info = M("UserConfig") -> where("id=$user_id") -> getField('readed_info');
		$readed_info = array_filter(explode(',', $readed_info));

		if (!empty($info_list)) {
			$un_read_doc = array_diff($info_list, $readed_info);
		} else {
			$un_read_doc = array();
		}
		return $un_read_doc;
	}

	private function _readed($id) {
		$user_id = get_user_id();
		$folder_list = D("SystemFolder") -> get_authed_folder();

		$where_readed['folder'] = array("in", $folder_list);
		$where_readed['create_time'] = array("egt", time() - 3600 * 24 * 30);

		$readed_list = array_filter(explode(",", get_user_config("readed_info") . "," . $id));

		$where_readed['id'] = array('in', $readed_list);

		$readed_info = M("Info") -> where($where_readed) -> getField("id", true);
		$readed_info = implode(",", $readed_info);

		$where_config['id'] = array('eq', $user_id);
		if (!empty($readed_info)) {
			M("UserConfig") -> where($where_config) -> setField('readed_info', $readed_info);
		}
	}

}
