<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_member_login extends discuz_table
{

	public function __construct() {

		$this->_table = 'common_member_login';
		$this->_pk    = 'uid';

		parent::__construct();
	}

	public function checkExists($loginName, $uid) {
		$v = DB::result_first("SELECT uid FROM %t WHERE loginname=%s", array($this->_table, $loginName));
		if(!$v) {
			return false;
		}
		return $uid != $v;
	}

	public function getUid($loginName) {
		return DB::result_first("SELECT uid FROM %t WHERE loginname=%s", array($this->_table, $loginName));
	}

}

?>