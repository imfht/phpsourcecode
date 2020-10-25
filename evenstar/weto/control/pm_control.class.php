<?php

/*
 * Copyright (C) xiuno.com
 */

!defined('FRAMEWORK_PATH') && exit('FRAMEWORK_PATH not defined.');

include BBS_PATH.'control/common_control.class.php';

class pm_control extends common_control {
	
	function __construct(&$conf) {
		parent::__construct($conf);
		
	}
	
	// 获取新短消息，如果有，则返回
	public function on_new() {
		
		$this->check_login();
		
		$uid = $this->_user['uid'];
		$user = $this->user->read($uid);
		
		// 可能用户被删除，退出登录，用户组变更？
		if(empty($user)) {
			misc::setcookie($this->conf['cookie_pre'].'auth', '', 0, $this->conf['cookie_path'], $this->conf['cookie_domain']);
			$this->message('用户不存在，或者已经被删除。', 0);
			exit;
		}
		
		// 用户名被修改，需要重新登录。
		if($user['username'] != $this->_user['username'] || $user['accesson'] != $this->_user['accesson']) {
			$this->user->set_login_cookie($user);
		}
		
		// 密码被修改，需要重新登录。
		if(substr($user['password'], 0, 8) != $this->_user['password']) {
			misc::setcookie($this->conf['cookie_pre'].'auth', '', 0, $this->conf['cookie_path'], $this->conf['cookie_domain']);
			$this->message('密码被修改，请重新登录。', 0);
		}
		
		// 五分钟更新一次
		if($_SERVER['time'] - $user['lastactive'] > 300) {
			$user['lastactive'] = $_SERVER['time'];
			$user['onlinetime'] += 300;
			$this->user->update($user);
			
			// 更新在线，如果不存在，则不更新(common_control.class.php 每隔5分钟会自动创建一次 online)
			$this->update_online();
		}
		
		// 如果用户组发生改变，此处更新。
		$this->user->update_group($user, $this->_user['groupid']);
		
		// 如果是管理员，密码修改后要退出！
		
		
		// 获取最新消息
		if($user['newpms'] > 0) {
			$userlist = $this->pmnew->get_new_userlist($uid);
			
			// hook pm_new_before.php
			
			$this->message($userlist, 1);
		} else {
			
			// hook pm_new_no_before.php
			$this->message('没有短消息', 2);
		}
		
	}
	
	// 列表
	public function on_ajaxlist() {
		$this->check_login();
		
		$uid1 = intval(core::gpc('uid'));
		$uid2 = $this->_user['uid'];
		
		$touser = $this->user->read($uid1);
		
		$this->check_user_exists($touser);
		$this->user->format($touser);
		
		// hook pm_list_before.php
		$pmcount = $this->pmcount->read($uid1, $uid2);
		$count = $pmcount['count'];
		$pagesize = 20;
		$totalpage = max(1, ceil($count / $pagesize));
		$page = $totalpage;
		$pages = misc::pages("?pm-ajaxlist-uid-$uid1.htm", $count, $page, $pagesize);
		
		//$pmlist = $this->pm->get_list_by_uid($uid1, $uid2, $page);
		
		$this->view->assign('touser', $touser);
		$this->view->assign('page', $page);
		$this->view->assign('totalpage', $totalpage);
		$this->view->assign('pages', $pages);
		//$this->view->assign('pmlist', $pmlist);
		
		// hook pm_list_after.php
		$this->view->display('pm_list_ajax.htm');
	}
	
	// 返回 maxpmid, maxpage
	public function on_ajaxlistbody() {
		$this->check_login();
		
		$uid1 = intval(core::gpc('uid'));
		$uid2 = $this->_user['uid'];
		
		// hook pm_body_before.php
		
		$page = misc::page();
		$pmcount = $this->pmcount->read($uid1, $uid2);
		$count = $pmcount['count'];
		$pagesize = 20;
		$totalpage = max(1, ceil($count / $pagesize));
		
		// 默认读取最后一页，newpm.count 标记为0, markread
		if(!core::gpc('page')) {
			$page = $totalpage;
			$this->pm->markread($uid1, $uid2);	// senduid, recvuid, 会清空 recvuid 的 newpms
		}
		
		$maxpmid = $totalpage = 0;
		$pmlist = $this->pm->get_list_by_uid($uid1, $uid2, $page);
		foreach($pmlist as $v) {
			$maxpmid = max($maxpmid, $v['pmid']);
		}
		
		// hook pm_body_after.php
		$this->view->json = array('maxpmid'=>$maxpmid, 'maxpage'=>$totalpage);
		$this->view->assign('pmlist', $pmlist);
		$this->view->display('pm_list_body_ajax.htm');
	}
	
