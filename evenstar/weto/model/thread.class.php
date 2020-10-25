<?php

/*
 * Copyright (C) xiuno.com
 */

class thread extends base_model {
	
	function __construct(&$conf) {
		parent::__construct($conf);
		$this->table = 'thread';
		$this->primarykey = array('fid', 'tid');
		$this->maxcol = 'tid';
		
		// hook thread_construct_end.php
	}
	
	public function get_threadlist_by_fid($fid, $orderby, $start, $limit, $total = 0) {
		$cond = array('fid'=>$fid);
		// 优化大数据翻页，倒排
		if($start > 1000 && $total > 2000 && $start > $total / 2) {
			$start = $total - $start;
			$orderby = $orderby == 0 ? array('lastpost'=>1) : array('tid'=>1);
			$threadlist = $this->index_fetch($cond, $orderby, max(0, $start - $limit), $limit);
			$threadlist = array_reverse($threadlist, TRUE);
			return $threadlist;
		} else {
			$orderby = $orderby == 0 ? array('lastpost'=>-1) : array('tid'=>-1);
			$threadlist = $this->index_fetch($cond, $orderby, $start, $limit);
			return $threadlist;
		}
	}
	
	// 按照 tid 倒序，获取最新的列表
	public function get_newlist($start = 0, $limit = 30, $threadlist = array()) {
		// 递归深度不能超过4次
		static $deep = 1;
		if($deep++ > 4) return $threadlist;
		
		$newlist = array();
		$newlist = $this->thread_new->index_fetch(array(), array('tid'=>-1), $start, $limit);
		foreach($newlist as $new) {
			$thread = $this->read($new['fid'], $new['tid']);
			$threadlist[] = $thread;
		}
		if(count($newlist) == $limit && count($threadlist) < $limit) {
			$threadlist += $this->get_newlist($start + $limit, $limit, $threadlist);
			$threadlist = array_slice($threadlist, 0, $limit);
		}
		return $threadlist;
	}
	
	// ------------------> 杂项
	public function check_subject(&$subject) {
		if(utf8::strlen($subject) > 200) {
			return '标题不能超过 200 字，当前长度：'.strlen($subject);
		}
		$error = $this->mmisc->check_badword($subject);
		if($error) {
			return $error;
		}
		if(empty($subject)) {
			return '标题不能为空。';
		}
		// hook thread_model_check_subject_end.php
		return '';
	}
	
	// 用来显示给用户
	public function format(&$thread, $forum = array()) {
		if(empty($thread)) return;
		$thread['subject_substr']  = utf8::substr($thread['subject'], 0, 40);
		isset($thread['message']) && $thread['message']  = nl2br(htmlspecialchars($thread['message']));
		$thread['isnew'] = ($this->conf['site_pv'] <= 1000000 ? $_SERVER['time'] - 86400 * 7 : $_SERVER['time_today']) < max($thread['dateline'], $thread['lastpost']);	// 最新帖定义：如果是 pv > 100w 的站点，为今日，否则为7天内的，否则先的太“冷清”了。
		$thread['dateline_fmt'] = misc::humandate($thread['dateline']);
		$thread['posts_fmt'] = max(0, $thread['posts'] - 1);	// 用来前端显示
		empty($thread['lastpost']) && $thread['lastpost'] = $thread['dateline'];
		$thread['lastpost_fmt'] = misc::humandate($thread['lastpost']);
		$fid = $thread['fid'];
		if($forum) {
			$thread['forum_types'] = &$forum['types'];
			$thread['typename1'] = $thread['typeid1'] && isset($forum['types'][1][$thread['typeid1']]) ? $forum['types'][1][$thread['typeid1']] : '';
			$thread['typename2'] = $thread['typeid2'] && isset($forum['types'][2][$thread['typeid2']]) ? $forum['types'][2][$thread['typeid2']] : '';
			$thread['typename3'] = $thread['typeid3'] && isset($forum['types'][3][$thread['typeid3']]) ? $forum['types'][3][$thread['typeid3']] : '';
			$thread['typename4'] = $thread['typeid4'] && isset($forum['types'][4][$thread['typeid4']]) ? $forum['types'][4][$thread['typeid4']] : '';
		} else {
			static $types = FALSE;
			$types === FALSE && $types = $this->thread_type->get_types_from_cache();
			$thread['typename1'] = $thread['typeid1'] && !empty($types[$fid][1][$thread['typeid1']]) ? $types[$fid][1][$thread['typeid1']] : '';
			$thread['typename2'] = $thread['typeid2'] && !empty($types[$fid][2][$thread['typeid2']]) ? $types[$fid][2][$thread['typeid2']] : '';
			$thread['typename3'] = $thread['typeid3'] && !empty($types[$fid][3][$thread['typeid3']]) ? $types[$fid][3][$thread['typeid3']] : '';
			$thread['typename4'] = $thread['typeid4'] && !empty($types[$fid][4][$thread['typeid4']]) ? $types[$fid][4][$thread['typeid4']] : '';
			$thread['forum_types'] = &$types[$fid];
		}
		$thread['forumname'] = isset($this->conf['forumarr'][$fid]) ? $this->conf['forumarr'][$fid] : '';
		
		// 精华 火帖 新帖 老帖
		// todo: 居然有记录没有 digest
		if(isset($thread['digest']) && $thread['digest'] > 0) {
			$thread['color'] = "thread-digest-$thread[digest]";
		} elseif($thread['posts'] > $this->conf['threadlist_hotviews']) {
			$thread['color'] = "thread-hot";
		// 24小时产生的帖子为新帖
		} elseif($_SERVER['time'] - $thread['dateline'] < 86400) {
			$thread['color'] = "thread-new";
		} else {
			$thread['color'] = "thread-old";
		}
		
		if($thread['top'] > 0) {
			$thread['icon'] = "icon-top-$thread[top]";
		} elseif(isset($thread['digest']) && $thread['digest'] > 0) {
			$thread['icon'] = "icon-digest-$thread[digest]";
		} elseif($thread['color'] == 'thread-new') {
			$thread['icon'] = "icon-post-blue";
		} elseif($thread['color'] == 'thread-old') {
			$thread['icon'] = "icon-post-grey";
		} elseif($thread['color'] == 'thread-hot') {
			$thread['icon'] = "icon-post-red";
		}
		
		$thread['lastpage'] = $thread['posts_fmt'] > $this->conf['pagesize'] ? ceil($thread['posts_fmt'] / $this->conf['pagesize']) : 1;
		
		// hook thread_model_format_end.php
	}
	
