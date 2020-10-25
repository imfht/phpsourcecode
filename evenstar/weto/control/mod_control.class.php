<?php

/*
 * Copyright (C) xiuno.com
 */

!defined('FRAMEWORK_PATH') && exit('FRAMEWORK_PATH not defined.');

include BBS_PATH.'control/common_control.class.php';

class mod_control extends common_control {
	
	function __construct(&$conf) {
		parent::__construct($conf);
		$this->check_login();
		if($this->_user['groupid'] > 5) {
			$this->message('对不起，您没有权限访问此版块。');
		}
		
		// 加载精华积分策略
		$this->conf += $this->kv->xget('conf_ext');
		
		// 检查IP 屏蔽
		$this->check_ip();
	}
	
	public function on_index() {
		$this->on_setforum();
	}
	
	// 设置置顶 各种置顶最多十个！
	public function on_top() {
		$this->_title[] = '设置置顶';
		$this->_nav[] = '设置置顶';
		
		$this->check_login();
		
		$fid = intval(core::gpc('fid'));
		$fidtidarr = $this->get_fid_tids();
		
		$forum = $this->forum->read($fid);

		$this->check_access($forum, 'top');
		
		// 去除非本版块的置顶主题
		foreach($fidtidarr as $k=>$v) {
			$_fid = $v[0];
			$_tid = $v[1];
			$thread = $this->thread->read($_fid, $_tid);
			if(empty($thread)) unset($fidtidarr[$k]);
		}
		
		if(!$this->form_submit()) {
			
			// 初始化控件状态
			$this->init_view_thread($fidtidarr, 'top');
			
			$this->view->assign('fid', $fid);
			
			$this->view->display('mod_top_ajax.htm');
		} else {
			$rank = intval(core::gpc('rank', 'P'));
			$systempm = intval(core::gpc('systempm', 'P'));
			$comment = core::gpc('comment', 'P');
			$this->check_comment($comment);
			
			if(($this->_user['groupid'] == 4 || $this->_user['groupid'] == 5) && $rank > 1) {
				$this->message('您只有本版块置顶权限！', 0);
			}
			
			// -------> 统计 top_1 2 3 的总数，是否超过5个。
			$n = count($fidtidarr);
			if($rank == 1) {
				// 1 级置顶
				$keys = array();
				$this->tidkeys_to_keys($keys, $forum['toptids']);
				if(count($keys) + $n > 8) {
					$this->message('版块置顶的个数不能超过8个。', 0);
				}
			} elseif($rank == 3) {
				$keys = array();
				$this->tidkeys_to_keys($keys, $this->conf['toptids']);
				if(count($keys) + $n > 8) {
					$this->message('全站置顶的个数不能超过8个。', 0);
				}
			}
			// end
			
			// hook mod_top_after.php
			
			// 先去除已有，然后加入
			$this->thread_top->delete_top_1($forum, $fidtidarr);
			$this->thread_top->delete_top_3($fidtidarr);
			
			if($rank == 0) {
				
			} elseif($rank == 1) {
				$this->thread_top->add_top_1($forum, $fidtidarr);
			} elseif($rank == 3) {
				$this->thread_top->add_top_3($fidtidarr);
			}
			
			// 记录到版主操作日志
			foreach($fidtidarr as &$v) {			// 此处也得用 &
				// 初始化数据
				$fid = $v[0];
				$tid = $v[1];
				
				$thread = $this->thread->read($fid, $tid);
				if(empty($thread)) continue;
				$this->modlog->create(array(
					'uid'=>$this->_user['uid'],
					'username'=>$this->_user['username'],
					'fid'=>$fid,
					'dateline'=>$_SERVER['time'],
					'tid'=>$tid,
					'pid'=>0,
					'subject'=>$thread['subject'],
					'credits'=>0,
					'golds'=>0,
					'dateline'=>$_SERVER['time'],
					'action'=>$rank == 0 ? 'untop' : 'top',
					'comment'=>$comment,
				));
				
				$this->inc_modnum($fid, $tid);
				
				// 发送系统消息：
				if($systempm) {
					$pmsubject = utf8::substr($thread['subject'], 0, 32);
					$pmmessage = "您的主题<a href=\"?thread-index-fid-$fid-tid-$tid.htm\" target=\"_blank\">【{$pmsubject}】</a>被【{$this->_user['username']}】".($rank > 0 ? '置顶' : '取消置顶')."。";
					$this->pm->system_send($thread['uid'], $thread['username'], $pmmessage);
				}
			}
			
			// hook mod_top_succeed.php
			
			$this->message('操作成功！', 1);
		}
		
	}
	
