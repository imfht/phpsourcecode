<?php

/*
 * Copyright (C) xiuno.com
 */

define('MAX_RECENT_USERS', 50);	// 最近联系的用户保留个数

class pmnew extends base_model{
	
	function __construct(&$conf) {
		parent::__construct($conf);
		$this->table = 'pmnew';
		$this->primarykey = array('recvuid', 'senduid');
		
		unset($this->conf);// 解除引用
		$this->conf = $conf;
		$this->old_conf = &$conf;
		$this->conf['cache']['enable'] = FALSE;	// 关闭 Memcached，短消息直接走MYSQL
		
		// hook pmnew_construct_end.php
	}

	// 获取最新的短消息
	public function get_list_by_uid($uid) {
		$arrlist = $this->index_fetch(array('recvuid'=>$uid, 'count'=>array('>'=>0)), array(), 0, 100);
		misc::arrlist_multisort($arrlist, 'dateline', FALSE);
		return $arrlist;
	}
	
	// 取5个最新的联系人
	public function get_new_userlist($uid) {
		$arrlist = $this->index_fetch(array('recvuid'=>$uid, 'count'=>array('>'=>0)), array(), 0, 5);
		misc::arrlist_multisort($arrlist, 'dateline', FALSE);
		
		$userlist = array();
		foreach($arrlist as $v) {
			$user = $this->user->read($v['senduid']);
			$this->user->format($user);
			$user2 = array(
				'uid'=>$user['uid'],
				'username'=>$user['username'],
				'avatar'=>$user['avatar'],
				'avatar_small'=>$user['avatar_small'],
				'newpms'=>$v['count'],
			);
			$userlist[$v['senduid']] = $user2;
		}
		return $userlist;
	}
	
	// 取最近联系人，默认40个
	public function get_recent_userlist($uid) {
		$arrlist = $this->index_fetch(array('recvuid'=>$uid), array(), 0, MAX_RECENT_USERS + 100);
		misc::arrlist_multisort($arrlist, 'dateline', FALSE);
		
		// 清理过多的最近联系人。
		if(count($arrlist) >= MAX_RECENT_USERS + 100) {
			$dellist = array_slice($arrlist, MAX_RECENT_USERS);
			foreach($dellist as $v) {
				$this->delete($v['recvuid'], $v['senduid']);
			}
			$arrlist = array_slice($arrlist, 0, MAX_RECENT_USERS);
		}
		
		$userlist = array();
		foreach($arrlist as $v) {
			$user = $this->user->read($v['senduid']);
			$this->user->format($user);
			$userlist[$v['senduid']] = $user;
		}
		return $userlist;
	}
	
}
?>