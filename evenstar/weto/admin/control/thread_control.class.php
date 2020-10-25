<?php

/*
 * Copyright (C) xiuno.com
 */

!defined('FRAMEWORK_PATH') && exit('FRAMEWORK_PATH not defined.');

include BBS_PATH.'admin/control/admin_control.class.php';

class thread_control extends admin_control {
	
	function __construct(&$conf) {
		parent::__construct($conf);
		$this->check_admin_group();
	}
	
	// 列表
	public function on_index() {
		$this->on_list();
	}	
	
	// 第一步查找帖子，第二步删除主题
	public function on_list() {
		$this->_title[] = '管理帖子';
		$this->_nav[] = '<a href="./">管理帖子</a>';
		
		$uid = core::gpc('uid', 'R');// 用户名/email/uid
		if(!empty($uid)) {
			if(is_numeric($uid)) {
				$uid = intval($uid);
				$user = $this->user->read($uid);
				$this->check_user_exists($user);
				$uid = $user['uid'];
			} elseif(strpos($uid, '@') !== FALSE) {
				$uid = $this->user->get_uid_by_email($uid);
				$user = $this->user->read($uid);
				$this->check_user_exists($user);
			} elseif($uid){
				$user = $this->user->get_user_by_username($uid);
				$this->check_user_exists($user);
				$uid = $user['uid'];
			}
		}
		
		$keyword = urldecode(core::gpc('keyword', 'R'));
		$keyword = misc::safe_str($keyword);
		$keyword_url = urlencode($keyword);
		
		$maxtid = $this->thread->maxid();
		$tidfrom = max(0, intval(core::gpc('tidfrom', 'R')));
		$tidto = max(0, intval(core::gpc('tidto', 'R')));
		
		// 根据UID，关键词查找主题，列出100个，两列排版，第二步，删除，批量替换，一次100个。
		
		// 数据量大的情况下，此处可能会导致性能问题。
		$threadlist = array();
		if($uid) {
			// 扫描定长表
			$threadlist = $this->thread->index_fetch(array('uid'=>$uid), array(), 0, 2000);
		} elseif($tidfrom) {
			$threadlist = $this->thread->index_fetch(array('tid'=>array('>='=>$tidfrom, '<='=>$tidto)), array(), 0, 2000);
		} elseif($keyword){
			// 标题 like
			$threadlist = $this->thread->index_fetch(array('subject'=>array('LIKE'=>$keyword)), array(), 0, 2000);
		}
		
		foreach($threadlist as &$thread) {
			$thread['forumname'] = isset($this->conf['forumarr'][$thread['fid']]) ? $this->conf['forumarr'][$thread['fid']] : '';
			$thread['dateline'] = misc::minidate($thread['dateline']);
			$keyword && $thread['subject'] = str_replace($keyword, '<span class="red">'.$keyword.'</span>', $thread['subject']);
		}
		
		$srchstring = core::gpc('srchstring', 'R');
		$replacestring = core::gpc('replacestring', 'R');
		
		// 默认值
		if(empty($tidto) && empty($tidfrom)) {
			$tidfrom = 1;
			$tidto = $maxtid;
			if($tidfrom - $tidto > 2000) $tidfrom = $tidto - 2000;
		}
		
		if(empty($srchstring) && !empty($keyword)) {
			$srchstring = $keyword;
		}
		$this->view->assign('srchstring', $srchstring);
		$this->view->assign('replacestring', $replacestring);
		$this->view->assign('uid', $uid);
		$this->view->assign('tidfrom', $tidfrom);
		$this->view->assign('tidto', $tidto);
		$this->view->assign('keyword', $keyword);
		$this->view->assign('keyword_url', $keyword_url);
		$this->view->assign('threadlist', $threadlist);
		
		// hook admin_thread_list_view_before.php
		
		$this->view->display('thread_list.htm');
	}
	
	public function on_replace() {
	
		$uid = intval(core::gpc('uid', 'P'));
		$tidfrom = intval(core::gpc('tidfrom', 'P'));
		$tidto = intval(core::gpc('tidto', 'P'));
		
		$srchstring = urldecode(core::gpc('srchstring', 'P'));
		$replacestring = urldecode(core::gpc('replacestring', 'P'));
		$srchstring_url = urlencode($srchstring);
		$replacestring_url = urlencode($replacestring);
		$fidtids = core::gpc('fidtids', 'P');
		foreach((array)$fidtids as $v) {
			list($fid, $tid) = explode('_', $v);
			$fid = intval($fid);
			$tid = intval($tid);
			$thread = $this->thread->read($fid, $tid);
			if(empty($thread)) continue;
			
			$thread['subject'] = str_replace($srchstring, $replacestring, $thread['subject']);
			$this->thread->update($thread);
			
			// 替换前10页
			$pagesize = $this->conf['pagesize'];
			$pagenum = ceil($thread['posts'] / $pagesize);
			$pagenum = min(10, $pagenum);	// 更新前10页。
			for($i = 1; $i <= $pagenum; $i++) {
				$postlist = $this->post->index_fetch(array('fid'=>$fid, 'tid'=>$tid, 'page'=>$i), array(), 0, $pagesize);
				foreach($postlist as $post) {
					$post['message'] = str_replace($srchstring, $replacestring, $post['message']);
					$this->post->update($post);
				}
			}
		}
		
		// hook admin_thread_replace_after.php
		
		$this->message('替换完毕', 1, "?thread-list-uid-$uid-tidfrom-$tidfrom-tidto-$tidto-srchstring-$srchstring_url-replacestring-$replacestring_url.htm");
	}

	public function on_delete() {
		$this->_title[] = '删除主题';
		$this->_nav[] = '删除主题';
		
		$uid = intval(core::gpc('uid', 'P'));
		$tidfrom = intval(core::gpc('tidfrom', 'P'));
		$tidto = intval(core::gpc('tidto', 'P'));
		
		$fidtids = core::gpc('fidtids', 'P');
		foreach((array)$fidtids as $v) {
			list($fid, $tid) = explode('_', $v);
			$fid = intval($fid);
			$tid = intval($tid);
			$thread = $this->thread->read($fid, $tid);
			if(empty($thread)) continue;
			
			// hook admin_thread_delete_after.php
			
			$this->thread->xdelete($fid, $tid, TRUE);
		}
		
		// hook admin_thread_delete_complete.php
		
		$this->message('删除完毕', 1, "?thread-list-uid-$uid-tidfrom-$tidfrom-tidto-$tidto.htm");
	}
	
	//hook admin_thread_control_after.php
	
}
?>