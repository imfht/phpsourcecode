<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * User: fanyk
 * Date: 2017/8/21
 * Time: 17:26.
 */
namespace We7\V10;

defined('IN_IA') or exit('Access Denied');
class UpdateVfounder2 {
	public $description = '把创始人在ims_users中的founder_groupid标识为1';

	public function up() {
		global $_W;
		$founder_ids = explode(',', $_W['config']['setting']['founder']);
		$uids = pdo_getall('users', array('uid' => $founder_ids), 'uid');
		if (!empty($uids)) {
			foreach ($uids as $user_id) {
				pdo_update('users', array('founder_groupid' => 1), array('uid' => $user_id['uid']));
			}
		}
	}
}
