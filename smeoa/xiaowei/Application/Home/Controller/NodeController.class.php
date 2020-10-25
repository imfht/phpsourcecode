<?php
/*--------------------------------------------------------------------
 小微OA系统 - 让工作更轻松快乐

 Copyright (c) 2013 http://www.smeoa.com All rights reserved.

 Author:  jinzhu.yin<smeoa@qq.com>

 Support: https://git.oschina.net/smeoa/xiaowei
--------------------------------------------------------------*/

namespace Home\Controller;

class NodeController extends HomeController {

	protected $config = array('app_type' => 'master','admin'=>'node');

	public function index() {
		$node = M("Node");
		if (!empty($_POST['eq_pid'])) {
			$eq_pid = $_POST['eq_pid'];
		} elseif (!empty($_GET['eq_pid'])) {
			$eq_pid = $_GET['eq_pid'];
		} else {
			$eq_pid = $node -> where('pid=0') -> order('sort asc') -> getField('id');
		}

		$this -> assign('eq_pid', $eq_pid);

		$list = $node -> where('pid=0') -> order('sort asc') -> getField('id,name');
		$this -> assign('groupList', $list);

		$menu = array();
		$menu = $node -> field('id,pid,name') -> order('sort asc') -> select();
		$tree = list_to_tree($menu, $eq_pid);

		$model = M("Node");
		$list = $model -> order('sort asc') -> getField('id,name');
		$this -> assign('node_list', $list);
		$this -> assign('menu', popup_tree_menu($tree));
		$this -> display();
	}

	protected function _insert($name='Node') {
		$model = D('Node');
		if (false === $model -> create()) {
			$this -> error($model -> getError());
		}
		if (strpos($model -> url, '##') !== false) {
			$model -> sub_folder = ucfirst(get_controller(str_replace("##", "", $model -> url))) . "Folder";
		} else {
			$model -> sub_folder = '';
		}
		//保存当前数据对象
		$list = $model -> add();
		if ($list !== false) {//保存成功
			$this -> assign('jumpUrl', get_return_url());
			$this -> success('新增成功!');
		} else {
			//失败提示
			$this -> error('新增失败!');
		}
	}

	protected function _update($name='Node'){
		$id = $_POST['id'];
		$model = D("Node");
		if (false === $model -> create()) {
			$this -> error($model -> getError());
		}
		if (strpos($model -> url, '##') !== false) {
			$model -> sub_folder = ucfirst(get_controller(str_replace("##", "", $model -> url))) . "Folder";
		} else {
			$model -> sub_folder = '';
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

	function winpop() {
		$menu = D("Node") -> order('sort asc') -> select();
		$tree = list_to_tree($menu);
		$this -> assign('menu', popup_tree_menu($tree));
		$this -> display();
	}

	function del($id){
		$where['pid']=array('eq',$id);
		$list=M("Node")->where($where)->select();
		
		if($list){
			$this->error('有子节点不能删除');
		}
		$model = M("RoleNode");
		$where['node_id'] = $id;
		$model -> where($where) -> delete();
		$this -> _destory($id);
	}

}
?>