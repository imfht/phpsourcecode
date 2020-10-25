<?php
namespace We7\V161;

defined('IN_IA') or exit('Access Denied');
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * Time: 1509009847
 * @version 1.6.1
 */


class AddEndtime {

	/**
	 *  执行更新
	 */
	public function up() {
		if (!pdo_fieldexists('account', 'endtime')) {
			pdo_query('ALTER TABLE ' . tablename('account') . " ADD `endtime` int(20) NOT NULL DEFAULT 0;");
		}
	}
	
	/**
	 *  回滚更新
	 */
	public function down() {
		

	}
}
		