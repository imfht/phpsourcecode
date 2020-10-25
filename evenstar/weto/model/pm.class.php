<?php

/*
 * Copyright (C) xiuno.com
 */

class pm extends base_model {
	
	function __construct(&$conf) {
		parent::__construct($conf);
		$this->table = 'pm';
		$this->primarykey = array('pmid');
		$this->maxcol = 'pmid';
		
		unset($this->conf);// 解除引用
		$this->conf = $conf;
		$this->old_conf = &$conf;
		$this->conf['cache']['enable'] = FALSE;	// 关闭 Memcached，短消息直接走MYSQL
		
		// hook pm_construct_end.php
	}

	public function system_send($touid, $tousername, $message) {
		return $this->send($this->conf['system_uid'], $touid, $this->conf['system_username'], $tousername, $message);
	}
	
	// 带有关联关系的创建，uid1 为创建者, $uid2 为接受者
	public function send($uid1, $uid2, $username1, $username2, $message) {
		if(empty($uid1) || empty($uid2)) {
			return FALSE;
		}
		$senduid = $uid1;
		$recvuid = $uid2;
		$recvuser = $this->user->read($recvuid);
		if(empty($recvuser)) {
			return FALSE;
		}
		
		// 交换变量，最小的在前。
		if($uid1 > $uid2) {
			$t = $uid1; $uid1 = $uid2; $uid2 = $t;
			$t = $username1; $username1 = $username2; $username2 = $t;
		}
		
		// pmcount.count++
		$pmcount = $this->pmcount->read($uid1, $uid2);
		if(empty($pmcount)) {
			$pmcount = array(
				'uid1'=>$uid1,
				'uid2'=>$uid2,
				'count'=>1,
				'dateline'=>$_SERVER['time'],
			);
			$page = 1;
			$this->pmcount->create($pmcount);	
		} else {
			$count = $pmcount ? $pmcount['count'] : 0;
			$pagesize = 20;
			$page = ceil(($count + 1) / $pagesize);
			$pmcount['count']++;
			$pmcount['dateline'] = $_SERVER['time'];
			$this->pmcount->update($pmcount);
		}
		
		// pm
		$pm = array(
			'uid1'=>$uid1,
			'uid2'=>$uid2,
			'uid'=>$senduid,
			'username1'=>$username1,
			'username2'=>$username2,
			'message'=>$message,
			'dateline'=>$_SERVER['time'],
			'page'=>$page
		);
		$pmid = $this->create($pm);
		$pm['pmid'] = $pmid;
		
		// pmnew.count++
		$pmnew = $this->pmnew->read($recvuid, $senduid);
		if(empty($pmnew)) {
			$pmnew = array(
				'recvuid'=>$recvuid,
				'senduid'=>$senduid,
				'count'=>1,
				'dateline'=>$_SERVER['time'],
			);
			$recvuser['newpms']++;
			$this->user->update($recvuser);
			$this->pmnew->create($pmnew);
		} else {
			// 如果为两人的某轮第一条短消息
			if($pmnew['count'] == 0) {
				$recvuser['newpms']++;
			} else {
				// 如果出了故障，这里修复意外。
				$recvuser['newpms'] == 0 && $recvuser['newpms'] = 1;
			}
			$pmnew['count']++;
			$pmnew['dateline'] = $_SERVER['time'];
			$this->user->update($recvuser);
			$this->pmnew->update($pmnew);
		}
		
		return $pm;
	}
	
	// 标记已经读过
	public function markread($senduid, $recvuid) {
		$pmnew = $this->pmnew->read($recvuid, $senduid);
		if($pmnew['count'] > 0) {
			// pmnew
			$pmnew['count'] = 0;
			$this->pmnew->update($pmnew);
			
			// pmcount 不变
		
			// user.newpms
			$user = $this->user->read($recvuid);
			$user['newpms']--;
			$this->user->update($user);
		}
		// pmnew.count = 0
		// pmcount.count = 0
		// user.newpms = 0
	}
	
	public function get_list_by_uid($uid1, $uid2, $page) {
		if($uid1 > $uid2) {
			$t = $uid1; $uid1 = $uid2; $uid2 = $t;
		}
		$pmlist = $this->index_fetch(array('uid1'=>$uid1, 'uid2'=>$uid2, 'page'=>$page), array(), 0, 100);
		foreach($pmlist as &$pm) {
			if(!empty($pm)) $this->format($pm);
		}
		misc::arrlist_multisort($pmlist, 'pmid', TRUE);
		return $pmlist;
	}
	
	public function format(&$pm) {
		$pm['dateline'] = misc::humandate($pm['dateline']);
	}

	// 关联删除，先 markread, 再删除。
	public function xdelete($pmid) {
		// 总数--
		$this->delete($pmid);
	}
	
	// 清空对话记录
	public function truncate_history($uid1, $uid2) {
		if($uid1 > $uid2) {
			$t = $uid1; $uid1 = $uid2; $uid2 = $t;
		}
		$this->index_delete(array('uid1'=>$uid1, 'uid2'=>$uid2));
		$pmcount = $this->pmcount->read($uid1, $uid2);
		$pmcount['count'] = 0;
		$this->pmcount->update($pmcount);
	}

}
?>