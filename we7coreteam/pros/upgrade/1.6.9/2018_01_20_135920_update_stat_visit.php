<?php

namespace We7\V169;

defined('IN_IA') or exit('Access Denied');
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * Time: 1516427960
 * @version 1.6.9
 */

class UpdateStatVisit {

	/**
	 *  执行更新
	 */
	public function up() {
		if (!pdo_fieldexists('stat_visit', 'type')) {
			pdo_query("ALTER TABLE " . tablename('stat_visit') . " ADD `type` varchar(10) NOT NULL DEFAULT 'app';");
		}

	}

	/**
	 *  回滚更新
	 */
	public function down() {


	}
}
