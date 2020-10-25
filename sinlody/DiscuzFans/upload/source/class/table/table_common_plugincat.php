<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_plugincat extends discuz_table
{
	public function __construct() {

		$this->_table = 'common_plugincat';
		$this->_pk    = 'catid';

		parent::__construct();
	}

	public function fetch_by_catid($catid) {
		return DB::fetch_first('SELECT * FROM %t WHERE catid=%d', array($this->_table, $catid));
	}

	public function fetch_by_catcode($catcode) {
		return DB::fetch_first('SELECT * FROM %t WHERE catcode=%s', array($this->_table, $catcode));
	}

	public function fetch_all_cat() {
		return DB::fetch_all('SELECT * FROM %t WHERE status = 1 ORDER BY displayorder ASC', array($this->_table), 'catid');
	}

	public function fetch_all(){
		return DB::fetch_all('SELECT * FROM %t ORDER BY displayorder ASC', array($this->_table), 'catid');
	}
}

?>