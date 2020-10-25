<?php

/*
 * Copyright (C) xiuno.com
 */

!defined('FRAMEWORK_PATH') && exit('FRAMEWORK_PATH not defined.');

include BBS_PATH.'control/common_control.class.php';

class follow_control extends common_control {
	
	function __construct(&$conf) {
		parent::__construct($conf);
		$this->check_login();
		
		// 检查IP 屏蔽
		$this->check_ip();
	}
	
	// 添加关注
	public function on_create() {
		$uid = intval(core::gpc('uid'));
		$myuid = $this->_user['uid'];
		
		// hook follow_create_before.php
		$this->follow->xcreate($myuid, $uid);
		// hook follow_create_after.php
		
		// 查看是否已经关注
		$this->message('成功。', 1);
		
	}
	
	// 取消关注
	public function on_delete() {
		$uid = intval(core::gpc('uid'));
		$myuid = $this->_user['uid'];
		
		// hook follow_delete_before.php
		$this->follow->xdelete($myuid, $uid);
		// hook follow_delete_after.php
		
		$this->message('成功。', 1);
	}

	//hook follow_control_after.php
}

?>