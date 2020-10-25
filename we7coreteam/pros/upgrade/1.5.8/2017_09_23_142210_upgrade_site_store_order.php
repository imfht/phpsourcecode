<?php
namespace We7\V158;
defined('IN_IA') or exit('Access Denied');
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * Time: 1506147730
 * @version 1.5.8
 */


class UpgradeSiteStoreOrder {

	/**
	 *  执行更新
	 */
	public function up() {
		if (!pdo_fieldexists('site_store_order', 'wxapp')) {
			pdo_query('ALTER TABLE ' . tablename('site_store_order') . " ADD `wxapp` INT(15) NOT NULL DEFAULT 0 ;");
		}
	}
	
	/**
	 *  回滚更新
	 */
	public function down() {
		

	}
}
		