<?php

/*
 * Copyright (C) xiuno.com
 */

class forum_access extends base_model {
	
	function __construct(&$conf) {
		parent::__construct($conf);
		$this->table = 'forum_access';
		$this->primarykey = array('fid', 'groupid');
		$this->maxcol = 'fid';
		
		// hook forum_access_construct_end.php
	}
	
	public function delete_by_fid($fid) {
		$accesslist = $this->get_list_by_fid($fid);
		foreach($accesslist as $access) {
			$this->delete($access['fid'], $access['groupid']);
		}
		return TRUE;
	}
	
	public function get_list_by_fid($fid) {
		$arr = array();
		$accesslist = $this->index_fetch(array('fid' => $fid), array('groupid'=>1), 0, 1000);
		foreach($accesslist as $v) {
			$arr[$v['groupid']] = $v;
		}
		return $arr;
	}
	
	// 获取开启了accesson 的版块
	public function get_accesson($forumarr) {
		$forumaccesson = array();
		foreach($forumarr as $fid=>$name) {
			$accesslist = $this->index_fetch(array('fid' => $fid), array(), 0, 1);
			!empty($accesslist) && $forumaccesson[$fid] = 1;
		}
		return $forumaccesson;
	}
	
	// 将游客调到最后一组
	/*
	public function judge_accesslist(&$accesslist) {
		list($access) = $accesslist;
		if($access['groupid'] == 0) {
			$access = array_shift($accesslist);
			array_push($accesslist, $access);
		}
	}
	*/
	
	// 用来显示给用户
	public function format(&$forum_access) {
		// format data here.
	}
}
?>