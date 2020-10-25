<?php

/*
 * Copyright (C) xiuno.com
 */

!defined('FRAMEWORK_PATH') && exit('FRAMEWORK_PATH not defined.');

include BBS_PATH.'admin/control/admin_control.class.php';

class user_control extends admin_control {
	
	function __construct(&$conf) {
		parent::__construct($conf);
		$this->check_admin_group();
	}
	
	public function on_index() {
		$this->view->display('user.htm');
	}
	
	public function on_list() {
		$this->_title[] = '用户列表';
		$this->_nav[] = '<a href="./">用户列表</a>';
		
		$keyword = urldecode(core::gpc('keyword', 'R'));
		$keyword_url = urlencode($keyword);
		$cond = array();
		if($keyword) {
			if(is_numeric($keyword)) {
				$cond = array('uid'=>intval($keyword));
			} elseif(strpos($keyword, '@') !== FALSE) {
				$cond = array('email'=>$keyword);
			} else {
				$cond = array('username'=>$keyword);
			}
		}
		
		$page = misc::page();
		$start = ($page - 1) * $this->conf['pagesize'];
		$limit = $this->conf['pagesize'];
		$users = $cond ? 1 : $this->user->count();
		$userlist = $this->user->get_list($cond, $start, $limit, $users);
		foreach($userlist as &$user) {
			$this->user->format($user);
			$user['groupname'] = $this->group->groupid_to_name($user['groupid']);
		}
		
		$pages = misc::pages("?user-list.htm", $users, $page, $this->conf['pagesize']);
		
		$this->view->assign('page', $page);
		$this->view->assign('$keyword', $$keyword);
		$this->view->assign('pages', $pages);
		$this->view->assign('userlist', $userlist);
		
		// hook admin_user_list_view_before.php
		
		$this->view->display('user_list.htm');
	}
	
	// 批量添加
	public function on_create() {
		$this->_title[] = '用户注册';
		$this->_nav[] = '用户注册';
		
		$this->conf += $this->kv->xget('conf_ext');
		
		$user = $error = array();
		if($this->form_submit()) {
			
			// 接受数据
			$user['email'] = core::gpc('email', 'P');
			$user['username'] = core::gpc('username', 'P');
			$user['password'] = core::gpc('password', 'P');
			$password2 = core::gpc('password2', 'P');
			$user['regdate'] = $_SERVER['time'];
			
			// check 数据格式
			$error['email'] = $this->user->check_email($user['email']);
			$error['email_exists'] = $this->user->check_email_exists($user['email']);
			$error['username'] = $this->user->check_username($user['username']);
			$error['password'] = $this->user->check_password($user['password']);
			$error['password2'] = $this->user->check_password2($user['password'], $password2);
			
			// 判断结果
			if(!array_filter($error)) {
				$error = array();
				$salt = rand(100000, 999999);
				$user['salt'] = $salt;
				$user['password'] = $this->user->md5_md5($user['password'], $salt);
				$this->user->xcreate($user);
				$this->runtime->xset('todayusers', '+1');
				// $this->runtime->xsave();
			}
		}
		
		$this->view->assign('user', $user);
		$this->view->assign('error', $error);
		
		// hook admin_user_create_view_before.php
		
		$this->view->display('user_create.htm');
	}
	
	// 修改
	public function on_update() {
		$this->_title[] = '修改用户资料';
		$this->_nav[] = '修改用户资料';
		
		$uid = intval(core::gpc('uid'));
		$user = $this->user->read($uid);
		$this->check_user_exists($user);
		
		$input = $error = array();
		if($this->form_submit()) {
			$post = array('uid'=>$uid);
			
			$post['email'] = htmlspecialchars(core::gpc('email', 'P'));
			$post['groupid'] = intval(core::gpc('groupid', 'P'));
			$post['threads'] = intval(core::gpc('threads', 'P'));
			$post['posts'] = intval(core::gpc('posts', 'P'));
			$post['credits'] = intval(core::gpc('credits', 'P'));
			$post['golds'] = intval(core::gpc('golds', 'P'));
			$post['password'] = core::gpc('password', 'P');
			
			// check 数据格式
			//$error['email'] = $this->user->check_email($post['email']);
			if(!empty($post['password'])) {
				$error['password'] = $this->user->check_password($post['password']);
				$post['password'] = $this->user->md5_md5($post['password'], $user['salt']);
			} else {
				$post['password'] = $user['password'];
			}
			if(!array_filter($error)) {
				$error = array();
				$user = array_merge($user, $post);
				$this->user->update($user);
			}
		}
		
		//$input['username'] = form::get_text('username', $user['username'], 300);
		//$input['email'] = form::get_text('email', $user['email'], 300);
		$grouplist = $this->group->get_list();
		$grouparr = misc::arrlist_key_values($grouplist, 'groupid', 'name');
		$input['groupid'] = form::get_select('groupid', $grouparr, $user['groupid']);
		$input['threads'] = form::get_text('threads', $user['threads'], 100);
		$input['posts'] = form::get_text('posts', $user['posts'], 100);
		$input['credits'] = form::get_text('credits', $user['credits'], 100);
		$input['golds'] = form::get_text('golds', $user['golds'], 100);
		
		$this->view->assign('input', $input);
		$this->view->assign('user', $user);
		$this->view->assign('error', $error);
		
		// hook admin_user_update_view_before.php
		
		$this->view->display('user_update.htm');
	}
	
	// 读取
	public function on_read() {
		$this->_title[] = '查看用户资料';
		$this->_nav[] = '查看用户资料';
		
		$uid = intval(core::gpc('uid'));
		empty($uid) && $uid = $this->_user['uid'];
		$user = $this->user->read($uid);
		$this->check_user_exists($user);
		$this->user->format($user);
		$this->view->assign('user', $user);
		
		// hook admin_user_read_view_before.php
		
		$this->view->display('user_read.htm');
	}
	
	//hook admin_user_control_after.php
	
}

?>