	// 关联删除，清理掉相关数据
	public function xdelete($fid, $tid, $updatestat = TRUE) {
		
		// 加载配置
		if(!isset($this->conf['credits_policy_post'])) {
			$this->conf += $this->kv->xget('conf_ext');
		}
		
		$forum = $this->forum->read($fid);
		$thread = $this->thread->read($fid, $tid);
		$user = $this->user->read($thread['uid']);
		$uid = $thread['uid'];
		
		// 受影响的值。
		$default_user = array('threads'=>0, 'posts'=>0, 'digests'=>0, 'credits'=>0, 'golds'=>0, 'myposts'=>0);
		$default_forum = array('threads'=>0, 'posts'=>0, 'digests'=>0, 'todayposts'=>0);
		$return = array(
			'forum'=> array($fid=>$default_forum),
			'user' => array($uid=>$default_user)
		);
		$rforum = &$return['forum'][$fid];
		$ruser = &$return['user'];
		
		// todo:算出分页，一页一页的删除，可能会超时。
		$pagesize = $this->conf['pagesize'];
		$pagenum = ceil($thread['posts'] / $pagesize);
		$todayposts = 0;
		for($i = 1; $i <= $pagenum; $i++) {
			$postlist = $this->post->index_fetch(array('fid'=>$fid, 'tid'=>$tid, 'page'=>$i), array(), 0, $pagesize);
			foreach($postlist as $post) {
				!isset($ruser[$post['uid']]) && $ruser[$post['uid']] = $default_user;
				
				// 删除 attach
				($post['attachnum'] || $post['imagenum']) && $this->attach->xdelete($post['fid'], $post['pid']);
				
				// 删除 mypost，删除主题一定不会空删除
				$this->mypost->delete($post['uid'], $post['fid'], $post['pid']);
				
				$ruser[$post['uid']]['myposts']++;
				
				$post['dateline'] > $_SERVER['time_today'] && $todayposts++;
				
				// 删除 $post
				$this->post->delete($post['fid'], $post['pid']);
				
				$ruser[$post['uid']]['posts']++;
				$ruser[$post['uid']]['credits'] += $this->conf['credits_policy_post'];
				$ruser[$post['uid']]['golds'] += $this->conf['golds_policy_post'];
			}
		}
		
		// 发表主题的积分策略不同于回帖的策略。
		$ruser[$uid]['credits'] += $this->conf['credits_policy_thread'];
		$ruser[$uid]['golds'] += $this->conf['golds_policy_thread'];
		if($thread['digest'] > 0) {
			$ruser[$uid]['digests']++;
			$ruser[$uid]['credits'] += $this->conf['credits_policy_digest_'.$thread['digest']];
			$ruser[$uid]['golds'] += $this->conf['golds_policy_digest_'.$thread['digest']];
		}
		$ruser[$uid]['threads']++;
		
		$rforum['threads']++;
		$rforum['posts'] += $thread['posts'];
		$rforum['todayposts'] += $todayposts;
		$thread['digest'] > 0 && $rforum['digests']++;
		
		// 删除置顶
		if($thread['top']) {
			$thread['top'] == 1 && $this->thread_top->delete_top_1($forum, array("$fid-$tid"));
			$thread['top'] == 3 && $this->thread_top->delete_top_3(array("$fid-$tid"));
		}
		
		// 删除主题
		$this->thread->delete($fid, $tid);
		$this->thread_digest->delete($tid);
		$this->thread_new->delete($tid);
		
		// 同时删除 thread_view, 这里为强关联
		$this->thread_views->delete($tid);
		
		// modlog
		$this->modlog->delete_by_fid_tid($fid, $tid);
		
		// 评分不删除
		
		// 更新 runtime
		$this->runtime->xset('threads', '-1');
		$this->runtime->xset('posts', '-'.$thread['posts']);
		$this->runtime->xset('todayposts', '-'.$todayposts);
		
		if($updatestat) {
			$this->xdelete_update($return);
			
			// 更新最后发帖，直接清零
			if($forum['lasttid'] == $tid) {
				$forum['lasttid'] = 0;
				$forum['lastuid'] = 0;
				$forum['lastusername'] = '';
				$forum['lastsubject'] = '';
			}
		}
		
		// 更新主题分类数
		if($thread['typeid1'] > 0 || $thread['typeid2'] > 0 || $thread['typeid3'] > 0 || $thread['typeid4'] > 0) {
			$this->thread_type_data->xdelete($fid, $tid);
		}
		
		// hook thread_model_xdelete_end.php
		
		return $return;
	}
	
