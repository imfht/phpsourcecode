<?php

/*
 * Copyright (C) xiuno.com
 */

class rate extends base_model {
	
	function __construct(&$conf) {
		parent::__construct($conf);
		$this->table = 'rate';
		$this->primarykey = array('rateid');
		$this->maxcol = 'rateid';
		
		// hook rate_construct_end.php
	}
	
	// 删除评价
	public function delete_by_fid($fid) {
		// 最多支持 100000
		$keys = $this->index_fetch_id(array('fid'=>$fid), array(), 0, 100000);
		foreach($keys as $key) {
			$this->delete($key);
		}
		return count($keys);
	}
	
	// 删除评价
	public function delete_by_fid_pid($fid, $pid) {
		$ratelist = $this->get_list_by_fid_pid($fid, $pid);
		foreach($ratelist as &$rate) {
			$this->delete($rate['rateid']);
		}
	}
	
	// 某个版主的管理操作，包含评分日志。
	public function get_list_by_uid($uid, $page, $pagesize) {
		$start = ($page - 1) * $pagesize;
		$ratelist = $this->index_fetch(array('uid'=>$uid), array('rateid'=>0), $start, $pagesize);
		foreach($ratelist as &$rate) {
			$this->format($rate);
		}
		return $ratelist;
	}
	
	// 返回某个帖子下的操作日志，包含评分日志。
	public function get_list_by_fid_pid($fid, $pid) {
		$ratelist = $this->index_fetch(array('fid'=>$fid, 'pid'=>$pid), array(), 0, 256);
		misc::arrlist_multisort($ratelist, 'rateid', TRUE);
		foreach($ratelist as &$rate) {
			$this->format($rate);
		}
		return $ratelist;
	}
	
	// 返回用户某天的评分
	public function get_today_rate_list($uid) {
		$ymd = date('Ymd', $_SERVER['time']);
		$ratelist = $this->index_fetch(array('uid'=>$uid, 'ymd'=>$ymd), array(), 0, 256);
		return $ratelist;
	}
	
	// 返回今日已经的发出去的评分
	public function get_today_credits_golds($uid) {
		$ratelist = $this->get_today_rate_list($uid);
		$credits = $golds = 0;
		foreach($ratelist as &$rate) {
			// 只统计正分，扣分不统计
			$rate['credits'] > 0 && $credits += $rate['credits'];
			$rate['golds'] > 0 && $golds += $rate['golds'];
		}
		return array($credits, $golds);
	}
	
	// 得到今日的评分，针对某个帖子
	public function get_today_rate_by_fid_pid_uid($fid, $pid, $uid) {
		$ratelist = $this->get_today_rate_list($uid);
		foreach($ratelist as $rate) {
			if($rate['fid'] == $fid && $rate['pid'] == $pid) {
				return $rate;
			}
		}
		return array();
	}
	
	// 用来显示给用户
	public function format(&$rate) {
		$rate['credits_fmt'] = $rate['credits'] ? ($rate['credits'] > 0 ? '+' : '').$rate['credits'] : '---';
		$rate['golds_fmt'] = $rate['golds'] ? ($rate['golds'] > 0 ? '+' : '').$rate['golds'] : '---';
		$rate['dateline_fmt'] = misc::humandate($rate['dateline']);
		$rate['forumname'] = $this->conf['forumarr'][$rate['fid']];
		$rate['user'] = $this->user->read($rate['uid']);
		$rate['thread'] = $this->thread->read($rate['fid'], $rate['tid']);
		$rate['post'] = $this->post->read($rate['fid'], $rate['pid']);
		$this->user->format($rate['user']);
	}
}
?>