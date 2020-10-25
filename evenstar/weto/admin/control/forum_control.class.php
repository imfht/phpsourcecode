<?php

/*
 * Copyright (C) xiuno.com
 */

!defined('FRAMEWORK_PATH') && exit('FRAMEWORK_PATH not defined.');

include BBS_PATH.'admin/control/admin_control.class.php';

class forum_control extends admin_control {
	
	function __construct(&$conf) {
		parent::__construct($conf);
		$this->_checked['bbs'] = ' class="checked"';
		$this->check_admin_group();
		
		// 加载精华积分策略
		$this->conf += $this->kv->xget('conf_ext');
	}
	
	// 列表
	public function on_index() {
		$this->on_list();
	}	
	
	public function on_list() {
		$this->_title[] = '版块列表';
		$this->_nav[] = '<a href="./">版块列表</a>';
		
		$error = array();
		if($this->form_submit()) {
			
			// 修改
			$namearr = core::gpc('name', 'P');
			$rankarr = core::gpc('rank', 'P');
			
			// hook admin_forum_list_gpc_after.php
			
			if(!empty($namearr)) {
				foreach($namearr as $fid=>$name) {
					$fid = intval($fid);
					$forum = $this->forum->read($fid);
					$forum['rank'] = intval($rankarr[$fid]);
					$forum['name'] = $namearr[$fid];
					$this->forum->update($forum);
					$this->mcache->clear('forum', $fid);
				}
			}
			
			// 新增
			$newnamearr = core::gpc('newname', 'P');
			$newrankarr = core::gpc('newrank', 'P');
			if(!empty($newnamearr)) {
				
				foreach($newnamearr as $fid=>$name) {
					$fid = intval($fid);
					!isset($newrankarr[$fid]) && $newrankarr[$fid] = 0;
					$forum = array(
						'name'=>$name,
						'rank'=>intval($newrankarr[$fid]),
						'threads'=>0,
						'posts'=>0,
						'digests'=>0,
						'todayposts'=>0,
						'lasttid'=>0,
						'brief'=>'',
						'accesson'=>0,
						'modids'=>'',
						'modnames'=>'',
						'toptids'=>'',
						'orderby'=>0,
						'seo_title'=>'',
						'seo_keywords'=>'',
					);
					
					// hook admin_forum_create_before.htm
					
					$forum = $this->forum->create($forum);
				}
			}
			
		}
		
		$page = misc::page();
		$forums = $this->forum->count();
		$forumlist = $this->forum->get_list();
		foreach($forumlist as &$forum) {
			$this->forum->format($forum);
		}
		
		// 更新缓存
		$this->runtime->xupdate('forumarr');
		
		// hook admin_forum_list_view_before.php
		
		$this->view->assign('error', $error);
		$this->view->assign('forumlist', $forumlist);
		$this->view->display('forum_list.htm');
	}

	// 合并版块，保留fid1， 删除fid2，
	public function on_merge() {
		$this->_title[] = '合并版块';
		$this->_nav[] = '合并版块';
		
		$fid1 = intval(core::gpc('fid1', 'R')); // 保留
		$fid2 = intval(core::gpc('fid2', 'R')); // 删除

		$forumoptions = $this->forum->get_options($this->_user['uid'], $this->_user['groupid'], $fid1, $defaultfid);
		$this->view->assign('forumoptions', $forumoptions);
		
		$input = $error = array();
		if($fid1 && $fid2) {
			
			$forum1 = $this->forum->read($fid1);
			$forum2 = $this->forum->read($fid2);
			$this->check_forum_exists($forum1);
			$this->check_forum_exists($forum2);
			
			// 修改fid 所有涉及到 fid 的表！
			$this->thread->index_update(array('fid'=>$fid2), array('fid'=>$fid1, 'top'=>0, 'typeid1'=>0, 'typeid2'=>0, 'typeid3'=>0, 'typeid4'=>0, 'modnum'=>0), TRUE);
			$this->post->index_update(array('fid'=>$fid2), array('fid'=>$fid1), TRUE);
			$this->attach->index_update(array('fid'=>$fid2), array('fid'=>$fid1), TRUE);
			$this->mypost->index_update(array('fid'=>$fid2), array('fid'=>$fid1), TRUE);
			$this->thread_digest->index_update(array('fid'=>$fid2), array('fid'=>$fid1), TRUE);
			$this->thread_new->index_update(array('fid'=>$fid2), array('fid'=>$fid1), TRUE);
			
			// 删除原来版块的数据
			$this->forum_access->delete_by_fid($fid2);
			$this->thread_type->delete_by_fid($fid2);
			$this->thread_type_cate->delete_by_fid($fid2);
			$this->thread_type_data->delete_by_fid($fid2);
			$this->thread_type_count->delete_by_fid($fid2);
			$this->modlog->delete_by_fid($fid2);
			$this->thread_top->delete_top_3_by_fid($fid2);
			
			// 更新统计数
			$forum1['posts'] += $forum2['posts'];
			$forum1['threads'] += $forum2['threads'];
			$forum1['digests'] += $forum2['digests'];
			$this->forum->update($forum1);
			
			// 更新缓存
			$this->mcache->clear('forum', $fid1);
			$this->mcache->clear('forum', $fid2);
			
			$this->forum->delete($fid2);
			
			$this->runtime->xupdate('forumarr');
			$this->runtime->delete('typearr');
			
			// hook admin_forum_merge_succeed.php
				
			$this->message('合并完毕！', 1, '?forum-merge.htm');
		}
		
		$this->view->assign('error', $error);
		$this->view->display('forum_merge.htm');
	}
	