	public function on_digest() {
		$this->_title[] = '设置精华';
		$this->_nav[] = '设置精华';
		
		$this->check_login();
		
		$fid = intval(core::gpc('fid'));
		$fidtidarr = $this->get_fid_tids();
		
		$forum = $this->forum->read($fid);
		$this->check_forum_exists($forum);
		
		$this->check_access($forum, 'digest');
		
		if(!$this->form_submit()) {
			
			// 第一个元素作为选中状态
			$fid_tid = array_shift($fidtidarr);
			$fid = $fid_tid[0];
			$tid = $fid_tid[1];
			$thread = $this->thread->read($fid, $tid);
			$this->check_thread_exists($thread);
			
			$this->view->assign('thread', $thread);
			$this->view->assign('fid', $fid);
			$this->view->assign('tid', $tid);
			
			// hook mod_digest_before.php
			$this->view->display('mod_digest_ajax.htm');
		} else {
			// 修改精华等级，分类。
			$rank = intval(core::gpc('rank', 'P'));
			$systempm = intval(core::gpc('systempm', 'P'));
			$comment = core::gpc('comment', 'P');
			$this->check_comment($comment);
			
			$fidarr = $creditarr = $goldarr = $digestarr = array();
			
			// hook mod_digest_after.php
			$tidnum = 0;
			foreach($fidtidarr as &$v) {			// 此处也得用 &
				// 初始化数据
				$fid = intval($v[0]);
				$tid = intval($v[1]);
				$thread = $this->thread->read($fid, $tid);
				if(empty($thread)) continue;
				
				$tidnum++;	// 帖子数，用来统计精华数
				
				// 更新论坛精华数 todo: 准确？ 没啥用
				$forum = $this->forum->read($fid);
				$rank == 0 ? ($thread['digest'] && $forum['digests']--) : (!$thread['digest'] && $forum['digests']++);
				$this->forum->xupdate($forum);
				$fidarr[$fid] = $fid;
				
				// 更新用户精华数，积分
				!isset($creditarr[$thread['uid']]) && $creditarr[$thread['uid']] = 0;
				!isset($goldarr[$thread['uid']]) && $goldarr[$thread['uid']] = 0;
				!isset($digestarr[$thread['uid']]) && $digestarr[$thread['uid']] = 0;
				// 先减去积分，否则会造成重复加分
				if($thread['digest'] > 0) {
					$creditarr[$thread['uid']] -= $this->conf['credits_policy_digest_'.$thread['digest']];
					$goldarr[$thread['uid']] -= $this->conf['golds_policy_digest_'.$thread['digest']];
				}
				if($rank > 0) {
					$creditarr[$thread['uid']] += $this->conf['credits_policy_digest_'.$rank];
					$goldarr[$thread['uid']] += $this->conf['golds_policy_digest_'.$rank];
				}
				// 加入精华
				if($rank > 0 && $thread['digest'] == 0) {
					$digestarr[$thread['uid']]++;
					$this->thread_digest->create(array('fid'=>$fid, 'tid'=>$tid, 'digest'=>$rank));
				// 取消精华
				} elseif($rank == 0 && $thread['digest'] > 0) {
					$digestarr[$thread['uid']]--;
					$this->thread_digest->delete($tid);
				// 更新精华
				} elseif($rank > 0 && $thread['digest'] > 0 && $rank != $thread['digest']) {
					$thread_digest = $this->thread_digest->read($tid);
					$thread_digest['digest'] = $rank;
					$this->thread_digest->update($thread_digest);
				} else {
					continue;
				}
				
				// 记录到版主操作日志
				$credits2 = $rank == 0 ? 0 - $this->conf['credits_policy_digest_'.$thread['digest']] : $this->conf['credits_policy_digest_'.$rank];
				$golds2 = $rank == 0 ? 0 - $this->conf['golds_policy_digest_'.$thread['digest']] : $this->conf['golds_policy_digest_'.$rank];
				$this->modlog->create(array(
					'uid'=>$this->_user['uid'],
					'username'=>$this->_user['username'],
					'fid'=>$fid,
					'tid'=>$tid,
					'pid'=>0,
					'subject'=>$thread['subject'],
					'credits'=> $credits2,
					'golds'=> $golds2,
					'dateline'=>$_SERVER['time'],
					'action'=>$rank == 0 ? 'undigest' : 'digest',
					'comment'=>$comment,
				));
				
				$thread['digest'] = $rank;
				
				$this->thread->update($thread);
				
				$this->inc_modnum($fid, $tid);
				
				// 发送系统消息：
				if($systempm) {
					$pmsubject = utf8::substr($thread['subject'], 0, 32);
					$pmmessage = "您的主题<a href=\"?thread-index-fid-$fid-tid-$tid.htm\" target=\"_blank\">【{$pmsubject}】</a>被【{$this->_user['username']}】".($rank > 0 ? '设置精华' : '取消精华')."。";
					$this->pm->system_send($thread['uid'], $thread['username'], $pmmessage);
				}
				
				// hook mod_digest_loop_after.php
			}
			
			foreach($creditarr as $uid=>$credits) {
				$uid = intval($uid);
				$user = $this->user->read($uid);
				$user['credits'] += $credits;
				$user['golds'] += $goldarr[$uid];
				$user['digests'] += $digestarr[$uid];
				$this->user->update($user);
			}
			
			// hook mod_digest_succeed.php
			$this->message('操作成功！');
		}
	}
	
