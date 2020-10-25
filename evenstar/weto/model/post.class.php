<?php

/*
 * Copyright (C) xiuno.com
 */

class post extends base_model {
	
	function __construct(&$conf) {
		parent::__construct($conf);
		$this->table = 'post';
		$this->primarykey = array('fid', 'pid');
		$this->maxcol = 'pid';
		
		// hook post_construct_end.php
	}
	
	// 附件数计数
	public function update_attachnum($fid, $pid, $num) {
		$post = $this->read($fid, $pid);
		$post['attachnum'] += $num;
		$this->update($post);
	}
	
	// 图片数计数
	public function update_imagenum($fid, $pid, $num) {
		$post = $this->read($fid, $pid);
		$post['imagenum'] += $num;
		$this->update($post);
	}

	public function get_list_by_page() {
	
	}
	
	public function check_message(&$message) {
		$message = trim($message);
		if(empty($message)) {
			return '内容不能为空。';
		}
		$s = str_replace(array('<br>', '<br/>', '<br />', '&nbsp;', ' ', "\r", "\n", "\t"), '', $message);
		$s = str_replace('　', '', $s);
		$s = preg_replace('#<p+[^>]*>\s*</p>#i', '', $s);
		$s = preg_replace('#<div[^>]*>\s*</div>#i', '', $s);
		$s = preg_replace('#<span[^>]*>\s*</span>#i', '', $s);
		if($s == '') {
			return '内容不能为空。';
		}
		if(utf8::strlen($message) > 2000000) {
			return '内容不能超过200万个字符。';
		}
		$error = $this->mmisc->check_badword($message);
		if($error) {
			return $error;
		}
		return '';
	}
	
	// 用来显示给用户
	public function format(&$post) {
		//$post['subject']  = htmlspecialchars($post['subject']);
		//isset($post['message']) && $post['message']  = nl2br(htmlspecialchars($post['message']));
		isset($post['dateline']) && $post['dateline_fmt'] = misc::humandate($post['dateline']);
		
		// hook post_model_format_end.php
	}

	// 删除回帖，非主题帖。相对比较简单，是相对！万恶的删除和缓存啊！不过现在终于可以把它封起来了，稳定了。
	public function xdelete($fid, $pid, $updatestat = TRUE) {
		if(!isset($this->conf['credits_policy_post'])) {
			$this->conf += $this->kv->xget('conf_ext');
		}
		
		$post = $this->read($fid, $pid);
		$tid = $post['tid'];
		$uid = $post['uid'];
		
		$default = array('threads'=>0, 'posts'=>0, 'credits'=>0, 'golds'=>0, 'myposts'=>0);
		$return = array (
			'forum'=>array($fid => array('todayposts'=>0, 'posts'=>0)),
			'user' => array($uid=>$default),
			'thread' => array("$fid-$tid" => array('posts'=>0)),
			'fidtidpid' => array("$fid-$tid-$pid" => $post['page'])	// 最小 page
		);
		$rforum = &$return['forum'][$fid];
		$ruser = &$return['user'];
		$rthread = &$return['thread']["$fid-$tid"];
		
		// 删除 $attach
		($post['attachnum'] || $post['imagenum']) && $this->attach->xdelete($fid, $pid);
		
		// 删除 mypost，有可能空删，因为记录的时候根据 tid 去重了
		$r = $this->mypost->delete($post['uid'], $post['fid'], $post['pid']);
		$r && $ruser[$uid]['myposts']++;
		
		// 删除 $post
		$this->delete($fid, $pid);
		
		// 更新 $forum 版块的总帖数
		$rforum['posts']++;
		$post['dateline'] > $_SERVER['time_today'] && $rforum['todayposts']++;
		
		// 更新 $user
		$ruser[$uid]['posts']++;
		$ruser[$uid]['credits'] += $this->conf['credits_policy_post'];
		$ruser[$uid]['golds'] += $this->conf['golds_policy_post'];
		
		// 更新 $thread
		$rthread['posts']++;
		
		if($updatestat) {
			$this->xdelete_update($return);
		}
		
		// 更新 runtime
		$this->runtime->xset('posts', '-1');
		
		// hook post_model_xdelete_end.php
		
		return $return;
	}
	
	// 合并返回值，用户删除版块时候，合并主题。
	public function xdelete_merge_return(&$return, &$return2) {
		foreach($return2['user'] as $uid=>$arr) {
			if(!$uid) continue;
			if(!isset($return['user'][$uid])) { $return['user'][$uid] = $arr; continue; }
			$return['user'][$uid]['posts'] += $arr['posts'];
			$return['user'][$uid]['credits'] += $arr['credits'];
			$return['user'][$uid]['golds'] += $arr['golds'];
			$return['user'][$uid]['myposts'] += $arr['myposts'];
		}
		foreach($return2['forum'] as $fid=>$arr) {
			if(!$fid) continue;
			if(!isset($return['forum'][$fid])) { $return['forum'][$fid] = $arr; continue; }
			$return['forum'][$fid]['posts'] += $arr['posts'];
			$return['forum'][$fid]['todayposts'] += $arr['todayposts'];
		}
		foreach($return2['thread'] as $tid=>$arr) {
			if(!$tid) continue;
			if(!isset($return['thread'][$tid])) { $return['thread'][$tid] = $arr; continue; }
			$return['thread'][$tid]['posts'] += $arr['posts'];
		}
		// 这里~~~ 万恶的数组合并，复杂的重现，浪费老夫几个小时的生命，应该做个记号吧。
		foreach($return2['fidtidpid'] as $fidtidpid=>$page) {
			if(!$fidtidpid) continue;
			if(!isset($return['fidtidpid'][$fidtidpid])) {$return['fidtidpid'][$fidtidpid] = $page; continue;}
			if($return['fidtidpid'][$fidtidpid] > $page) { 
				$return['fidtidpid'][$fidtidpid] = $page;
			}
		}
		
		// hook post_model_xdelete_merge_end.php
	}
	
