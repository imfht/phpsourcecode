<?php
namespace We7\V158;

defined('IN_IA') or exit('Access Denied');

class UpgradeUpdateUser {

	/**
	 *  执行更新
	 */
	public function up() {
		if (pdo_fieldexists('users', 'type')) {
			pdo_query("UPDATE ".tablename('users')." SET `type` = 1 WHERE `type` = 0;");
		}
	}

	/**
	 *  回滚更新
	 */
	public function down() {


	}
}
