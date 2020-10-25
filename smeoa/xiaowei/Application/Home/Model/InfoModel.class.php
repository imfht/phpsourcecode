<?php
/*---------------------------------------------------------------------------
 小微OA系统 - 让工作更轻松快乐

 Copyright (c) 2013 http://www.smeoa.com All rights reserved.

 Author:  jinzhu.yin<smeoa@qq.com>

 Support: https://git.oschina.net/smeoa/xiaowei
 -------------------------------------------------------------------------*/

namespace Home\Model;
use Think\Model;

class  InfoModel extends CommonModel {
	// 自动验证设置
	protected $_validate = array( array('name', 'require', '标题必须', 1), array('content', 'require', '内容必须'), );

	function _before_insert(&$data, $options) {
		$sql = "SELECT CONCAT(year(now()),'-',LPAD(count(*)+1,4,0)) doc_no FROM `" . $this -> tablePrefix . "info` WHERE 1 and year(FROM_UNIXTIME(create_time))>=year(now())";
		$rs = $this -> db -> query($sql);
		if ($rs) {
			$data['doc_no'] = $rs[0]['doc_no'];
		} else {
			$data['doc_no'] = date('Y') . "-0001";
		}
	}

	function _after_insert($data, $options) {
		$id = $data['id'];
		$scope_user_id = $data['scope_user_id'];
		$this -> _save_scope($id, $scope_user_id);
	}

	function _after_update($data, $options) {
		$is_sign = I('is_sign');
		if (empty($is_sign)) {
			$data['is_sign'] = 0;
		}

		$id = $data['id'];
		$scope_user_id = $data['scope_user_id'];
		$this -> _del_scope($id);
		$this -> _save_scope($id, $scope_user_id);
	}

	private function _del_scope($id) {
		$where['info_id'] = array('eq', $id);

		$model = M("InfoScope");
		$result = $model -> where($where) -> delete();

		if ($result === false) {
			return false;
		} else {
			return true;
		}
	}

	private function _save_scope($id, $user_list) {
		$user_list = array_filter(explode(",", $user_list));
		$return_user_list = array();
		foreach ($user_list as $val) {
			if (strpos($val, "dept_") !== false) {
				$dept_id = str_replace("dept_", '', $val);
				$dept_user = $this -> _get_user_list_by_dept_id($dept_id);
				if ($dept_user) {
					$return_user_list = array_merge($return_user_list, $dept_user);
				}
			} else {
				$return_user_list[] = $val;
			}
		}
		if (!empty($return_user_list)) {

			$info = D('InfoView') -> find($id);
			$push_data['type'] = '信息';
			$push_data['action'] = $info['folder_name'];
			$push_data['title'] = $info['name'];
			$push_data['content'] = del_html_tag($info['content']);
			$push_data['url'] = U('Info/read',"id={$id}&return_url=Info/index");
			
			send_push($push_data, $return_user_list);
						
			$return_user_list = implode(",", $return_user_list);
			$where = 'a.id in (' . $return_user_list . ') and b.id=\'' . $id . '\'';
			$sql = 'insert into ' . $this -> tablePrefix . 'info_scope (user_id,info_id) ';
			$sql .= ' select a.id, b.id from ' . $this -> tablePrefix . 'user a, ' . $this -> tablePrefix . 'info b where ' . $where;
			$result = $this -> execute($sql);

			if ($result === false) {
				return false;
			} else {
				return true;
			}

		} else {
			return true;
		}
	}

	private function _get_user_list_by_dept_id($id) {

		$dept = tree_to_list(list_to_tree( M("Dept") -> where('is_del=0') -> select(), $id));
		$dept = rotate($dept);

		if (empty($dept)) {
			$dept = array($id);
		} else {
			$dept = explode(",", implode(",", $dept['id']) . ",$id");
		}

		$model = M("User");
		$where['dept_id'] = array('in', $dept);
		$where['is_del'] = array('eq', 0);

		$data = $model -> where($where) -> getField('id', true);
		return $data;
	}

}
?>