	// 批量设置主题分类
	public function on_type() {
		$this->_title[] = '设置主题分类';
		$this->_nav[] = '设置主题分类';
		
		$this->check_login();
		
		$fid = intval(core::gpc('fid'));
		$fidtidarr = $this->get_fid_tids();
		
		$forum = $this->mcache->read('forum', $fid);
		
		if(!array_filter($forum['typecates'])) {
			$this->message('当前版块未开启主题分类。', 0);
		}
		
		if(!$this->form_submit()) {
			
			// 初始化控件状态
			$fidtid = array_pop($fidtidarr);
			$fid = $fidtid[0];
			$tid = $fidtid[1];
			$thread = $this->thread->read(intval($fid), intval($tid));
			$this->check_thread_exists($thread);
			$typeid1 = $thread['typeid1'];
			$typeid2 = $thread['typeid2'];
			$typeid3 = $thread['typeid3'];
			$typeid4 = $thread['typeid4'];
			
			$this->init_view_thread($fidtidarr, 'type');
			$this->init_type_select($forum, $typeid1, $typeid2, $typeid3, $typeid4);
			
			$this->view->assign('fid', $fid);
			$this->view->assign('forum', $forum);
			
			// hook mod_type_before.php
			$this->view->display('mod_type_ajax.htm');
		} else {
			// 修改精华等级，分类。
			$typeid1 = intval(core::gpc('typeid1', 'P'));
			$typeid2 = intval(core::gpc('typeid2', 'P'));
			$typeid3 = intval(core::gpc('typeid3', 'P'));
			$typeid4 = intval(core::gpc('typeid4', 'P'));
			$systempm = intval(core::gpc('systempm', 'P'));
			$comment = core::gpc('comment', 'P');
			$this->check_comment($comment);
			
			// hook mod_type_after.php
			foreach($fidtidarr as &$v) {			// 此处也得用 &
				// 初始化数据
				$fid = intval($v[0]);
				$tid = intval($v[1]);
				
				// 过滤非本版块的主题分类
				if($fid != intval(core::gpc('fid'))) continue;
				
				$thread = $this->thread->read($fid, $tid);
				if(empty($thread)) continue;
				
				$this->thread_type_data->xupdate($fid, $tid, $typeid1, $typeid2, $typeid3, $typeid4);
				
				$thread['typeid1'] = $typeid1;
				$thread['typeid2'] = $typeid2;
				$thread['typeid3'] = $typeid3;
				$thread['typeid4'] = $typeid4;
				$this->thread->update($thread);
				
				// 记录到版主操作日志
				$this->modlog->create(array(
					'uid'=>$this->_user['uid'],
					'username'=>$this->_user['username'],
					'fid'=>$fid,
					'tid'=>$tid,
					'pid'=>0,
					'subject'=>$thread['subject'],
					'credits'=>0,
					'golds'=>0,
					'dateline'=>$_SERVER['time'],
					'action'=>'type',
					'comment'=>$comment,
				));
				
				$this->inc_modnum($fid, $tid);
				
				// 发送系统消息：
				if($systempm) {
					$pmsubject = utf8::substr($thread['subject'], 0, 32);
					$pmmessage = "您的主题<a href=\"?thread-index-fid-$fid-tid-$tid.htm\" target=\"_blank\">【{$pmsubject}】</a>被【{$this->_user['username']}】".($typeid1 > 0 ? "设置主题分类" : "取消主题分类")."。";
					$this->pm->system_send($thread['uid'], $thread['username'], $pmmessage);
				}
			}
			
			$this->message('操作成功！');
		}
	}
	
