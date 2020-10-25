<?php
/*--------------------------------------------------------------------
 小微OA系统 - 让工作更轻松快乐

 Copyright (c) 2013 http://www.smeoa.com All rights reserved.

 Author:  jinzhu.yin<smeoa@qq.com>

 Support: https://git.oschina.net/smeoa/xiaowei
 --------------------------------------------------------------*/

namespace Home\Controller;

class SystemFolderController extends HomeController {
	protected $config = array('app_type' => 'asst');

	//过滤查询字段
	function _search_filter(&$map) {
		$map['name'] = array('like', "%" . $_POST['name'] . "%");
		$map['is_del'] = array('eq', '0');
	}

	function add($controller) {
		$this -> assign('controller', $controller);
		$this->assign('has_pid',I('has_pid'));
		$this -> display();
	}

	function index() {
		$model = D("SystemFolder");
		if (IS_POST) {
			$opmode = $_POST["opmode"];
			if (false === $model -> create()) {
				$this -> error($model -> getError());
			}
			if ($opmode == "add") {
				$model -> controller = CONTROLLER_NAME;
				$list = $model -> add();
				if ($list != false) {
					$this -> success("添加成功");
				} else {
					$this -> error("添加成功");
				}
			}
			if ($opmode == "edit") {
				$list = $model -> save();
				if ($list != false) {
					$this -> success("保存成功");
				} else {
					$this -> error("保存失败");
				}
			}
			if ($opmode == "del") {
				$this -> _del($model -> id);
			}
		}

		$folder_list = $model -> get_folder_list();
		$tree = list_to_tree($folder_list);

		$this -> assign('menu', sub_tree_menu($tree));
		$this -> assign("folder_list", $folder_list);
		$this -> assign('controller', CONTROLLER_NAME);
		$this -> display('SystemFolder:index');
	}

	function read($id) {
		$model = M("SystemFolder");
		$data = $model -> find($id);
		if ($data !== false) {// 读取成功
			$return['data'] = $data;
			$this -> ajaxReturn($return);
		}
	}

	function _del($id, $name = CONTROLLER_NAME, $return_flag = false) {
		$model = D("SystemFolder");
		$data = $model -> getById($id);
		$controller = $data['controller'];
		$count = M($controller) -> where(array('folder' => $id, 'is_del' => 0)) -> count();

		$sub_folder_list = tree_to_list(list_to_tree($model -> get_folder_list(), $id));
		if ($count > 0 || !empty($sub_folder_list)) {// 读取成功
			$this -> error('只能删除空文件夹');
		} else {
			$result = $model -> where(array('id' => $id)) -> setField("is_del", 1);
			if ($return_flag) {
				return $result;
			}
			if ($result) {
				$this -> success('删除成功');
				die ;
			}
		}
	}

	function winpop($controller) {
		$where['controller'] = $controller;
		$where['is_del'] = 0;
		$menu = M("SystemFolder") -> where($where) -> field('id,pid,name') -> order('sort asc') -> select();
		$tree = list_to_tree($menu);
		$this -> assign('menu', popup_tree_menu($tree));
		$this -> display("SystemFolder:winpop");
	}
}
