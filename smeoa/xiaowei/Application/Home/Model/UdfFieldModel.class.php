<?php
/*---------------------------------------------------------------------------
 小微OA系统 - 让工作更轻松快乐

 Copyright (c) 2013 http://www.smeoa.com All rights reserved.

 Author:  jinzhu.yin<smeoa@qq.com>

 Support: https://git.oschina.net/smeoa/xiaowei
 -------------------------------------------------------------------------*/

namespace Home\Model;
use Think\Model;

class  UdfFieldModel extends CommonModel {
	public function get_field_list($row_type, $controller = CONTROLLER_NAME) {
		$where['row_type'] = array('eq', $row_type);
		$where['controller'] = $controller;
		$where['is_del'] = 0;
		$list = $this -> where($where) -> order('sort asc') -> select();
		return $list;
	}

	public function get_show_field($udf_data) {
		$field_data = json_decode($udf_data, true);
		$field_id = array_keys($field_data);

		$where_field['id'] = array('in', $field_id);
		$list_field = $this -> where($where_field) -> select();

		foreach ($list_field as $key => $field) {
			if (strpos($field['config'], 'show') !== false) {
				$return[] = $field;
			}
		}
		return $return;
	}

	public function get_data_list($udf_data) {
		if (!empty($udf_data)) {
			$field_data = json_decode($udf_data, true);
			$field_id = array_keys($field_data);

			$where_field['id'] = array('in', $field_id);
			$list_field = $this -> where($where_field) -> select();

			foreach ($list_field as $key => $field) {
				$val[$key] = $field;
				$val[$key]['val'] = $field_data[$field['id']];
			}
			return $val;
		}
	}

	public function get_field_name($udf_name) {
		if (!empty($udf_name)) {
			$field_data = json_decode($udf_name, true);
			$field_id = array_keys($field_data);

			$where_field['id'] = array('in', $field_id);
			$list_field = $this -> where($where_field) -> select();

			foreach ($list_field as $key => $field) {
				$val[$key] = $field["name"];

			}

			return $val;
		}
	}

	function get_field_data() {
		$udf_field = array_filter(array_keys($_REQUEST), array($this, 'filter'));
		if (!empty($udf_field)) {
			foreach ($udf_field as $field) {
				$tmp = array_filter(explode("_", $field));
				$val = $_REQUEST[$field];

				if (is_array($val)) {
					$val = implode("|", $val);
				}
				$field_data[$tmp[2]] = $val;
			}
			return json_encode($field_data, JSON_UNESCAPED_UNICODE);
		} else {
			return;
		}
	}

	function filter($val) {
		if (strpos($val, "udf_field") !== false) {
			return true;
		} else {
			return false;
		}
	}

}
?>