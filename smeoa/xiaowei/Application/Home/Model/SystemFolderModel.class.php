<?php
/*---------------------------------------------------------------------------
 小微OA系统 - 让工作更轻松快乐

 Copyright (c) 2013 http://www.smeoa.com All rights reserved.

 Author:  jinzhu.yin<smeoa@qq.com>

 Support: https://git.oschina.net/smeoa/xiaowei
 -------------------------------------------------------------------------*/

namespace Home\Model;
use Think\Model;

class  SystemFolderModel extends CommonModel {

	function get_folder_list($controller = CONTROLLER_NAME, $field = 'id,name,pid,sort') {
		$where['controller'] = $controller;
		$where['is_del'] = 0;
		$list = $this -> where($where) -> order("sort") -> Field($field) -> select();

		return $list;
	}

	function get_folder_name($id) {
		$where['id'] = array('eq', $id);
		return $this -> where($where) -> getField("name");
	}

	function get_folder_menu() {
		$sql = "SELECT";
		$sql .= "  'badge_count_system_folder'    badge_function,";
		$sql .= "  a.id                           fid,";
		$sql .= "  a.sort,";
		$sql .= "  CONCAT('sfid_',a.id)        AS id,";
		$sql .= "  a.name,";
		$sql .= "  a.controller,";
		$sql .= "  a.sort,";
		$sql .= "  CONCAT('sfid_',a.pid)       AS pid,";
		$sql .= "  CONCAT(a.controller,'/folder?fid=',a.id) AS url";
		$sql .= " FROM {$this->trueTableName}  AS a";
		$sql .= " WHERE is_del = 0";
		$sql .= " ORDER BY a.controller,a.sort asc";
		$list = $this -> db -> query($sql);

		$data = array();
		foreach ($list as $key => $val) {
			if ($val["pid"] == 'sfid_0') {
				$where['sub_folder'] = $val['controller'] . "Folder";
				$pid = M("Node") -> where($where) -> getField('id');
				$val["pid"] = $pid;
			}
			$data[$key] = $val;
		}
		return $data;
	}

	function get_authed_folder($controller = CONTROLLER_NAME) {
		$folder_list = array();
		$where['controller'] = array('eq', $controller);
		$list = $this -> where($where) -> getField('id', true);
		if ($list) {
			foreach ($list as $key => $val) {
				$auth = $this -> get_folder_auth($val);
				if ($auth['read']) {
					$folder_list[] = $val;
				}
			}
		}
		return $folder_list;
	}

	function del_folder($id) {
		$model = M("SystemFolder");
		$sub_folder_list = tree_to_list(list_to_tree($this -> get_folder_list(), $id));
		$folder_list = rotate($folder_list);

		$folder_list = implode(",", $folder_list['id']) . ",$id";
		$where['id'] = array('in', $folder_list);
		$this -> where($where) -> delete();
	}

	function get_folder_auth($id) {
		$where['id'] = array('eq', $id);
		$auth_list = M("SystemFolder") -> where($where) -> Field('admin,write,read') -> find();
		$result = array_map(array($this, "_check_auth"), $auth_list);
		if ($result['admin'] == true) {
			$result['write'] = true;
		}
		if ($result['write'] == true) {
			$result['read'] = true;
		}
		//dump($result);
		return $result;
	}

	private function _check_auth($auth_list) {
		$arrtmp = array_filter(explode(';', $auth_list));
		foreach ($arrtmp as $item) {
			if (stripos($item, "dept_") !== false) {
				$arr_dept = explode('|', $item);
				$dept_id = substr($arr_dept[1], 5);
				$emp_list = $this -> get_emp_list_by_dept_id($dept_id);
				$emp_list = rotate($emp_list);
				$emp_list = $emp_list['emp_no'];

				//dump($emp_list);
				if (in_array(get_emp_no(), $emp_list)) {
					return true;
				}
			} else {
				if (stripos($item, get_emp_no()) !== false) {
					return true;
				}
			}
		}
		return false;
	}

	private function get_emp_list_by_dept_id($id) {

		$list = M("Dept") -> where('is_del=0') -> select();

		if (!empty($list)) {
			$dept = tree_to_list(list_to_tree($list, $id));
			$dept = rotate($dept);
			if (!empty($dept)) {
				$dept = $dept['id'];
				$dept = implode(",", $dept) . ",$id";
				$where['dept_id'] = array('in', $dept);
			} else {
				$where['dept_id'] = array('eq', $id);
			}
		}
		$model = M("User");
		$data = $model -> where($where) -> select();
		return $data;
	}

}
?>