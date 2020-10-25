<?php
namespace We7\V157;
defined('IN_IA') or exit('Access Denied');
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * Time: 1505212727
 * @version 1.5.7
 */
class UpdateAccountWxappType {
	/**
	 *  执行更新
	 */
	public function up() {
		$types = pdo_fetchall("SELECT a.acid FROM " . tablename('account_wxapp') . " AS aw JOIN " . tablename('account') . " AS a ON a.acid = aw.acid AND a.uniacid = aw.uniacid WHERE a.type = 1");
		if (!empty($types)) {
			$up_acids = array();
			foreach ($types as $acid) {
				$up_acids[] = $acid['acid'];
			}
			pdo_update('account', array('type' => 4), array('acid' => $up_acids));
		}
	}

	/**
	 *  回滚更新
	 */
	public function down() {


	}
}