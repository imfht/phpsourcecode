<?php
/*--------------------------------------------------------------------
 小微OA系统 - 让工作更轻松快乐

 Copyright (c) 2013 http://www.smeoa.com All rights reserved.

 Author:  jinzhu.yin<smeoa@qq.com>

 Support: https://git.oschina.net/smeoa/xiaowei
 --------------------------------------------------------------*/

namespace Home\Controller;

class UserFolderController extends HomeController {
	function _search_filter(&$map) {
		$map['name'] = array('like', "%" . $_POST['name'] . "%");
		$map['is_del'] = array('eq', '0');
	}

	function index() {
		$model = D("UserFolder");
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

		$model = D("UserFolder");
		$folder_list = $model -> get_folder_list();
		$this -> assign("folder_list", $folder_list);

		$tree = list_to_tree($folder_list);
		$this -> assign('menu', sub_tree_menu($tree));

		$this -> display('UserFolder:index');

	}

	protected function _insert($name = CONTROLLER_NAME) {
		$model = D("UserFolder");
		if (false === $model -> create()) {
			$this -> error($model -> getError());
		}

		$model -> folder = $name;

		//保存当前数据对象
		$list = $model -> add();
		if ($list !== false) {//保存成功.
			$this -> assign('jumpUrl', get_return_url());
			$this -> success('新增成功!');
		} else {
			//失败提示
			$this -> error('新增失败!');
		}
	}

	protected function _update($name = CONTROLLER_NAME) {
		$model = D("UserFolder");
		if (false === $model -> create()) {
			$this -> error($model -> getError());
		}
		// 更新数据
		$list = $model -> save();
		if (false !== $list) {
			//成功提示
			$this -> assign('jumpUrl', get_return_url());
			$this -> success('编辑成功!');
		} else {
			//错误提示
			$this -> error('编辑失败!');
		}
	}

	function read($id) {
		$model = M("UserFolder");
		$data = $model -> getById($id);
		if ($data !== false) {// 读取成功
			if ($data['user_id'] == get_user_id()) {
				$data['data'] = $data;
				$this -> ajaxReturn($data);
			}
			$this -> ajaxReturn("", "", 0);
		}
	}

	function _del($id, $name = CONTROLLER_NAME, $return_flag = false) {
		$model = D("UserFolder");
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

	function winpop() {
		$node = M("UserFolder");
		$menu = array();
		$where['folder'] = CONTROLLER_NAME;
		$where['is_del'] = 0;
		$where['user_id'] = get_user_id();

		$menu = $node -> where($where) -> field('id,pid,name') -> order('sort asc') -> select();
		$tree = list_to_tree($menu);
		$this -> assign('menu', popup_tree_menu($tree));
		$this -> display("UserFolder:winpop");
	}

}
