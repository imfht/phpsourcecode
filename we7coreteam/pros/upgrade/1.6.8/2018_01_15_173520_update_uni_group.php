<?php

namespace We7\V168;

defined('IN_IA') or exit('Access Denied');
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * Time: 1516008920
 * @version 1.6.8
 */

class UpdateUniGroup {

	/**
	 *  执行更新
	 */
	public function up() {
		if (pdo_fieldexists('uni_group', 'modules')) {
			pdo_query("ALTER TABLE ". tablename('uni_group') ." CHANGE `modules` `modules` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ");
		}
	}

	/**
	 *  回滚更新
	 */
	public function down() {


	}
}