	// 关联删除后的更新，会涉及到楼层整理，非常麻烦。
	public function xdelete_update($return) {
		// 更新回复用户的积分
		if(isset($return['user'])) {
			foreach($return['user'] as $uid=>$arr) {
				if(!$uid) continue;
				$user = $this->user->read($uid);
				$user['posts'] -= $arr['posts'];
				$user['credits'] -= $arr['credits'];
				$user['golds'] -= $arr['golds'];
				$user['myposts'] -= $arr['myposts'];
				$this->user->update($user);
			}
		}
		if(isset($return['forum'])) {
			$todayposts = 0;
			foreach($return['forum'] as $fid=>$arr) {
				if(!$fid) continue;
				$forum = $this->forum->read($fid);
				$forum['posts'] -= $arr['posts'];
				$forum['todayposts'] -= $arr['todayposts'];
				$todayposts += $arr['todayposts'];
				$this->forum->xupdate($forum);
			}
			
			$this->runtime->xset('todayposts', '-'.$todayposts);
		}
		
		// todo: lastuid, lastusername 貌似没有更新
		if(isset($return['thread'])) {
			foreach($return['thread'] as $tid=>$arr) {
				if(!$tid) continue;
				list($fid, $tid) = explode('-', $tid);
				$fid = intval($fid);
				$tid = intval($tid);
				$thread = $this->thread->read($fid, $tid);
				$thread['posts'] -= $arr['posts'];
				$this->thread->update($thread);
			}
		}
		if(isset($return['fidtidpid'])) {
			foreach($return['fidtidpid'] as $fidtidpid=>$page) {
				if(!$fidtidpid) continue;
				list($fid, $tid, $pid) = explode('-', $fidtidpid);
				$fid = intval($fid);
				$tid = intval($tid);
				$pid = intval($pid);
				$this->rebuild_page($fid, $tid, $pid, $page);
			}
		}
		
		// hook post_model_xdelete_update_end.php
	}
	
	// 重建帖子，传入最小的 $startpage
	public function rebuild_page($fid, $tid, $pid, $startpage) {
		$thread = $this->thread->read($fid, $tid);
		$tid = $thread['tid'];
	
		// 如果回帖数小于100， 重建所在页之后的帖子
		$totalpage = ceil($thread['posts'] / $this->conf['pagesize']);
		
		$k = 0; // 翻页计数，到20则清零，并且 $kpage+1
		$kpage = $startpage;
		for($i = $startpage; $i <=  $totalpage; $i++) {
			// 翻页查找所有id,逐个更新
			$postlist = $this->index_fetch(array('fid'=>$fid, 'tid'=>$tid, 'page'=>$i),  array(), 0, $this->conf['pagesize']);
			//ksort($postlist);
			foreach($postlist as $_post) {
				if($kpage != $_post['page']) {
					$_post['page'] = $kpage;
					$this->update($_post);
				}
				if(++$k == $this->conf['pagesize']) {
					$k = 0;
					$kpage++;
				}
			}
		}
		return TRUE;
	}
	
	/*
	public function html_safe($s) {
		include_once FRAMEWORK_PATH.'lib/kses.class.php';
		$allowed = array('b' => array(),
		                 'i' => array(),
		                 'a' => array('href'  => array('minlen' => 3, 'maxlen' => 50),
		                              'title' => array('valueless' => 'n')),
		                 'p' => array('align' => 1,
		                              'dummy' => array('valueless' => 'y')),
		                 'img' => array('src' => 1), # FIXME
		                 'font' => array('size' =>
		                                         array('minval' => 4, 'maxval' => 20)),
		                 'br' => array(), 
		                 'span' => array('style'=>array()), 
		                 'h1' => array(), 'h2'=> array(), 'h3'=> array(), 'h4'=> array(), 'h5'=> array(), 
		                 'div' => array(),
		                 'table' => array('width'=> array('maxval'=>800)), 'tr' => array(), 'td' => array('maxval'=>800), 'th' => array('maxval'=>800),'tbody' => array(),'tfoot' => array(),'thead' => array(),
		                 );
		$s = kses($s, $allowed, array('http', 'https'));
		return $s;
	}
	*/
	
	public function html_safe($doc) {
		return xn_html_safe::filter($doc);
	}
	
	// hook post_model_end.php
}
?>