	// 所有 fid 相关表都需要更新，版块的统计数也需要更新。
	public function on_move() {
		$this->_title[] = '移动主题';
		$this->_nav[] = '移动主题';
		
		$this->check_login();
		
		$fid = intval(core::gpc('fid'));
		$fidtidarr = $this->get_fid_tids();
		
		$forum = $this->mcache->read('forum', $fid);

		$this->check_access($forum, 'move');
		
		$typeid1 = intval(core::gpc('typeid1', 'R'));
		$typeid2 = intval(core::gpc('typeid2', 'R'));
		$typeid3 = intval(core::gpc('typeid3', 'R'));
		$typeid4 = intval(core::gpc('typeid4', 'R'));
		
		$this->thread_type->check_typeid($typeid1, 1);
		$this->thread_type->check_typeid($typeid2, 2);
		$this->thread_type->check_typeid($typeid3, 3);
		$this->thread_type->check_typeid($typeid4, 4);
		
		if(!$this->form_submit()) {
			
			$forumoptions = $this->forum->get_public_options($fid, $this->_user);
			$this->view->assign('forumoptions', $forumoptions);
			
			$this->view->assign('forum', $forum);
			$this->view->assign('fid', $fid);
			
			// 初始化 select 控件
			$this->init_type_select($forum, $typeid1, $typeid2, $typeid3, $typeid4);
			
			// hook mod_move_before.php
			
			$this->view->display('mod_move_ajax.htm');
		} else {
			
			$systempm = intval(core::gpc('systempm', 'P'));
			$comment = core::gpc('comment', 'P');
			$this->check_comment($comment);
			
			// 目标论坛的发帖权限
			$fid2 = intval(core::gpc('fid2', 'P'));
			$forum2 = $this->forum->read($fid2);
			$this->check_forum_exists($forum2);
			$this->check_access($forum2, 'post');
			if($fid == $fid2) {
				$this->message('请选择其他版块。', 0);
			}
			
			$typeidsum = $typeid1 + $typeid2 + $typeid3 + $typeid4;	// 检查合法范围
			
			// hook mod_move_after.php
			
			foreach($fidtidarr as $v) {
				$fid = $v[0];
				$tid = $v[1];
				$thread = $this->thread->read($fid, $tid);
				if(empty($thread)) continue;
				if($thread['top'] > 0) {
					$this->message('您选择的主题中包含置顶主题，请先取消置顶再进行移动。', 0);
				}
			}
			
			// 查找主题。更新 fid
			$tidnum = $pidnum = $digestnum = 0;
			foreach($fidtidarr as $v) {
				$fid = $v[0];
				$tid = $v[1];
				$thread = $this->thread->read($fid, $tid);
				if(empty($thread)) continue;
				$tidnum++;	// 帖子数
				
				// ----------->更新相关数据的 fid start
				if($thread['digest'] > 0) $digestnum++;
				
				// 主题分类，从原来的主题分类中清除
				if($thread['typeid1'] > 0 ||$thread['typeid2'] > 0 ||$thread['typeid3'] > 0 ||$thread['typeid4'] > 0) {
					$this->thread_type_data->xdelete($fid, $tid);
				}
				
				// 加入到 thread_type
				$this->thread_type_data->xcreate($fid2, $tid, $typeid1, $typeid2, $typeid3, $typeid4);
				
				$thread['typeid1'] = $typeid1;
				$thread['typeid2'] = $typeid2;
				$thread['typeid3'] = $typeid3;
				$thread['typeid4'] = $typeid4;
				$this->thread->update($thread);
				
				$this->thread->index_update(array('fid'=>$fid, 'tid'=>$tid), array('fid'=>$fid2), TRUE);
				$this->post->index_update(array('fid'=>$fid, 'tid'=>$tid), array('fid'=>$fid2), TRUE);
				$this->attach->index_update(array('fid'=>$fid, 'tid'=>$tid), array('fid'=>$fid2), TRUE);
				$this->mypost->index_update(array('fid'=>$fid, 'tid'=>$tid), array('fid'=>$fid2), TRUE);
				$this->modlog->index_update(array('fid'=>$fid, 'tid'=>$tid), array('fid'=>$fid2), TRUE);
				$this->thread_digest->index_update(array('fid'=>$fid, 'tid'=>$tid), array('fid'=>$fid2), TRUE);
				$this->thread_new->index_update(array('fid'=>$fid, 'tid'=>$tid), array('fid'=>$fid2), TRUE);
				
				// ----------->更新相关数据的 fid end
				
				$pidnum += $thread['posts'];
				
				// 记录到版主操作日志
				$this->modlog->create(array(
					'uid'=>$this->_user['uid'],
					'username'=>$this->_user['username'],
					'fid'=>$fid2,
					'tid'=>$tid,
					'pid'=>0,
					'subject'=>$thread['subject'],
					'credits'=>0,
					'golds'=>0,
					'dateline'=>$_SERVER['time'],
					'action'=>'move',
					'comment'=>$comment,
				));
				
				$this->inc_modnum($fid2, $tid);
				
				// 发送系统消息：
				if($systempm) {
					$pmsubject = utf8::substr($thread['subject'], 0, 32);
					$pmmessage = "您的主题<a href=\"?thread-index-fid-$fid2-tid-$tid.htm\" target=\"_blank\">【{$pmsubject}】</a>被【{$this->_user['username']}】移动到了【{$forum2['name']}】。";
					$this->pm->system_send($thread['uid'], $thread['username'], $pmmessage);
				}
				
				// hook mod_move_loop_after.php
			}
			
			// 更新版块主题数，回复数
			$forum['threads'] -= $tidnum;
			$forum2['threads'] += $tidnum;
			$forum['posts'] -= $pidnum;
			$forum2['posts'] += $pidnum;
			$forum['digests'] -= $digestnum;
			$forum2['digests'] += $digestnum;
			
			$this->forum->xupdate($forum);
			$this->forum->xupdate($forum2);
			
			// hook mod_move_succeed.php
			$this->message("操作成功！", 1, '?forum-index-fid-$fid2.htm');
		}
	}
	