	// 修改
	public function on_update() {
		$this->_title[] = '修改版块';
		$this->_nav[] = '修改版块';
		
		$fid = intval(core::gpc('fid'));

		$forum = $this->forum->read($fid);
		$this->check_forum_exists($forum);
		
		// 检测 rank 重复
		
		$input = $error = array();
		if($this->form_submit()) {
			
			// 处理主题分类
			$this->process_threadtype($fid);
			
			// 准备更新数据
			$post = array();
			
			$post['name'] = core::gpc('name', 'P');
			$post['rank'] = intval(core::gpc('rank', 'P'));
			$post['orderby'] = intval(core::gpc('orderby', 'P'));
			//$post['threads'] = intval(core::gpc('threads', 'P'));
			//$post['posts'] = intval(core::gpc('posts', 'P'));
			//$post['todayposts'] = intval(core::gpc('todayposts', 'P'));
			$post['brief'] = core::gpc('brief', 'P');
			$post['modnames'] = trim(core::gpc('modnames', 'P'));
			$post['seo_title'] = trim(core::gpc('seo_title', 'P'));
			$post['seo_keywords'] = trim(core::gpc('seo_keywords', 'P'));
			
			// 权限
			$post['accesson'] = intval(core::gpc('accesson', 'P'));
			$groupids = core::gpc('groupids', 'P');
			$allowreads = (array)core::gpc('allowread', 'P');// 是数组
			$allowposts = (array)core::gpc('allowpost', 'P');
			$allowthreads = (array)core::gpc('allowthread', 'P');
			$allowattachs = (array)core::gpc('allowattach', 'P');
			$allowdowns = (array)core::gpc('allowdown', 'P');
			
			// 版主
			$modids = $modnames = '';
			$post['modnames'] = str_replace(array('　', "\t", '  '), ' ', $post['modnames']);
			$modnamearr = explode(' ', $post['modnames']);
			$modnamearr = array_unique($modnamearr);
			$modnamearr = array_slice($modnamearr, 0, 15);	// 最多6个
			foreach($modnamearr as $modname) {
				$_user = $this->user->get_user_by_username($modname);
				if($_user) {
					$_user && $modids .= ' '.$_user['uid'];
					$modnames .= ' '.$_user['username'];
					// 调整用户组
					$groupid = 4;
					$user = $this->user->read($_user['uid']);
					$user['groupid'] = $groupid > $user['groupid'] ? $user['groupid'] : $groupid;// 提升版主权限
					$this->user->update($user);
				}
			}
			$post['modids'] = trim($modids);
			$post['modnames'] = trim($modnames);
			
			if($post['accesson']) {
				foreach($groupids as $groupid) {
					$groupid = intval($groupid);
					!isset($allowreads[$groupid]) && $allowreads[$groupid] = 0;
					!isset($allowposts[$groupid]) && $allowposts[$groupid] = 0;
					!isset($allowthreads[$groupid]) && $allowthreads[$groupid] = 0;
					!isset($allowattachs[$groupid]) && $allowattachs[$groupid] = 0;
					!isset($allowdowns[$groupid]) && $allowdowns[$groupid] = 0;
					$access = $this->forum_access->read($fid, $groupid);
					$access['allowread'] = intval($allowreads[$groupid]);
					$access['allowpost'] = intval($allowposts[$groupid]);
					$access['allowthread'] = intval($allowthreads[$groupid]);
					$access['allowdown'] = intval($allowdowns[$groupid]);
					$access['allowattach'] = intval($allowattachs[$groupid]);
					$access['allowdown'] = intval($allowdowns[$groupid]);
					$access['fid'] = $fid;
					$access['groupid'] = intval($groupid);
					$this->forum_access->set(array($fid, $groupid), $access);
				}
			} else {
				// 清除权限
				$this->forum_access->delete_by_fid($fid);
			}
			
			$error['name'] = $this->forum->check_name($post['name']);

			if(!array_filter($error)) {
				$error = array();

				$forum = array_merge($forum, $post);
				
				// hook admin_forum_update_after.php
				
				$this->forum->update($forum);
			}
			
			$forum = $this->forum->read($fid);
			
			// ------------> 初始化 thread_type start
			$this->forum->format_thread_type_full($forum);
			
			if(!$forum['typelist'] && core::gpc('typeon', 'P')) {
				$this->thread_type_cate->enable($fid, 1);
				$this->thread_type_cate->enable($fid, 2);
				$this->thread_type_cate->enable($fid, 3);
				$this->thread_type_cate->enable($fid, 4);
				
			} elseif($forum['typelist'] && !core::gpc('typeon', 'P')) {
				// 关闭即可
				$this->thread_type_cate->disable($fid, 1);
				$this->thread_type_cate->disable($fid, 2);
				$this->thread_type_cate->disable($fid, 3);
				$this->thread_type_cate->disable($fid, 4);
			}
			// ------------> 初始化 thread_type end
		
			// 清除缓存
			$this->mcache->clear('forum', $fid);
			$this->runtime->xupdate('forumarr');
			$this->runtime->delete('typearr');
		}
		
		// 版块权限
		$grouplist = $this->group->get_list();
		$accesslist = $this->forum_access->get_list_by_fid($fid);
		if(empty($accesslist)) {
			$groupidarr = array_keys($grouplist);
			foreach($groupidarr as $groupid) {
				$accesslist[$groupid] = array();
				$access = &$accesslist[$groupid];
				if($groupid == 0) {
					$access['allowread'] = 1;
					$access['allowpost'] = 0;
					$access['allowthread'] = 0;
					$access['allowattach'] = 0;	// 游客不允许上传附件！写死了！
					$access['allowdown'] = 1;
				} else {
					$access['allowread'] = 1;
					$access['allowpost'] = 1;
					$access['allowthread'] = 1;
					$access['allowattach'] = 1;
					$access['allowdown'] = 1;
				}
			}
		}
		
		$forumoptions = $this->forum->get_options($this->_user['uid'], $this->_user['groupid'], $fid, $defaultfid);
		$this->view->assign('forumoptions', $forumoptions);
		
		$orderbyarr = array(0=>'顶帖时间排序', 1=>'发帖时间排序');
		
		$input = array();
		$input['orderby'] = form::get_radio('orderby', $orderbyarr, $forum['orderby']);
		$this->forum->format($forum);
		$this->forum->format_thread_type_full($forum);
		
		$admin_auth = core::gpc($this->conf['cookie_pre'].'admin_auth', 'C');
		$this->view->assign('input', $input);
		$this->view->assign('admin_auth', $admin_auth);
		$this->view->assign('grouplist', $grouplist);
		$this->view->assign('accesslist', $accesslist);
		$this->view->assign('fid', $forum['fid']);
		$this->view->assign('forum', $forum);
		$this->view->assign('error', $error);
		
		// hook admin_forum_update_before.php
		
		$this->view->display('forum_update.htm');
	}
	
