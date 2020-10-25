<?php

/*
 * Copyright (C) xiuno.com
 */

class group extends base_model {
	
	function __construct(&$conf) {
		parent::__construct($conf);
		$this->table = 'group';
		$this->primarykey = array('groupid');
		$this->maxcol = 'groupid';
		
		// hook group_construct_end.php
	}
	
	// 取得注册用户的 groupid, 和0
	public function get_list() {
		$usergroup = array();
		//$usergroup[0] = $this->get(0);
		$usergroup = $this->index_fetch(array(), array('groupid'=>1), 0, 1000);
		misc::arrlist_change_key($usergroup, 'groupid');
		return $usergroup;
	}
	
	public function groupid_to_name($groupid) {
		$group = $this->read($groupid);
		return $group['name'];
	}

	// 获取 groupid=>name
	/*public function get_group_kv() {
		$group_kv = $this->kv->get('group_kv');
		if(empty($group_kv)) {
			$group_kv = misc::arrlist_key_values();
			$this->kv->set('group_kv', core::json_encode($group_kv));
		}
		return $group_kv;
	}*/
	
	public function get_groupid_by_credits($groupid, $credits) {
		// 根据用户组积分范围升级
		if($groupid > 10) {
			$grouplist = $this->get_list();
			foreach($grouplist as $group) {
				if($group['groupid'] < 11) continue;
				if($credits >= $group['creditsfrom'] && $credits < $group['creditsto']) {
					return $group['groupid'];
				}
			}
		}
		return $groupid;
	}
	
	public function check_name(&$name) {
		if(empty($name)) {
			return '用户组名称不能为空。';
		}
		return '';
	}
	
	public function check_creditsfrom(&$creditsfrom) {
		if(empty($creditsfrom)) {
			return '起始积分不能为空。';
		}
		return '';
	}
	
	public function check_creditsto(&$creditsto) {
		if(empty($creditsto)) {
			return '截止积分不能为空。';
		}
		return '';
	}
	
	// 用来显示给用户
	public function format(&$group) {
		// format data here.
	}
}
?>