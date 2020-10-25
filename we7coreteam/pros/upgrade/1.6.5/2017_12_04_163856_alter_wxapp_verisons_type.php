<?php

namespace We7\V165;

defined('IN_IA') or exit('Access Denied');
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * Time: 1512376736
 * @version 1.6.5
 */

class AlterWxappVerisonsType {

	/**
	 *  执行更新
	 */
	public function up() {
		if (!pdo_fieldexists('wxapp_versions', 'type')) {
			$table = tablename('wxapp_versions');
			pdo_query("ALTER TABLE $table ADD `type` int(2) NOT NULL DEFAULT 0 COMMENT '0 默认小程序 1 公众号应用'");
		}

		if (!pdo_fieldexists('wxapp_versions', 'entry_id')) {
			$table = tablename('wxapp_versions');
			pdo_query("ALTER TABLE $table ADD `entry_id` int(11) NOT NULL DEFAULT 0 COMMENT '普通应用小程序入口ID'");
		}
	}
	
	/**
	 *  回滚更新
	 */
	public function down() {
		

	}
}
		