	public function on_rate() {
		$this->_title[] = '版主评分';
		$this->_nav[] = '版主评分';
		
		$fid = intval(core::gpc('fid'));
		$pid = intval(core::gpc('pid'));
		$uid = $this->_user['uid'];
		
		// 权限检测
		$forum = $this->forum->read($fid);
		if(!$this->is_mod($forum, $this->_user)) {
			$this->message('您没有权限管理该版块！');
		}
		
		$post = $this->post->read($fid, $pid);
		$this->check_post_exists($post);
		
		$thread = $this->thread->read($fid, $post['tid']);
		$this->check_thread_exists($thread);
		
		$tid = $thread['tid'];
		
		// 剩余积分
		$user = $this->user->read($uid);
		$group = $this->group->read($user['groupid']);
		list($credits, $golds) = $this->rate->get_today_credits_golds($uid);
		$remain_credits = $group['maxcredits'] - $credits;
		$remain_golds = $group['maxgolds'] - $golds;
		
		// 每日一个斑竹只能对一个帖子的评分只记录一条，后面的覆盖前面。
		$rate = $this->rate->get_today_rate_by_fid_pid_uid($fid, $pid, $uid);
		
		if(!$this->form_submit()) {
			
			$this->view->assign('remain_credits', $remain_credits);
			$this->view->assign('remain_golds', $remain_golds);
			$this->view->assign('fid', $fid);
			$this->view->assign('pid', $pid);
			$this->view->assign('rate', $rate);
			$this->view->display('mod_rate_ajax.htm');
		} else {
			
			// 取消评分
			$delete = core::gpc('delete', 'P');
			if($delete) {
				if(!empty($rate)) {
					
					// 更新用户积分！
					$user = $this->user->read($post['uid']);
					// 还原积分
					$user['credits'] -= $rate['credits'];
					$user['golds'] -= $rate['golds'];
					$this->user->update($user);
					
					$post['rates']--;
					$this->post->update($post);
					$this->rate->delete($rate['rateid']);	// 只能删除今日自己对该楼的，不用判断权限。
				
				
					// 发送系统消息：
					$pmsubject = utf8::substr($thread['subject'], 0, 32);
					$credits_html = $rate['credits'] > 0 ? '-'.$rate['credits'] : -$rate['credits'];
					$golds_html = $rate['golds'] > 0 ? '-'.$rate['golds'] : -$rate['golds'];
					$pmmessage = "您的帖子<a href=\"?thread-index-fid-$fid-tid-$tid-page-$post[page].htm\" target=\"_blank\">【{$pmsubject}】</a>被【{$this->_user['username']}】取消了评分，积分：{$credits_html}，金币{$golds_html}。";
					$this->pm->system_send($post['uid'], $post['username'], $pmmessage);
				}
				$this->message('取消评分完毕。');
			}
			
			$credits = intval(core::gpc('credits', 'P'));
			$golds = intval(core::gpc('golds', 'P'));
			$comment = core::gpc('comment', 'P');
			$this->check_comment($comment);
			
			// 判断积分是否足够
			if($credits > 0 && $credits > $remain_credits) {
				$this->message("本次评价积分不够！需要积分：$credits, 剩余积分：$remain_credits", 0);
			}
			if($golds > 0 && $golds > $remain_golds) {
				$this->message("本次评价金币不够！需要金币：$golds, 剩余金币：$remain_golds", 0);
			}
			
			/*
			if(empty($credits) && empty($golds)) {
				$this->message("请选择评价的积分或金币。", 0);
			}*/
			
			// 如果已经评价过，则返回差值，更新记录
			if(!empty($rate)) {
				// 更新用户积分！
				$user = $this->user->read($post['uid']);
				// 先还原积分
				$user['credits'] -= $rate['credits'];
				$user['golds'] -= $rate['golds'];
				
				// 再设置积分
				$user['credits'] += $credits;
				$user['golds'] += $golds;
				$this->user->update($user);
				
				// 积分差额自动返回（剩余金额是计算出来的，所以不存在返还）。
				$rate['credits'] = $credits;
				$rate['golds'] = $golds;
				$rate['comment'] = $comment;
				$this->rate->update($rate);
				
			} else {
				
				# 版主评分日志，针对每一楼，实际上也可以是任意用户评分
				$this->rate->create(array(
					'uid'=>$this->_user['uid'],
					'username'=>$this->_user['username'],
					'fid'=>$fid,
					'tid'=>$post['tid'],
					'pid'=>$pid,
					'credits'=>$credits,
					'golds'=>$golds,
					'dateline'=>$_SERVER['time'],
					'ymd'=>date('Ymd', $_SERVER['time']),
					'comment'=>$comment,
					'comment'=>$comment,
				));

				$post['rates']++;
				$this->post->update($post);
				
				// 更新用户积分！
				$user = $this->user->read($post['uid']);
				$user['credits'] += $credits;
				$user['golds'] += $golds;
				$this->user->update($user);
			}
			
			// 发送系统消息：
			$pmsubject = utf8::substr($thread['subject'], 0, 32);
			$credits_html = $credits > 0 ? '+'.$credits : $credits;
			$golds_html = $golds > 0 ? '+'.$golds : $golds;
			$pmmessage = "您的帖子<a href=\"?thread-index-fid-$fid-tid-$post[tid]-page-$post[page].htm\" target=\"_blank\">【{$pmsubject}】</a>被【{$this->_user['username']}】评分，积分：{$credits_html}，金币{$golds_html}。";
			$this->pm->system_send($post['uid'], $post['username'], $pmmessage);
			
			$this->message('操作成功！', 1);
		}
	}
	
