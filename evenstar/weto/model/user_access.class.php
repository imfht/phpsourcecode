<?php

/*
 * Copyright (C) xiuno.com
 */

class user_access extends base_model{
	
	function __construct(&$conf) {
		parent::__construct($conf);
		$this->table = 'user_access';
		$this->primarykey = array('uid');
		$this->maxcol = 'uid';
		$this->conf['cache']['enable'] = FALSE;
		
		// hook user_access_construct_end.php
	}
	
	// 初始化权限
	public function init($uid) {
		$access = array(
			'uid'=>$uid,
			'allowread'=>1,
			'allowthread'=>1,
			'allowpost'=>1,
			'allowreply'=>1,
			'allowattach'=>1,
			'allowdown'=>1,
			'expiry'=>0
		);
		$user = $this->user->read($uid);
		$user['accesson'] = 1;
		$this->user->update($user);
		$this->create($access);
	}
	
	// 重置 user 的状态
	public function reset($uid) {
		$user = $this->user->read($uid);
		$user['accesson'] = 0;
		$this->user->update($user);
		$this->delete($uid);
	}
	
	public function xupdate($access) {
		$uid = $access['uid'];
		// 如果都允许，则删除权限限定
		$user = $this->user->read($uid);
		// 如果全部允许，则设置 user.accesson 的标志，并清空 user_access。
		if($access['allowread'] && $access['allowthread'] && $access['allowpost'] && $access['allowattach']) {
			$user['accesson'] = 0;
			$this->user->update($user);
			return $this->delete($uid);
		} else {
			$user['accesson'] = 1;
			$this->user->update($user);
			return $this->update($access);
		}
	}
}
?>