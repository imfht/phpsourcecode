<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: table_common_smscodecheck.php 33625 2013-07-19 06:03:49Z pmonkey_w $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_smscodecheck extends discuz_table
{
	private $_uids = array();
	public function __construct() {

		$this->_table = 'common_smscodecheck';
		$this->_pk    = 'id';

		parent::__construct();
	}

	public function delete_expiration($id = 0) {
		if($id) {
			$id = dintval($id);
			DB::delete($this->_table, "id='$id'");
		}
		DB::delete($this->_table, TIMESTAMP."-dateline>600");
		DB::delete($this->_table, "verified>4");
		DB::delete($this->_table, "succeed>1");
	}

	public function get_code_by_sms($sms) {
		return DB::fetch_first("SELECT * FROM %t WHERE succeed=0 AND sms=%s ORDER BY dateline DESC", array($this->_table, $sms));
	}

	public function get_code_by_code_sms($sms,$code) {
		return DB::fetch_first("SELECT * FROM %t WHERE sms=%s AND code=%s ORDER BY dateline DESC", array($this->_table, $sms, $code));
	}

	public function update_verified($id) {
		DB::query("UPDATE %t SET verified=verified+1 WHERE id=%d", array($this->_table, $id));
	}

	public function update_succeed($id) {
		DB::query("UPDATE %t SET verified=verified+1,succeed=succeed+1 WHERE id=%d", array($this->_table, $id));
	}

	public function truncate() {
		DB::query("TRUNCATE %t", array($this->_table));
	}

}

?>