<?php
/*--------------------------------------------------------------------
 小微OA系统 - 让工作更轻松快乐

 Copyright (c) 2013 http://www.smeoa.com All rights reserved. 
 
 Author:  jinzhu.yin<smeoa@qq.com>

 Support: https://git.oschina.net/smeoa/xiaowei
--------------------------------------------------------------*/

namespace Home\Controller;

class WorkLogController extends HomeController {
	protected $config = array('app_type' => 'common');
	//过滤查询字段
	function _search_filter(&$map) {
		$map['is_del'] = array('eq', '0');
		if (!empty($_POST['keyword'])) {
			$where['content'] = array('like', '%' . $_POST['keyword'] . '%');
			$where['plan'] = array('like', '%' . $_POST['keyword'] . '%');
			$where['_logic'] = 'or';
			$map['_complex'] = $where;
		}
	}

	public function index() {
		$plugin['date'] = true;
		$plugin['uploader'] = true;
		$plugin['editor'] = true;
		$this -> assign("plugin", $plugin);
		$this -> assign('user_id', get_user_id());

		$auth = $this -> config['auth'];
		$this -> assign('auth', $auth);
		if ($auth['admin']) {
			$node = D("Dept");
			$dept_id = get_dept_id();
			$dept_name = get_dept_name();
			
			$menu = array();
			$dept_menu = $node -> field('id,pid,name') -> where("is_del=0") -> order('sort asc') -> select();
			$dept_tree = list_to_tree($dept_menu, $dept_id);
			$count = count($dept_tree);
			if (empty($count)) {
				/*获取部门列表*/
				$html = '';
				$html = $html . "<option value='{$dept_id}'>{$dept_name}</option>";
				$this -> assign('dept_list', $html);
				/*获取人员列表*/
				$where['dept_id'] = array('eq', $dept_id);
				$emp_list = D("User") -> where($where) -> getField('id,name');
				$this -> assign('emp_list', $emp_list);
			} else {
				/*获取部门列表*/
				$this -> assign('dept_list', select_tree_menu($dept_tree));
				$dept_list = tree_to_list($dept_tree);
				$dept_list = rotate($dept_list);
				$dept_list = $dept_list['id'];

				/*获取人员列表*/
				$where['dept_id'] = array('in', $dept_list);
				$emp_list = D("User") -> where($where) -> getField('id,name');
				$this -> assign('emp_list', $emp_list);
			}
		}

		$model = D("WorkLogView");
		$map = $this -> _search($model);
		if ($auth['admin']) {
			if (empty($map['dept_id'])) {
				if (!empty($dept_list)) {
					$map['dept_id'] = array('in', array_merge($dept_list, array($dept_id)));
				} else {
					$map['dept_id'] = array('eq', $dept_id);
				}
			}
		} else {
			$map['user_id'] = get_user_id();
		}

		if (method_exists($this, '_search_filter')) {
			$this -> _search_filter($map);
		}

		if (!empty($model)) {
			$this -> _list($model, $map);
		}
		$this -> display();
	}

	function edit($id) {
		$plugin['date'] = true;
		$plugin['uploader'] = true;
		$plugin['editor'] = true;
		$this -> assign("plugin", $plugin);

		$this -> _edit($id);
	}

	public function add() {
		$plugin['date'] = true;
		$plugin['uploader'] = true;
		$plugin['editor'] = true;
		$this -> assign("plugin", $plugin);
		$this -> display();
	}
	
	function upload() {
		$this -> _upload();
	}

	function down($attach_id) {
		$this -> _down($attach_id);
	}

	/** 插入新新数据  **/
	protected function _insert($name="WorkLog") {		
		$model = D($name);
		if (false === $model -> create()) {
			$this -> error($model -> getError());
		}
		if (in_array('user_id', $model -> getDbFields())) {
			$model -> user_id = get_user_id();
		};
		if (in_array('user_name', $model -> getDbFields())) {
			$model -> user_name = get_user_name();
		};
		if (in_array('dept_id', $model -> getDbFields())) {
			$model -> dept_id = get_dept_id();
		};
		if (in_array('dept_name', $model -> getDbFields())) {
			$model -> dept_name = get_dept_name();
		};
		$model -> create_time = time();
		/*保存当前数据对象 */
		$list = $model -> add();
		if ($list !== false) {//保存成功
			$this -> assign('jumpUrl', get_return_url());
			$this -> success('新增成功!');
		} else {
			$this -> error('新增失败!');
			//失败提示
		}
	}

}
