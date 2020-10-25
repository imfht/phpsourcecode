<?php
/*--------------------------------------------------------------------
  小微OA系统 - 让工作更轻松快乐 

  Copyright (c) 2013 http://www.smeoa.com All rights reserved.                                             

   

  Author:  jinzhu.yin<smeoa@qq.com>                         

  Support: https://git.oschina.net/smeoa/xiaowei               
--------------------------------------------------------------*/

namespace Home\Controller;

class UserTagController extends HomeController {
	protected $config=array('app_type'=>'asst');
		
	function _search_filter(&$map) {
		$keyword=I('keyword');
		if (!empty($keyword)) {
			$map['code|name'] = array('like', "%" . $keyword . "%");
		}
	}

	public function index() {
		if ($_POST){
			$opmode = $_POST["opmode"];
			$model = D("UserTag");
			if (false === $model -> create()) {
				$this -> error($model -> getError());
			}
			if ($opmode == "add") {
				$model -> controller = CONTROLLER_NAME;
				$model->user_id=get_user_id();
				$list = $model -> add();
			}
			if ($opmode == "edit") {
				$model->user_id=get_user_id();
				$list = $model -> save();
			}
			if ($opmode == "del") {
				$model->user_id=get_user_id();
				$tag_id = $model -> id;
				$model -> del_tag($tag_id);
			}
		}
		
		$model = D("UserTag");
		$tag_list = $model -> get_tag_list("id,pid,name");
		$tree = list_to_tree($tag_list);
		$this -> assign('menu', sub_tree_menu($tree));

		$tag_list = $model -> get_tag_list();
		$this -> assign("tag_list", $tag_list);
		$this -> assign('js_file',"UserTag:js/index");
		$this -> display('UserTag:index');
	}

	function winpop() {
		$model = M("UserTag");
		$controller = $_GET['controller'];
		$where['controller'] = array('eq', $controller);
		$menu = $model -> where($where) -> field('id,pid,name') -> order('sort asc') -> select();
		$tree = list_to_tree($menu);
		$this -> assign('menu', popup_tree_menu($tree));
		$this -> display();
	}

	function popup() {
		$model = M("UserTag");
		$controller = $_GET['controller'];
		$where['controller'] = array('eq', $controller);
		$list = array();
		$list = $model -> where($where) -> field('id,pid,name') -> order('sort asc') -> select();
		$list = list_to_tree($list);
		$this -> assign('list_popup', sub_tree_menu($list));
		$this -> display();
		return;
	}
}
?>