	public function on_delete() {
		$this->_title[] = '删除版块';
		$this->_nav[] = '删除版块';
		
		$fid = intval(core::gpc('fid'));
		$starttid = intval(core::gpc('starttid'));//tid
		$threads = intval(core::gpc('threads'));//fid
		
		$limit = 200;

		$forum = $this->forum->read($fid);
		if(empty($forum)) {
			$this->message('版块已经被删除。', 1, '?forum-list.htm');
		}
		
		if(empty($threads)) {
			$threads = $forum['threads'];
		}
		if($starttid >= $threads) {
			// fid++ tid=0 跳转
			// hook admin_forum_delete_complete.php
			
			// 统一删除数据量较小的表：
			$this->forum_access->delete_by_fid($fid);
			$this->thread_type->delete_by_fid($fid);
			$this->thread_type_cate->delete_by_fid($fid);
			$this->thread_type_data->delete_by_fid($fid);
			$this->thread_type_count->delete_by_fid($fid);
			$this->modlog->delete_by_fid($fid);
			
			$this->forum->delete($fid);
			
			$this->runtime->xupdate('forumarr');
			$this->runtime->delete('typearr');
			$this->mcache->clear('forum', $fid);
			$this->message("删除 fid: $fid 完毕 ...", 1, "?forum-delete-fid-$fid.htm");
		} else {
			// 分批删除主题。
			$tidkeys = $this->thread->index_fetch_id(array('fid'=>$fid), array(), 0, $limit);
			$return = array();
			foreach($tidkeys as $key) {
				list($table, $_, $_, $_, $tid) = explode('-', $key);
				$tid = intval($tid);
				// hook admin_forum_delete_tid_before.php
				$return2 = $this->thread->xdelete($fid, $tid, FALSE);
				$this->thread->xdelete_merge_return($return, $return2);
			}
			$this->thread->xdelete_update($return);
			$starttid += $limit;
			$this->message("删除 fid: $fid, tid: $starttid ...", 1, "?forum-delete-fid-$fid-threads-$threads-starttid-$starttid.htm");
		}
		
		// hook admin_forum_delete_after.php
		
		// 删除首页的缓存
		$this->message('删除完毕', 1, '?forum-list.htm');
	}
	
