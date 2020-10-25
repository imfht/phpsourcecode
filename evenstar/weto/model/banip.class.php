<?php

/*
 * Copyright (C) xiuno.com
 */

class banip extends base_model {
	
	function __construct(&$conf) {
		parent::__construct($conf);
		$this->table = 'banip';
		$this->primarykey = array('banid');
		$this->maxcol = 'banid';
		
		// hook banip_construct_end.php
	}
	
	public function is_banip($ip) {
		return $this->get_banip($ip);
	}
	
	public function get_list() {
		$baniplist = $this->index_fetch(array(), array(), 0, 1000);
		foreach($baniplist as &$banip) {
			$banip['dateline_fmt'] = date('Y-n-j', $banip['dateline']);
			$banip['expiry_fmt'] = date('Y-n-j', $banip['expiry']);
			$banip['ip0'] = $this->intvalip($banip['ip0']);
			$banip['ip1'] = $this->intvalip($banip['ip1']);
			$banip['ip2'] = $this->intvalip($banip['ip2']);
			$banip['ip3'] = $this->intvalip($banip['ip3']);
			$user = $this->user->read($banip['uid']);
			$banip['username'] = $user['username'];
		}
		return $baniplist;
	}
	
	// 获取对应的BAN记录，可能是某个IP段
	public function get_banip($ip) {
		$ip = explode('.', $ip);
		foreach($ip as &$v) {
			$v = intval($v);
		}
		if($this->count() < 50) {
			$arrlist = $this->index_fetch(array(), array(), 0, 50);
			foreach($arrlist as $arr) {
				if($arr['ip0'] == $ip[0] && ($arr['ip1'] == -1 || $arr['ip1'] == $ip[1] &&  ($arr['ip2'] == -1 || $arr['ip2'] == $ip[2] &&  ($arr['ip3'] == -1 || $arr['ip3'] == $ip[3])))) {
					return $arr;
				}
			}
		// 查4次
		} else {
			$arrlist = $this->index_fetch(array('ip0'=>$ip[0], 'ip1'=>-1), array(), 0, 1);
			empty($arrlist) && $this->index_fetch(array('ip0'=>$ip[0], 'ip1'=>$ip[1], 'ip2'=>-1), array(), 0, 1);
			empty($arrlist) && $this->index_fetch(array('ip0'=>$ip[0], 'ip1'=>$ip[1], 'ip2'=>$ip[2], 'ip3'=>-1), array(), 0, 1);
			empty($arrlist) && $this->index_fetch(array('ip0'=>$ip[0], 'ip1'=>$ip[1], 'ip2'=>$ip[2], 'ip3'=>$ip[3]), array(), 0, 1);
			if($arrlist) return array_pop($arrlist);
		}
		return array();
	}
	
	public function add_banip($ip, $uid, $expiry) {
		$arr = explode('.', $ip);
		$banip = $this->get_banip($ip);
		if($banip) {
			$banip['ip0'] = $arr[0];
			$banip['ip1'] = $arr[1];
			$banip['ip2'] = $arr[2];
			$banip['ip3'] = $arr[3];
			$banip['uid'] = $uid;
			$banip['expiry'] = $expiry;
			return $this->update($banip);
		}
		foreach($arr as &$v) {
			$v == '*' && $v = -1;
			$v = intval($v);
		}
		$banid = $this->create(array('ip0'=>$arr[0], 'ip1'=>$arr[1], 'ip2'=>$arr[2], 'ip3'=>$arr[3], 'uid'=>$uid, 'dateline'=>$_SERVER['time'], 'expiry'=>$expiry));
		return $banid;
	}
	
	private function intvalip($v) {
		if($v == -1) return '*';
		return intval($v);
	}
}
?>