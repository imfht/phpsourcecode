<?php
/*---------------------------------------------------------------------------
 小微OA系统 - 让工作更轻松快乐

 Copyright (c) 2013 http://www.smeoa.com All rights reserved.

 Author:  jinzhu.yin<smeoa@qq.com>

 Support: https://git.oschina.net/smeoa/xiaowei
 -------------------------------------------------------------------------*/

namespace Home\Model;
use Think\Model;

class  UserFolderModel extends CommonModel {

	function get_folder_list($controller = CONTROLLER_NAME, $field = 'id,name,pid,sort') {
		$where['controller'] = $controller;
		$where['user_id'] = get_user_id();
		$where['is_del'] = 0;
		$list = $this -> where($where) -> order("sort") -> Field($field) -> select();		
		return $list;
	}

	public function get_folder_name($folder_id) {
		$where['id'] = array('eq', $folder_id);
		return $this -> where($where) -> getField("name");
	}

	public function get_folder_menu() {
		$user_id = get_user_id();

		$sql = "SELECT";
		$sql .= "  'badge_count_user_folder'    badge_function,";
		$sql .= "  a.id                           fid,";
		$sql .= "  a.sort,";
		$sql .= "  CONCAT('ufid_',a.id)        AS id,";
		$sql .= "  a.name,";
		$sql .= "  a.controller,";
		$sql .= "  a.sort,";
		$sql .= "  CONCAT('ufid_',a.pid)       AS pid,";
		$sql .= "  CONCAT(a.controller,'/folder?fid=',a.id) AS url";
		$sql .= " FROM {$this->trueTableName}  AS a";
		$sql .= " WHERE is_del = 0 and user_id={$user_id}";
		$sql .= " ORDER BY a.controller,a.sort asc";
		$list = $this -> db -> query($sql);

		$data = array();
		foreach ($list as $val) {
			if ($val["pid"] == 'ufid_0') {
				$where['sub_folder'] = $val['controller'] . "Folder";
				$pid = M("Node") -> where($where) -> getField('id');
				$val["pid"] = $pid;
			}
			$data[] = $val;
		}
		return $data;
	}

	public function _get_folder_auth($folder_id) {
		return array('admin' => true, "write" => true, "read" => true);
	}
}
?>