<?php

namespace We7\V163;

defined('IN_IA') or exit('Access Denied');
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * Time: 1510385348
 * @version 1.6.3
 */

class UpdateUniSettings {

	/**
	 *  执行更新
	 */
	public function up() {
		if (!pdo_fieldexists('uni_settings', 'bind_domain')) {
			pdo_query("ALTER TABLE " . tablename('uni_settings') . " ADD `bind_domain` varchar(200) NOT NULL DEFAULT '';");
		}
	}
	
	/**
	 *  回滚更新
	 */
	public function down() {
		

	}
}
		