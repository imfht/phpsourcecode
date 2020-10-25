<?php

namespace We7\V170;

defined('IN_IA') or exit('Access Denied');
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * Time: 1517299361
 * @version 1.7.0
 */

class AlterAccountWxapp {

	/**
	 *  执行更新
	 */
	public function up() {
		if (!pdo_fieldexists('account_wxapp', 'auth_refresh_token')) {
			$tableName = tablename('account_wxapp');
			$sql = <<<EOT
		ALTER TABLE $tableName ADD COLUMN `auth_refresh_token` VARCHAR(255) DEFAULT '';
EOT;
			pdo_run($sql);

		}
	}
	
	/**
	 *  回滚更新
	 */
	public function down() {
		

	}
}
		