<?php

/*
 * Copyright (C) xiuno.com
 */

class thread_top extends base_model {
	
	function __construct(&$conf) {
		parent::__construct($conf);
	}
	
	// 保存一级/二级置顶，合并到 $forum
	public function add_top_1($forum, $fidtidarr) {
		$tidkeys = $this->tidarr_to_fidtid($fidtidarr);
		$forum['toptids'] = misc::key_str_merge($forum['toptids'], $tidkeys);
		$this->forum->xupdate($forum);
		
		// 更新到 $thread
		$this->update_thread_top($fidtidarr, 1);
		
		// hook thread_top_construct_end.php
	}
	
	/*
		$fidtidarr 格式: array('123-12345', '123-23456')
	*/
	public function delete_top_1($forum, $fidtidarr) {
		$tidkeys = $this->tidarr_to_fidtid($fidtidarr);
		$forum['toptids'] = misc::key_str_strip($forum['toptids'], $tidkeys);
		$this->forum->xupdate($forum);
		
		// 更新到 $thread
		$this->update_thread_top($fidtidarr, 0);
	}

	// $tidkeys 
	public function add_top_3($fidtidarr) {
		$tidkeys = $this->tidarr_to_fidtid($fidtidarr);
		$toptids = misc::key_str_merge($this->conf['toptids'], $tidkeys);
		$this->kv->set('toptids', $toptids);
		$this->runtime->xset('toptids', $toptids);
		
		// 更新到 $thread
		$this->update_thread_top($fidtidarr, 3);
	}
	
	public function delete_top_3($fidtidarr) {
		$tidkeys = $this->tidarr_to_fidtid($fidtidarr);
		$toptids = misc::key_str_strip($this->conf['toptids'], $tidkeys);
		$this->kv->set('toptids', $toptids);
		$this->runtime->xset('toptids', $toptids);
		
		// 更新到 $thread
		$this->update_thread_top($fidtidarr, 0);
	}
	
	// 删除某个版块下的所有三级置顶数据
	public function delete_top_3_by_fid($fid) {
		$toptids = trim($this->conf['toptids']);
		if(empty($toptids)) return;
		$toptidarr = explode(' ', $toptids);
		foreach($toptidarr as $v) {
			list($_fid, $_tid) = explode('-', $v);
			if(empty($_fid) || empty($_tid)) continue;
			if($fid == $_fid) {
				$toptids = misc::key_str_strip($toptids, $v);
			}
		}
		$this->kv->set('toptids', $toptids);
		$this->runtime->xset('toptids', $toptids);
	}
	
	// 获取 三级置顶的 fid, tid，全表扫描！还好是定长表，仅仅在mysql重启以后需要，节约一个索引。
	public function get_top_3_fid_tid() {
		$threadlist = $this->thread->index_fetch(array('top'=>3), array(), 0, 100);
		$tidkeys = '';
		foreach($threadlist as $thread) {
			$tidkeys .= " $thread[fid]-$thread[tid]";
		}
		$tidkeys = trim($tidkeys);
		return $tidkeys;
	}
	
	// 删除版块的时候，清理2级，3级的置顶帖子
	public function clear_top_by_fid($fid) {
	
	}
	
	private function update_thread_top($fidtidarr, $top) {
		// 更新到 $thread
		foreach($fidtidarr as $v) {
			$fid = $v[0];
			$tid = $v[1];
			$thread = $this->thread->read($fid, $tid);
			if(!empty($thread)) {
				$thread['top'] = $top;
				$this->thread->update($thread);
			}
		}
	}
	
	// 返回格式 "123-12345 123-12346 123-12347"
	private function tidarr_to_fidtid($fidtidarr) {
		$fidtids = '';
		foreach($fidtidarr as $v) {
			$fidtids .= " $v[0]-$v[1]";
		}
		return $fidtids;
	}
	
}
?>