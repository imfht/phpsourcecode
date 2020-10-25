<?php
namespace We7\V162;

defined('IN_IA') or exit('Access Denied');

class UpgradeModulesBindings {

	/**
	 *  执行更新
	 */
	public function up() {
		if (pdo_fieldexists('modules_bindings', 'do')) {
			pdo_query("ALTER TABLE " . tablename('modules_bindings') . " CHANGE `do` `do` VARCHAR(120) NOT NULL DEFAULT '';");
		}
	}
	
	/**
	 *  回滚更新
	 */
	public function down() {
		

	}
}
		