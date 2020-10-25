<?php

namespace We7\V166;

defined('IN_IA') or exit('Access Denied');
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * Time: 1515122343
 * @version 1.6.6
 */

class ModulesBindingsEntry {

	/**
	 *  执行更新
	 */
	public function up() {
		if (pdo_fieldexists('modules_bindings', 'entry')) {
			pdo_query("ALTER TABLE " . tablename('modules_bindings') . " CHANGE `entry` `entry` VARCHAR(30) NOT NULL DEFAULT '';");
		}
	}
	
	/**
	 *  回滚更新
	 */
	public function down() {
		

	}
}
		