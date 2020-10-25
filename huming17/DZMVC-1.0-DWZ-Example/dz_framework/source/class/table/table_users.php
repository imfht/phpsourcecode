<?php

if(!defined('IN_SYSTEM')) {
	exit('Access Denied');
}

class table_users extends core_table
{
	public function __construct() {

		$this->_table = 'users';
		$this->_pk    = 'user_id';
		$this->_pre_cache_key = 'users_';
	}

	public function max_uid() {
		return DB::result_first('SELECT MAX(uid) FROM %t', array($this->_table));
	}

	public function range_by_uid($from, $limit) {
		return DB::fetch_all('SELECT * FROM %t WHERE uid >= %d ORDER BY uid LIMIT %d', array($this->_table, $from, $limit), $this->_pk);
	}
}

?>