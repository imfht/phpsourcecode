<?php

/*
 * Copyright (C) xiuno.com
 */

/*
	thread_type 功能中的 data count, 用来计数，翻页，一般被 type_data 调用。
*/

class thread_type_count extends base_model {

	function __construct(&$conf) {
		parent::__construct($conf);
		$this->table = 'thread_type_count';
		$this->primarykey = array('fid', 'typeidsum');
		
		// hook thread_type_count_construct_end.php
	}
	
	public function xcreate($fid, $typeidsum) {
		$arr = array(
			'fid'=>$fid,
			'typeidsum'=>$typeidsum,
			'threads'=>0,
		);
		return $this->create($arr);
	}
	
	public function inc($fid, $typeidsum) {
		$arr = $this->read($fid, $typeidsum);
		if(empty($arr)) {
			$this->xcreate($fid, $typeidsum);
			$arr = $this->read($fid, $typeidsum);
		}
		$arr['threads']++;
		return $this->update($arr);
	}
	
	public function dec($fid, $typeidsum) {
		$arr = $this->read($fid, $typeidsum);
		if(empty($arr)) {
			$this->xcreate($fid, $typeidsum);
			$arr = $this->read($fid, $typeidsum);
		}
		$arr['threads']--;
		return $this->update($arr);
	}
	
	public function delete_by_fid($fid) {
		$this->index_delete(array('fid'=>$fid));
	}
	
	public function get_threads($fid, $typeidsum) {
		$arr =  $this->read($fid, $typeidsum);
		return empty($arr) ? 0 : $arr['threads'];
	}
}
?>