	public function on_delete() {
		$this->_title[] = '删除主题';
		$this->_nav[] = '删除主题';
		
		$this->check_login();
		
		$fid = intval(core::gpc('fid'));
		$fidtidarr = $this->get_fid_tids();
		
		$forum = $this->forum->read($fid);
		
		$this->check_access($forum, 'delete');
		
		if(!$this->form_submit()) {
			
			$this->view->assign('fid', $fid);
			
			// hook mod_delete_before.php
			$this->view->display('mod_delete_ajax.htm');
		} else {
			
			$systempm = intval(core::gpc('systempm', 'P'));
			$comment = core::gpc('comment', 'P');
			$this->check_comment($comment);
			
			// hook mod_delete_after.php
			foreach($fidtidarr as $v) {
				$fid = intval($v[0]);
				$tid = intval($v[1]);
				
				// 记录到版主操作日志
				$thread = $this->thread->read($fid, $tid);
				$this->modlog->create(array(
					'uid'=>$this->_user['uid'],
					'username'=>$this->_user['username'],
					'fid'=>$fid,
					'tid'=>$tid,
					'pid'=>0,
					'subject'=>$thread['subject'],
					'credits'=>0,
					'golds'=>0,
					'dateline'=>$_SERVER['time'],
					'action'=>'delete',
					'comment'=>$comment,
				));
				
				// hook mod_delete_loop_after.php
				
				// 发送系统消息：
				if($systempm) {
					$pmsubject = utf8::substr($thread['subject'], 0, 32);
					$pmmessage = "您的帖子【{$pmsubject}】被【{$this->_user['username']}】删除。";
					$this->pm->system_send($thread['uid'], $thread['username'], $pmmessage);
				}
				
				$this->thread->xdelete($fid, $tid, TRUE);
			}
			
			// hook mod_delete_succeed.php
			$this->message('操作成功！');
		}
	}
	
