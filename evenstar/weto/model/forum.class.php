<?php

/*
 * Copyright (C) xiuno.com
 */

class forum extends base_model {
	
	function __construct(&$conf) {
		parent::__construct($conf);
		$this->table = 'forum';
		$this->primarykey = array('fid');
		$this->maxcol = 'fid';
		
		// hook forum_construct_end.php
	}
	
	// 更新版块的最后发帖
	public function update_last($fid) {
		$forum = $this->read($fid);
		$threadlist = $this->thread->index_fetch(array('fid'=>$fid), array('tid'=>-1), 0, 1);
		if(empty($threadlist)) {
			$forum['lasttid'] = 0;
		} else {
			$thread = array_pop($threadlist);
			$forum['lasttid'] = $thread['tid'];
		}
		$this->update($forum);
	}
	
	// 取版块列表，二级
	public function get_list() {
		$forumlist = $this->index_fetch(array(), array('rank'=>1), 0, 1000);
		misc::arrlist_change_key($forumlist, 'fid');
		return $forumlist;
	}
	
	public function check_name(&$name) {
		if(empty($name)) {
			return '版块名称不能为空。';
		}
		return '';
	}
	
	public function check_rank(&$rank) {
		if(empty($rank)) {
			return '显示倒序不能为空。';
		}
		return '';
	}
	
	public function check_brief(&$brief) {
		if(empty($brief)) {
			return '版块简介不能为空。';
		}
		return '';
	}
	
	public function check_icon(&$icon) {
		if(empty($icon)) {
			return '版块图标不能为空。';
		}
		return '';
	}
	
	public function format(&$forum, $threadtype = FALSE) {
		// 版主
		$forum['modlist'] = array();
		if(!empty($forum['modids'])) {
			$modidarr = explode(' ', $forum['modids']);
			$modnamearr = explode(' ', $forum['modnames']);
			$forum['modlist'] = array_combine($modidarr, $modnamearr);
		}
		
		// hook forum_model_format_end.php
	}
	
	// 简洁格式，存入缓存，前台调用
	public function format_thread_type(&$forum) {
		$fid = $forum['fid'];
		$forum['typecates'] = array();
		$forum['typecates_mod'] = array(); // 只有版主有权限的主题分类
		$forum['types'] = array();
		$typecate = array();
		$types = array();
		for($i=1; $i<=4; $i++) {
			$cate = $this->thread_type_cate->xread($fid, $i, FALSE);
			if($cate && $cate['enable']) {
				$typecate[] = $cate;
			}
		}
		
		misc::arrlist_multisort($typecate, 'rank');
		foreach($typecate as $k=>$cate) {
			$forum['typecates'][$cate['cateid']] = $cate['catename'];
			($cate['enable'] & 2) && $forum['typecates_mod'][$cate['cateid']] = $cate['cateid'];
			$arrlist = $this->thread_type->get_list_by_fid_cateid($fid, $cate['cateid'], FALSE);
			foreach($arrlist as $k=>$v) {
				if(!$v['enable']) unset($arrlist[$k]);
			}
			$forum['types'][$cate['cateid']] = misc::arrlist_key_values($arrlist, 'typeid', 'typename');
		}
	}
	
	
	
	// 详细的格式化，填充, 后台调用
	public function format_thread_type_full(&$forum) {
		$fid = $forum['fid'];
		$forum['typecatelist'] = array();
		$forum['typelist'] = array();
		$typecatelist = array();
		for($i=1; $i<=4; $i++) {
			$typecatelist[$i] = $this->thread_type_cate->xread($fid, $i, TRUE); // 填充空白
		}
		misc::arrlist_multisort($typecatelist, 'rank');
		foreach($typecatelist as $typecate) {
			$forum['typecatelist'][$typecate['cateid']] = $typecate;
			$forum['typelist'][$typecate['cateid']] = $this->thread_type->get_list_by_fid_cateid($fid, $typecate['cateid'], TRUE); // 填充空白
		}
	}
	
	// 获取有权限管理的版块列表，默认第一个，如果有权限限制，则查询用户组权限，仅供斑竹列表使用
	public function get_options($uid, $groupid, $checkedfid, &$defaultfid) {
		$forumlist = $this->forum->get_list();
		$s = '';
		$checkedfid && $defaultfid = $checkedfid;
		foreach($forumlist as $forum) {
			if($groupid == 1 || $groupid == 2 || ($groupid == 4 && strpos(' '.$forum['modids'].' ', ' '.$uid.' ') !== FALSE)) {
				
				// 隐藏权限不足的版块。
				$fid = $forum['fid'];
				if(!isset($this->conf['forumarr'][$fid])) {
					continue;
				}
				
				empty($checkedfid) && empty($defaultfid) && $defaultfid = $fid;
				
				$checked = $checkedfid == $forum['fid'] ? ' selected="selected"' : '';
				$s .= '<option value="'.$forum['fid'].'"'.$checked.' style="font-weight: 800;">'.$forum['name'].'</option>';
			}
		}
		return $s;
	}
	
	// 获取版块列表
	public function get_public_options($checkfid = 0, $user = array()) {
		$s = '';
		$forumarr = $this->conf['forumarr'];
		foreach($forumarr as $fid=>$name) {
			if(!empty($forumarr[$fid])) {
				$access = $this->forum_access->read($fid, $user['groupid']);
				if(!empty($access) && !$access['allowread']) {
					unset($forumarr[$fid]);
					continue;
				}
			}
			$checked = $checkfid == $fid ? ' selected="selected"' : '';
			$s .= '<option value="'.$fid.'"'.$checked.'>'.$name.'</option>';
		}
		return $s;
	}
	
	public function xupdate($new) {
		$fid = $new['fid'];
		$forum = $this->read($fid);
		$cache = $this->mcache->read('forum', $fid);
		foreach($new as $k=>$v) {
			isset($forum[$k]) && !is_array($new[$k]) && $forum[$k] = $v;
			if($k == 'typecates' && !is_array($v)) continue; // todo: 此行代码为了兼容，正式版可以去掉了。
			isset($cache[$k]) && $cache[$k] = $v;
		}
		$this->update($forum);
		$this->mcache->real_set('forum', $fid, $cache);
	}
	
	// hook forum_model_end.php
	
}
?>