	// 合并返回值，用户删除版块时候，合并主题。
	public function xdelete_merge_return(&$return, &$return2) {
		foreach($return2['user'] as $uid=>$arr) {
			if(!$uid) continue;
			if(!isset($return['user'][$uid])) { $return['user'][$uid] = $arr; continue; }
			$return['user'][$uid]['threads'] += $arr['threads'];
			$return['user'][$uid]['posts'] += $arr['posts'];
			$return['user'][$uid]['myposts'] += $arr['myposts'];
			$return['user'][$uid]['credits'] += $arr['credits'];
			$return['user'][$uid]['golds'] += $arr['golds'];
			$return['user'][$uid]['digests'] += $arr['digests'];
		}
		foreach($return2['forum'] as $fid=>$arr) {
			if(!$fid) continue;
			if(!isset($return['forum'][$fid])) { $return['forum'][$fid] = $arr; continue; }
			$return['forum'][$fid]['threads'] += $arr['threads'];
			$return['forum'][$fid]['posts'] += $arr['posts'];
			$return['forum'][$fid]['todayposts'] += $arr['todayposts'];
			$return['forum'][$fid]['digests'] += $arr['digests'];
		}
		
		// hook thread_model_xdelete_merge_return_end.php
	}
	
	// 关联删除后的更新
	public function xdelete_update($return, $keep_user_credits = 0) {
		// 更新回复用户的积分
		if(isset($return['user'])) {
			foreach($return['user'] as $uid=>$arr) {
				if(!$uid) continue;
				$user = $this->user->read($uid);
				if(empty($user)) continue;
				$user['threads'] -= $arr['threads'];
				$user['posts'] -= $arr['posts'];
				$user['myposts'] -= $arr['myposts'];
				empty($keep_user_credits) && $user['credits'] -= $arr['credits'];
				empty($keep_user_credits) && $user['golds'] -= $arr['golds'];
				$user['digests'] -= $arr['digests'];
				$this->user->update($user);
			}
		}
		
		if(isset($return['forum'])) {
			foreach($return['forum'] as $fid=>$arr) {
				if(!$fid) continue;
				$forum = $this->forum->read($fid);
				if(empty($forum)) continue;
				$forum['threads'] -= $arr['threads'];
				$forum['posts'] -= $arr['posts'];
				$forum['todayposts'] -= $arr['todayposts'];
				$forum['digests'] -= $arr['digests'];
				$this->forum->xupdate($forum);
				$this->forum->update_last($fid);
				$this->runtime->xupdate('forumarr');
			}
		}
		
		// hook thread_model_xdelete_update_end.php
	}
	
	// hook thread_model_end.php
}
?>