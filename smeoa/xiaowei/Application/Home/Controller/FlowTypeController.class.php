<?php
/*--------------------------------------------------------------------
 小微OA系统 - 让工作更轻松快乐

 Copyright (c) 2013 http://www.smeoa.com All rights reserved.

 Author:  jinzhu.yin<smeoa@qq.com>

 Support: https://git.oschina.net/smeoa/xiaowei
--------------------------------------------------------------*/

namespace Home\Controller;

class FlowTypeController extends HomeController {
	protected $config = array('app_type' => 'master');

	//过滤查询字段
	function _search_filter(&$map) {
		$keyword = I('keyword');
		if (!empty($keyword)) {
			$map['name'] = array('like', "%" . $keyword . "%");
		}
	}

	function add() {

		$plugin['editor'] = true;
		$this -> assign("plugin", $plugin);

		$this -> assign("user_id", get_user_id());
		$this -> _assign_tag_list();
		$this -> _assign_duty_list();
		$this -> display();
	}

	function index() {
		$model = D("FlowTypeView");
		$map = $this -> _search();
		if (method_exists($this, '_search_filter')) {
			$this -> _search_filter($map);
		}

		$list = $model -> where($map) -> order('tag,sort') -> select();
		$this -> assign('list', $list);
		$this -> _assign_tag_list();
		$this -> display();
		return;
	}

	function del($id) {
		$result = $this -> _destory($id);
	}

	function move_to($id, $val) {
		$model = D("SystemTag");
		$model -> del_data_by_row($id);
		$result = $model -> set_tag($id, $val);
		
		$field = 'tag';
		$result = $this -> _set_field($id, $field, $val);

		if ($result !== false) {
			$this -> assign('jumpUrl', get_return_url());
			$this -> success('操作成功!');
		} else {
			//失败提示
			$this -> error('操作失败!');
		}
	}

	protected function _assign_tag_list() {
		$model = D("SystemTag");
		$tag_list = $model -> get_tag_list('id,name');
		$this -> assign("tag_list", $tag_list);
	}

	protected function _assign_duty_list() {
		$model = D("Duty");
		$where['is_del'] = array('eq', 0);
		$duty_list = $model -> where($where) -> order('sort') -> getField("id,name");
		$this -> assign("duty_list", $duty_list);
	}

	function tag_manage() {
		$this -> _system_tag_manage("分组管理");
	}

	function edit($id) {
		$plugin['editor'] = true;
		$this -> assign("plugin", $plugin);

		$this -> assign("user_id", get_user_id());
		$model = D("FlowTypeView");

		$vo = $model -> getById($id);
		$this -> assign('vo', $vo);
		$this -> _assign_tag_list();
		$this -> _assign_duty_list();
		$this -> display();
	}

	function field() {
		$plugin['editor'] = true;
		$this -> assign("plugin", $plugin);

		if ($_POST) {
			$opmode = $_POST["opmode"];
			$model = D("FlowField");
			if (false === $model -> create()) {
				$this -> error($model -> getError());
			}
			if ($opmode == "add") {
				$list = $model -> add();
				if ($list !== false) {//保存成功
					$this -> assign('jumpUrl', get_return_url());
					$this -> success('新增成功!');
				} else {
					$this -> error('新增失败!');
					//失败提示
				}
			}
			if ($opmode == "edit") {
				$list = $model -> save();
				if ($list !== false) {//保存成功
					$this -> assign('jumpUrl', get_return_url());
					$this -> success('保存成功!');
				} else {
					$this -> error('保存失败!');
					//失败提示
				}
			}
			if ($opmode == "del") {
				$id = $_REQUEST['id'];
				$list = $model -> where("id=$id") -> delete();
				if ($list !== false) {//保存成功
					$this -> assign('jumpUrl', get_return_url());
					$this -> success('删除成功!');
				} else {
					$this -> error('删除失败!');
					//失败提示
				}
			}
		}

		$plugin['date'] = true;

		$this -> assign("plugin", $plugin);

		$model = D("FlowField");
		$type_id = $_REQUEST['type_id'];
		$this -> assign('type_id', $type_id);

		$where['type_id'] = array('eq', $type_id);
		$where['is_del'] = 0;

		$field_list = $model -> where($where) -> order('sort asc') -> select();

		$tree = list_to_tree($field_list);
		$this -> assign('menu', sub_tree_menu($tree));

		$this -> assign("field_list", $field_list);
		$this -> display();
	}

	function get_field() {
		$id = I('id');
		$model = M("FlowField");
		$vo = $model -> getById($id);
		if (IS_AJAX) {
			if ($vo !== false) {// 读取成功
				$data['data'] = $vo;
				$this -> ajaxReturn($data);
			} else {
				die ;
			}
		}
	}
	
	function upload(){
		$this->_upload();
	}
}
?>