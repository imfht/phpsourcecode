<?php
namespace We7\V156;
defined('IN_IA') or exit('Access Denied');
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * Time: 1505123650
 * @version 1.5.6
 */


class CreateUpdateWxapp {

	/**
	 *  执行更新
	 */
	public function up() {
		if (!pdo_fieldexists('site_store_goods', 'module_group')) {
			pdo_query('ALTER TABLE ' . tablename('site_store_goods') . " ADD `module_group` int(10) NOT NULL DEFAULT 0;");
		}
		if (!pdo_fieldexists('uni_settings', 'statistics')) {
			pdo_query('ALTER TABLE ' . tablename('uni_settings') . " ADD `statistics` varchar(100) NOT NULL DEFAULT '';");
		}
		if (!pdo_fieldexists('site_store_goods', 'api_num')) {
			pdo_query('ALTER TABLE ' . tablename('site_store_goods') . " ADD `api_num` int(10) NOT NULL DEFAULT 0;");
		}
	}

	/**
	 *  回滚更新
	 */
	public function down() {


	}
}