	// copy from thread_control.class.php
	private function tidkeys_to_keys(&$keys, $tidkeys) {
		if($tidkeys) {
			$fidtidlist = explode(' ', trim($tidkeys));
			foreach($fidtidlist as $fidtid) {
				list($fid, $tid) = explode('-', $fidtid);
				$tid && $keys[] = "thread-fid-$fid-tid-$tid";
			}
		}
	}
	
	// 截取前几个字符串，分隔符为
	private function substr_by_sep($string, $sep, $n) {
		$arr = explode($sep, $string);
		$arr2 = array_slice($arr, 0, $n);
		return implode($sep, $arr2);
	}
	
	// 传递 tid
	private function get_fid_tids() {
		$fid_tids = core::gpc('fid_tids'); // 字符串: 123_100__123_101__123_102
		$r = array();
		$arr = explode('__', $fid_tids);
		foreach((array)$arr as $v) {
			$arr2 = explode('_', $v);
			//if(!isset($arr2[1])) continue;
			$r[] = array(intval($arr2[0]), intval($arr2[1]));
		}
		//$fidtidarr = misc::explode('_', '__', $fid_tids);
		$threads = count($r);
		$this->view->assign('fid_tids', $fid_tids);
		$this->view->assign('threads', $threads);
		return $r;
	}
	
