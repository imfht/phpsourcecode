<?php

/*
 * Copyright (C) xiuno.com
 */

class mypost extends base_model {
	
	function __construct(&$conf) {
		parent::__construct($conf);
		$this->table = 'mypost';
		$this->primarykey = array('uid', 'fid', 'pid');
		
		// hook mypost_construct_end.php
		
	}

	public function read_by_tid($uid, $fid, $tid) {
		$mypost = $this->index_fetch(array('uid'=>$uid, 'fid'=>$fid, 'tid'=>$tid), array(), 0, 1);
		return count($mypost) > 0 ? array_pop($mypost) : array();
	}
	
	// 判断某个 tid 是否已经被加入过了
	public function have_tid($uid, $fid, $tid) {
		$havetid = $this->index_fetch(array('uid'=>$uid, 'fid'=>$fid, 'tid'=>$tid), array(), 0, 1);
		return count($havetid);
	}

	public function get_list_by_uid($uid, $page = 1, $pagesize = 30) {
		$mypostlist = $this->index_fetch(array('uid'=>$uid), array('pid'=>-1), ($page - 1) * $pagesize, $pagesize);
		return $mypostlist;
	}
	
	// 获取最后一个发帖
	public function get_last_post($uid) {
		$post = array();
		$mypostlist = $this->get_list_by_uid($uid, 1, 1);
		if(!empty($mypostlist)) {
			$mypost = array_pop($mypostlist);
			$post = $this->post->read($mypost['fid'], $mypost['pid']);
		}
		return $post;
	}
	
	/*
	foreach($mypostlist as &$mypost) {
		$this->format($mypost);
	}
	*/
	public function format(&$mypost) {
		$mypost['thread'] = $mypost['post'] = array();
		$thread = $this->thread->read($mypost['fid'], $mypost['tid']);
		if(empty($thread)) return;
		$post = $this->post->read($mypost['fid'], $mypost['pid']);
		if(empty($post)) return;
		$forum = $this->forum->read($thread['fid']);
		$this->thread->format($thread, $forum);
		$this->post->format($post);
		$mypost['thread'] = $thread;
		$mypost['post'] = $post;
	}
}
?>