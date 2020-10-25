<?php
/*--------------------------------------------------------------------
  小微OA系统 - 让工作更轻松快乐 

  Copyright (c) 2013 http://www.smeoa.com All rights reserved.                                             

   

  Author:  jinzhu.yin<smeoa@qq.com>                         

  Support: https://git.oschina.net/smeoa/xiaowei               
--------------------------------------------------------------*/


namespace Home\Controller;

class TodoController extends HomeController {
	protected $config=array('app_type'=>'personal');
	//过滤查询字段
	function _search_filter(&$map) {
		$map['name'] = array('like', "%" . I('keyword') . "%");
	}

	public function index() {
		$user_id = get_user_id();
		$where['user_id'] = $user_id;
		$where['status'] = array("in", "1,2");
		$keyword=I('keyword');
		if (!empty($keyword)) {
			$where['name'] = array('like', "%" . $_POST["keyword"] . "%");
		}
		$list = M("Todo") -> where($where) -> order('priority desc,sort asc') -> select();
		$this -> assign("list", $list);

		$where['status'] = 3;
		$list2 = M("Todo") -> where($where) -> order('priority desc,sort asc') -> select();
		$this -> assign("list2", $list2);

		$this -> display();
		return;
	}

	public function upload() {
		$this->_upload();
	}

	function read($id) {
		$model = M('Todo');
		$list = $_REQUEST['list'];
		$this -> assign("list", $list);
		$list = array_filter(explode("|", $list));
		array_pop($list);
		$current = array_search($id, $list);
		if ($current !== false) {
			$next = $list[$current + 1];
			$prev = $list[$current - 1];
		}
		$this -> assign('next', $next);
		$this -> assign('prev', $prev);

		$where['id'] = $id;
		$where['user_id'] = get_user_id();
		$vo = $model -> where($where) -> find();
		$this -> assign('vo', $vo);
		$this -> display();
	}

	public function down($attach_id) {
		$this->_down($attach_id);
	}

	function del() {
		$id = I('id');
		$where['id'] = $id;
		$where['user_id'] = get_user_id();
		$result = M("Todo") -> where($where) -> delete();
		if ($result !== false) {//保存成功
			$this -> assign('jumpUrl', get_return_url());
			$this -> success('删除成功!');
		} else {
			//失败提示
			$this -> error('删除失败!');
		}
	}

	public function add() {
		$plugin['jquery-ui'] = true;			
		$plugin['date'] = true;					
		$plugin['uploader'] = true;
		$plugin['editor'] = true;
		$this -> assign("plugin", $plugin);		
		$this -> display();
	}

	public function edit($id) {
		$plugin['jquery-ui'] = true;				
		$plugin['date'] = true;						
		$plugin['uploader'] = true;
		$plugin['editor'] = true;
		$this -> assign("plugin", $plugin);			

		$this -> assign("time_list", $time_list);
		$model = M('Todo');
		$where['user_id'] = get_user_id();
		$where['id'] = $id;
		$vo = $model -> where($where) -> find();

		$vo['start_time'] = fix_time($vo['start_time']);
		$vo['end_time'] = fix_time($vo['end_time']);
		$this -> assign('vo', $vo);
		$this -> display();
	}

	public function set_sort() {
		$node = $_REQUEST['node'];
		$priority = $_REQUEST['priority'];
		$sort = $_REQUEST['sort'];

		$model = M("Todo");
		// 实例化User对象
		$where['user_id'] = get_user_id();
		foreach ($node as $key => $val) {
			$data = array('priority' => $priority[$key], 'sort' => $sort[$key]);
			$where['id'] = $val;
			$model -> where($where) -> setField($data);
		}
	}

	public function mark_status() {
		$id = I('id');
		$val = I('val');
		if ($val == 3) {
			$field = 'end_date';
			$date = date("Y-m-d");
			$model = M("Todo");
			$where['id'] = $id;
			$where['user_id'] = array('eq', get_user_id());
			$list = $model -> where($where) -> setField($field, $date);
		}
		$field = 'status';
		$result = $this -> _set_field($id, $field, $val);
		if ($result !== false) {//保存成功
			$this -> assign('jumpUrl', get_return_url());
			$this -> success('操作成功!');
		} else {
			//失败提示
			$this -> error('操作失败!');
		}
	}

	function json() {
		header("Cache-Control: no-cache, must-revalidate");
		header("Content-Type:text/html; charset=utf-8");
		$user_id = get_user_id();
		$start_date = $_REQUEST["start_date"];
		$end_date = $_REQUEST["end_date"];

		$where['user_id'] = $user_id;
		$where['start_date'] = array( array('gt', $start_date), array('lt', $end_date));
		$list = M("Todo") -> where($where) -> order('start_date,priority desc') -> select();
		exit(json_encode($list));
	}

}
?>