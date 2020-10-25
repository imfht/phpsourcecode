<?php
namespace We7\V156;
defined('IN_IA') or exit('Access Denied');
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * Time: 1505183601
 * @version 1.5.6
 */


class CreateUpgradeUser {

	/**
	 *  执行更新
	 */
	public function up() {
		if (!pdo_fieldexists('users_profile', 'avatar')) {
			pdo_query("ALTER TABLE ". tablename('users_profile') ." CHANGE `avatar` `avatar` VARCHAR(254) NOT NULL DEFAULT '';");
		}
	}

	/**
	 *  回滚更新
	 */
	public function down() {


	}
}
