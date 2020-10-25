<?php
/*---------------------------------------------------------------------------
 小微OA系统 - 让工作更轻松快乐

 Copyright (c) 2013 http://www.smeoa.com All rights reserved.

 Author:  jinzhu.yin<smeoa@qq.com>

 Support: https://git.oschina.net/smeoa/xiaowei
 -------------------------------------------------------------------------*/

// 用户模型
namespace Home\Model;
use Think\Model;

class  SystemTagModel extends CommonModel {
	public function get_tag_list($field = "id,name", $controller = CONTROLLER_NAME) {
		$where['controller'] = $controller;
		$list = $this -> where($where) -> order('sort asc') -> getfield($field);
		return $list;
	}

	public function get_data_list($controller = CONTROLLER_NAME, $tag_id = null) {
		$model = M("SystemTagData");
		$where = "tag.controller='$controller'";
		if (!empty($tag_id)) {
			$where .= " and tag_id=$tag_id";
		}
		$join = 'join ' . $this -> tablePrefix . 'system_tag tag on tag_id=tag.id';
		$list = $model -> join($join) -> where($where) -> field("row_id,tag_id") -> select();
		return $list;
	}

	function del_data_by_row($row_list, $controller = CONTROLLER_NAME) {
		if (isset($row_list)) {
			if (is_array($row_list)) {
				$where['row_id'] = array("in", array_filter($row_list));
			} else {
				$where['row_id'] = array('in', array_filter(explode(',', $row_list)));
			}
			$model = M("SystemTagData");
			$where['controller'] = $controller;
			$result = $model -> where($where) -> delete();
		}
		return $result;
	}

	function del_tag($tag_id) {
		$model = M("SystemTag");
		$tag_list = tree_to_list(list_to_tree($this -> get_tag_list("id,pid,name"), $tag_id));
		$tag_list = rotate($tag_list);

		$tag_list = implode(",", $tag_list['id']) . ",$tag_id";
		$where['id'] = array('in', $tag_list);
		$this -> where($where) -> delete();
		$this -> _del_data_by_tag($tag_list);
	}

	function set_tag($row_list, $tag_list, $controller = CONTROLLER_NAME) {
		if (empty($row_list)) {
			return true;
		}
		if (empty($tag_list)) {
			return true;
		}
		if (is_array($row_list)) {
			$row_list = array_filter($row_list);
		} else {
			$row_list = explode(",", $row_list);
			$row_list = array_filter($row_list);
		}
		$row_list = implode(",", $row_list);
		if (is_array($tag_list)) {
			$tag_list = array_filter($tag_list);
		} else {
			$tag_list = explode(",", $tag_list);
			$tag_list = array_filter($tag_list);
		}
		$controller_table=M($controller)->trueTableName;
		$tag_list = implode(",", $tag_list);
		$where = 'a.id in (' . $row_list . ') AND b.id in(' . $tag_list . ')';
		$sql = 'INSERT INTO ' . $this -> tablePrefix . 'system_tag_data (row_id,controller,tag_id) SELECT a.id,b.controller,b.id ';
		$sql .= ' FROM ' . $controller_table  . ' a, ' . $this -> tablePrefix . 'system_tag b WHERE ' . $where;

		$result = $this -> execute($sql);
		if ($result === false) {
			return false;
		} else {
			return true;
		}
	}

	protected function _del_data_by_tag($tag_id) {
		if (isset($tag_id)) {
			if (is_array($tag_id)) {
				$where['tag_id'] = array("in", array_filter($tag_id));
			} else {
				$where['tag_id'] = array('in', array_filter(explode(',', $tag_id)));
			}
			$model = M("SystemTagData");
			$result = $model -> where($where) -> delete();
		}
		return $result;
	}
}
?>