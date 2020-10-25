<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: table_common_member_count.php 31022 2012-07-10 03:16:07Z chenmengshu $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_member_openid extends discuz_table
{
	public function __construct() {

		$this->_table = 'common_member_openid';
		$this->_pk    = 'uid';

		parent::__construct();
	}

	public function fetch_first_by_uid_type($uid, $type){
	    return DB::fetch_first('SELECT * FROM %t WHERE uid=%d AND `type`=%s', array($this->_table, $uid, $type));
	}

	public function fetch_first_by_openid_type($openid, $type){
	    return DB::fetch_first('SELECT * FROM %t WHERE `type`=%s AND `openid`=%s', array($this->_table, $type, $openid));
	}

	public function update_by_uid_type($uid, $type, $data){
	    return DB::update($this->_table, $data, "uid = ".intval($uid)." AND `type`='".daddslashes($type)."'");
	}
}

?>