	// 增加版主操作次数
	private function inc_modnum($fid, $tid) {
		$thread = $this->thread->read($fid, $tid);
		$thread['modnum']++;
		$this->thread->update($thread);
	}

	// 初始化控件的初始值。
	private function init_view_thread($fidtidarr, $action = '') {
		$thread = $modlog = array();
		foreach($fidtidarr as &$v) {
			$fid = $v[0];
			$tid = $v[1];
			$thread = $this->thread->read($fid, $tid);
			break;
		}
		$this->view->assign('thread', $thread);
		
		// modlog, 最后一次的操作
		if(!empty($thread)) {
			$modloglist = $this->modlog->get_list_by_fid_tid($thread['fid'], $thread['tid']);
			foreach($modloglist as &$modlog) {
				if($modlog['action'] == $action) {
					break;
				}
			}
			$this->view->assign('modlog', $modlog);
		}
	}
	
	private function check_comment(&$comment) {
		core::htmlspecialchars($comment);
		if(utf8::strlen($comment) > 64) {
			$this->message('评价不能超过64个字符！', 0);
		}
	}
	
	// copy from post_control.class.php
	private function init_type_select($forum, $typeid1 = 0, $typeid2 = 0, $typeid3 = 0, $typeid4 = 0) {
		$typearr1 = empty($forum['types'][1]) ? array() : array('0'=>'&gt;'.$forum['typecates'][1]) + (array)$forum['types'][1];
		$typearr2 = empty($forum['types'][2]) ? array() : array('0'=>'&gt;'.$forum['typecates'][2]) + (array)$forum['types'][2];
		$typearr3 = empty($forum['types'][3]) ? array() : array('0'=>'&gt;'.$forum['typecates'][3]) + (array)$forum['types'][3];
		$typearr4 = empty($forum['types'][4]) ? array() : array('0'=>'&gt;'.$forum['typecates'][4]) + (array)$forum['types'][4];
		$typeselect1 = $typearr1 && !empty($forum['typecates'][1]) ? form::get_select('typeid1', $typearr1, $typeid1, '') : '';
		$typeselect2 = $typearr2 && !empty($forum['typecates'][2]) ? form::get_select('typeid2', $typearr2, $typeid2, '') : '';
		$typeselect3 = $typearr3 && !empty($forum['typecates'][3]) ? form::get_select('typeid3', $typearr3, $typeid3, '') : '';
		$typeselect4 = $typearr4 && !empty($forum['typecates'][4]) ? form::get_select('typeid4', $typearr4, $typeid4, '') : '';
		$this->view->assign('typeselect1', $typeselect1);
		$this->view->assign('typeselect2', $typeselect2);
		$this->view->assign('typeselect3', $typeselect3);
		$this->view->assign('typeselect4', $typeselect4);
	}
	
	//hook mod_control_after.php
}

?>