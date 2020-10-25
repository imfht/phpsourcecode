<?php

namespace We7\V164;

defined('IN_IA') or exit('Access Denied');
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * Time: 1511574210
 * @version 1.6.4
 */

class AlterWxappVersions {

	/**
	 *  执行更新
	 */
	public function up() {
		if (!pdo_fieldexists('wxapp_versions', 'appjson')) {
			pdo_query('ALTER TABLE ' . tablename('wxapp_versions') . " ADD `appjson` text NOT NULL DEFAULT '' COMMENT '用户自定义appjson';");
		}

		if (!pdo_fieldexists('wxapp_versions', 'default_appjson')) {
			pdo_query('ALTER TABLE ' . tablename('wxapp_versions') . " ADD `default_appjson` text NOT NULL DEFAULT '' COMMENT 'cloud_appjson 默认appjson';");
		}

		if (!pdo_fieldexists('wxapp_versions', 'use_default')) {
			pdo_query('ALTER TABLE ' . tablename('wxapp_versions') . " ADD `use_default` tinyint(1) NOT NULL DEFAULT 1 COMMENT '使用默认app.json';");
		}
	}
	
	/**
	 *  回滚更新
	 */
	public function down() {
		

	}
}
		