	// 发送短消息
	public function on_create() {
		$this->check_login();
		
		// 检查IP 屏蔽
		$this->check_ip();
		
		$touid = intval(core::gpc('touid'));
		$message = htmlspecialchars(core::gpc('message', 'P'));
		$message = misc::html_space($message);
		$message = utf8::safe_substr($message, 0, 254);		// 安全截取
		
		$touser = $this->user->read($touid);
		$this->check_user_exists($touser);
		
		$uid1 = $this->_user['uid'];
		$username1 = $this->_user['username'];
		$uid2 = $touser['uid'];
		$username2 = $touser['username'];
		
		// hook pm_create_before.php
		
		if($touid == $uid1) {
			$this->message('不能对自己发送短消息', 0);
		}
		
		$pm = $this->pm->send($uid1, $uid2, $username1, $username2, $message);
		$this->pm->format($pm);
		$pmlist = array($pm);
		
		$this->view->json = array('maxpmid'=>$pm['pmid'], 'maxpage'=>$pm['page']);
		$this->view->assign('pmlist', $pmlist);
		// hook pm_create_after.php
		$this->view->display('pm_list_body_ajax.htm');
	}
	
	public function on_ajaxdelete() {
		$this->check_login();
		
		// 检查IP 屏蔽
		$this->check_ip();
		
		$pmid = intval(core::gpc('pmid'));
		$pm = $this->pm->read($pmid);
		if($pm['uid'] != $this->_user['uid'] && !($pm['uid'] == 2 && ($pm['uid1'] == $this->_user['uid'] || $pm['uid2'] == $this->_user['uid']))) {
			$this->message('您不能删除别人的留言！', 0);
		}
		
		// hook pm_delete_before.php
		$this->pm->xdelete($pmid);
		// hook pm_delete_after.php
		
		$this->message('删除成功', 1);
	}
	
	// 获取最新的短消息，查询最后两页，返回最新的pmlist
	// 返回 maxpmid, maxpage
	public function on_newlist() {
		$this->check_login();
		
		// maxpmid, maxpage
		$maxpmid = intval(core::gpc('maxpmid'));
		$maxpage = intval(core::gpc('page'));
		$fromuid = intval(core::gpc('fromuid'));		// 谁对我的uid
		
		$uid1 = $fromuid;
		$uid2 = $this->_user['uid'];
		
		// 看是否有最新消息，取最后一页，并且大于 maxpmid 的记录
		$pmcount = $this->pmcount->read($uid1, $uid2);
		$count = $pmcount['count'];
		$pagesize = 20;
		$totalpage = max(1, ceil($count / $pagesize));
		
		// hook pm_newlist_before.php
		
		// 取最新短消息列表
		$maxpage = $totalpage - $maxpage > 3 ? $totalpage - 3 : $maxpage;	// 最多取三页
		$pmlist = array();
		for($i = $maxpage; $i <= $totalpage; $i++) {
			$pmlist = array_merge($pmlist, $this->pm->get_list_by_uid($uid1, $uid2, $i));
		}
		
		// 取maxpmid maxpage
		$newmaxpmid = 0;
		foreach($pmlist as $k=>$v) {
			if($v['pmid'] <= $maxpmid) {
				unset($pmlist[$k]);
			} else {
				$newmaxpmid = max($newmaxpmid, $v['pmid']);
			}
		}
		
		if($pmlist) {
			
			// 标记已读，不在提示新消息
			$this->pm->markread($fromuid, $this->_user['uid']);
			
			// 标记两人之间的新消息为 0
			
			$this->view->json = array('maxpmid'=>$newmaxpmid, 'maxpage'=>$totalpage);
			$this->view->assign('pmlist', $pmlist);
			// hook pm_newlist_after.php
			$this->view->display('pm_list_body_ajax.htm');
		} else {
			$this->message('', 0);
		}
		
	}
	
	public function on_truncate() {
		$touid = intval(core::gpc('touid'));
		$this->pm->truncate_history($this->_user['uid'], $touid);
		$this->message('清除成功');
	}
	
	//hook pm_control_after.php
	
}

?>