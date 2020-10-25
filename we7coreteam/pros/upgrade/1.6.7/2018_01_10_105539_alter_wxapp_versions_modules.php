<?php

namespace We7\V167;

defined('IN_IA') or exit('Access Denied');
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * Time: 1515552939
 * @version 1.6.7
 */

class AlterWxappVersionsModules {

	/**
	 *  执行更新
	 */
	public function up() {
		pdo_query("ALTER TABLE " . tablename('wxapp_versions') . " MODIFY COLUMN  `modules` TEXT;");
	}
	
	/**
	 *  回滚更新
	 */
	public function down() {
		

	}
}
		