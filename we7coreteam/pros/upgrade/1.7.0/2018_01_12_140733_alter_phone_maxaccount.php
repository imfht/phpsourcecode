<?php

namespace We7\V170;

defined('IN_IA') or exit('Access Denied');

class AlterPhoneMaxaccount {

	/**
	 *  执行更新
	 */
	public function up() {
		if(!pdo_fieldexists('users_group', 'maxphoneapp')) {
			pdo_query("ALTER TABLE " . tablename('users_group') . " ADD `maxphoneapp` INT(10) NOT NULL DEFAULT 0 COMMENT 'APP最大创建数量';");
		}

		if(!pdo_fieldexists('users_founder_group', 'maxphoneapp')) {
			pdo_query("ALTER TABLE " . tablename('users_founder_group') . " ADD `maxphoneapp` INT(10) NOT NULL DEFAULT 0 COMMENT 'APP最大创建数量';");
		}
	}
	
	/**
	 *  回滚更新
	 */
	public function down() {
		

	}
}
		