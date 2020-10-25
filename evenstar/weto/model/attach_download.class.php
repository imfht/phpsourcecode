<?php

/*
 * Copyright (C) xiuno.com
 */

class attach_download extends base_model {
	
	function __construct(&$conf) {
		parent::__construct($conf);
		$this->table = 'attach_download';
		$this->primarykey = array('uid', 'fid', 'aid');
		
		// hook attach_download_construct_end.php
	}

	public function get_list_by_fid_aid($fid, $aid, $page = 1, $pagesize = 20) {
		$start = ($page -1) * $pagesize;
		$downlist = $this->index_fetch(array('fid'=>$fid, 'aid'=>$aid), array(), $start, $pagesize);
		foreach($downlist as &$down) {
			$this->format($down);
		}
		misc::arrlist_multisort($downlist, 'aid', FALSE);
		return $downlist;
	}
	
	public function get_list_by_uid($uid, $page = 1, $pagesize = 20) {
		$start = ($page -1) * $pagesize;
		$downlist = $this->index_fetch(array('uid'=>$uid), array(), $start, $pagesize);
		foreach($downlist as &$down) {
			$this->format($down);
		}
		misc::arrlist_multisort($downlist, 'aid', FALSE);
		return $downlist;
	}
	
	public function get_list_by_uploaduid($uploaduid, $page = 1, $pagesize = 20) {
		$start = ($page -1) * $pagesize;
		$downlist = $this->index_fetch(array('uploaduid'=>$uploaduid), array('dateline'=>0), $start, $pagesize);
		foreach($downlist as &$down) {
			$this->format($down);
		}
		misc::arrlist_multisort($downlist, 'dateline', TRUE);
		return $downlist;
	}
	
	// 删除附件的时候删除
	public function delete_by_fid_aid($fid, $aid) {
		return $this->index_delete(array('fid'=>$fid, 'aid'=>$aid));
	}
	
	// 用来显示给用户
	public function format(&$down) {
		// format data here.
		$down['attach'] = $this->attach->read($down['fid'], $down['aid']);
		$down['user'] = $this->user->read($down['uid']);
		$down['dateline_fmt'] = misc::humandate($down['dateline']);
		$this->attach->format($down['attach']);
		
		// hook attach_download_model_format_end.php
	}

}
?>