	private function process_threadtype($fid) {
		$typecateenables = (array)core::gpc('typecateenable', 'P');
		$typecatemods = (array)core::gpc('typecatemod', 'P');
		$typecateranks = (array)core::gpc('typecaterank', 'P');
		$typecatenames = (array)core::gpc('typecatename', 'P');
		$typenames = (array)core::gpc('typename', 'P');
		$typeranks = (array)core::gpc('typerank', 'P');
		$typeenables = (array)core::gpc('typeenable', 'P');
		
		// 主题分类的大分类
		foreach($typecateranks as $typecateid=>$_) {
			$typecateid = intval($typecateid);
			$enable = isset($typecateenables[$typecateid]) ? intval($typecateenables[$typecateid]) : 0;
			$typecatemod = isset($typecatemods[$typecateid]) ? intval($typecatemods[$typecateid]) : 0;
			$rank = intval($typecateranks[$typecateid]);
			$name = $typecatenames[$typecateid];
			$typecate = $this->thread_type_cate->xread($fid, $typecateid);
			$enable = $enable && $typecatemod ? 3 : $enable; // 如果不开启，也无法设置版主权限
			if($name) {
				if(empty($typecate)) {
					$typecate['fid'] = $fid;
					$typecate['cateid'] = $typecateid;
					$typecate['enable'] = $enable;
					$typecate['rank'] = $rank;
					$typecate['catename'] = $name;
					$this->thread_type_cate->create($typecate);
				} else {
					$typecate['enable'] = $enable;
					$typecate['rank'] = $rank;
					$typecate['catename'] = $name;
					$this->thread_type_cate->update($typecate);
				}
			} else {
				$typecate && $this->thread_type_cate->delete($fid, $typecateid);
			}
		}
		
		foreach($typenames as $typeid=>$typename) {
			$typeid = intval($typeid);
			$type = $this->thread_type->read($fid, $typeid);
			if($typename) {
				$rank = isset($typeranks[$typeid]) ? intval($typeranks[$typeid]) : 0;
				$enable = isset($typeenables[$typeid]) ? intval($typeenables[$typeid]) : 0;
				if(empty($type)) {
					$type = array(
						'fid'=>$fid,
						'typeid'=>$typeid,
						'typename'=>$typename,
						'rank'=>$rank,
						'enable'=>$enable,
					);
					$this->thread_type->create($type);
				} else {
					$type['typename'] = $typename;
					$type['rank'] = $rank;
					$type['enable'] = $enable;
					$this->thread_type->update($type);
				}
			} else {
				$type && $this->thread_type->delete($fid, $typeid);
			}
		}
	}
	
	//hook admin_forum_control_after.php
	
}
?>