<?php
/*--------------------------------------------------------------------
 小微OA系统 - 让工作更轻松快乐

 Copyright (c) 2013 http://www.smeoa.com All rights reserved.

 Author:  jinzhu.yin<smeoa@qq.com>

 Support: https://git.oschina.net/smeoa/xiaowei
--------------------------------------------------------------*/

namespace Home\Controller;

class DocController extends HomeController {
	protected $config = array('app_type' => 'folder', 'admin' => 'del,move_to,folder_manage');

	//过滤查询字段
	function _search_filter(&$map) {
		$map['is_del'] = array('eq', '0');
		$keyword = I('keyword');
		if (!empty($keyword) && empty($map['64'])) {
			$map['name'] = array('like', "%" . $keyword . "%");
		}
	}

	public function index() {

		$plugin['date'] = true;
		$this -> assign("plugin", $plugin);

		$map = $this -> _search();
		if (method_exists($this, '_search_filter')) {
			$this -> _search_filter($map);
		}

		$folder_list = D("SystemFolder") -> get_authed_folder();
		if (!empty($folder_list)) {
			$map['folder'] = array("in", $folder_list);
		} else {
			$map['_string'] = '1=2';
		}

		$model = D("DocView");

		if (!empty($model)) {
			$this -> _list($model, $map);
		}
		$this -> display();
	}

	public function edit($id) {
		$plugin['uploader'] = true;
		$plugin['editor'] = true;
		$this -> assign("plugin", $plugin);

		$model = M("Doc");
		$folder_id = $model -> where("id=$id") -> getField('folder');
		$this -> assign("auth", D("SystemFolder") -> get_folder_auth($folder_id));
		$this -> _edit($id);
	}

	public function folder($fid) {
		$plugin['date'] = true;
		$this -> assign("plugin", $plugin);
		$this -> assign('auth', $this -> config['auth']);

		$model = D("Doc");
		$map = $this -> _search();
		if (method_exists($this, '_search_filter')) {
			$this -> _search_filter($map);
		}

		$map['folder'] = $fid;

		if (!empty($model)) {
			$this -> _list($model, $map);
		}

		$where = array();
		$where['id'] = array('eq', $fid);

		$folder_name = M("SystemFolder") -> where($where) -> getField("name");
		$this -> assign("folder_name", $folder_name);
		$this -> assign("folder", $fid);

		$this -> _assign_folder_list();
		$this -> display();
		return;
	}

	public function add($fid) {
		$plugin['uploader'] = true;
		$plugin['editor'] = true;
		$this -> assign("plugin", $plugin);

		$this -> assign('folder', $fid);
		$this -> display();
	}

	public function read($id) {
		$model = M("Doc");
		$folder_id = $model -> where("id=$id") -> getField('folder');
		$this -> assign("auth", D("SystemFolder") -> get_folder_auth($folder_id));
		$this -> _edit($id);
	}

	public function del($id) {
		$where['id'] = array('in', $id);
		$folder = M("Doc") -> distinct(true) -> where($where) -> getField('folder',true);
		if (count($folder) == 1) {
			$auth = D("SystemFolder") -> get_folder_auth($folder[0]);
			if ($auth['admin'] == true) {
				$this -> _del($id);
			}
		} else {
			$return['info'] = "删除失败";
			$return['status'] = 0;
			$this -> ajaxReturn($return);
		}
	}

	public function move_to($id, $val) {
		$target_folder = $val;
		$where['id'] = array('in', $id);
		$folder = M("Doc") -> distinct(true) -> where($where) ->  getField('folder',true);
		if (count($folder) == 1) {
			$auth = D("SystemFolder") -> get_folder_auth($folder[0]);
			if ($auth['admin'] == true) {
				$field = 'folder';
				$result = $this -> _set_field($id, $field, $target_folder);

				if ($result) {
					$return['info'] = "操作成功";
					$return['status'] = 1;
					$this -> ajaxReturn($return);
				} else {
					$return['info'] = "操作失败";
					$return['status'] = 1;
					$this -> ajaxReturn($return);
				}
			}
		} else {
			$return['info'] = "操作成功";
			$return['status'] = 1;
			$this -> ajaxReturn($return);
		}
	}
	
	function folder_manage(){
		$this->_system_folder_manage('文档管理',true);
	} 
	
	function upload() {
		$this -> _upload();
	}

	function down($attach_id) {
		$this -> _down($attach_id);
	}

}
