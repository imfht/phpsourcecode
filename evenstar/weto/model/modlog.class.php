<?php

/*
 * Copyright (C) xiuno.com
 */

class modlog extends base_model {
	
	function __construct(&$conf) {
		parent::__construct($conf);
		$this->table = 'modlog';
		$this->primarykey = array('logid');
		$this->maxcol = 'logid';
		
		// hook modlog_construct_end.php
	}
	
	public function delete_by_fid($fid) {
		$this->index_delete(array('fid'=>$fid));
	}
	
	public function delete_by_fid_tid($fid, $tid) {
		$modloglist = $this->get_list_by_fid_tid($fid, $tid);
		foreach($modloglist as &$modlog) {
			$this->delete($modlog['logid']);
		}
	}
	
	// 某个版主的管理操作。
	public function get_list_by_uid($uid, $page, $pagesize) {
		$start = ($page - 1) * $pagesize;
		$modloglist = $this->index_fetch(array('uid'=>$uid), array('logid'=>0), $start, $pagesize);
		foreach($modloglist as &$modlog) {
			$this->format($modlog);
			$modlog['thread'] = $this->thread->read($modlog['fid'], $modlog['tid']);
		}
		return $modloglist;
	}
	
	// 返回某个主题下的操作日志。
	public function get_list_by_fid_tid($fid, $tid) {
		$modloglist = $this->index_fetch(array('fid'=>$fid, 'tid'=>$tid), array(), 0, 256);
		misc::arrlist_multisort($modloglist, 'logid', TRUE);
		return $modloglist;
	}
	
	// 用来显示给用户
	public function format(&$modlog) {
		$arr = array('digest'=>'加精华', 'undigest'=>'取消精华', 'top'=>'置顶', 'untop'=>'取消置顶', 'delete'=>'删除', 'move'=>'移动', 'type'=>'主题分类', 'rate'=>'评分');
		// hook modlog_model_format_after.php
		$modlog['action_fmt'] = $arr[$modlog['action']];
		$modlog['credits_fmt'] = $modlog['credits'] ? ($modlog['credits'] > 0 ? '+' : '&nbsp;').$modlog['credits'] : '---';
		$modlog['golds_fmt'] = $modlog['golds'] ? ($modlog['golds'] > 0 ? '+' : '&nbsp;').$modlog['golds'] : '---';
		$modlog['dateline_fmt'] = misc::humandate($modlog['dateline']);
		$modlog['forumname'] = $this->conf['forumarr'][$modlog['fid']];
	}
	
	// hook modlog_model_end.php
}
?>