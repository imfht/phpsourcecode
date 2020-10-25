<?php

namespace We7\V165;

defined('IN_IA') or exit('Access Denied');
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * Time: 1510133613
 * @version 1.6.5
 */

class AlterModulePcSupport {

	/**
	 *  执行更新
	 */
	public function up() {
		if(!pdo_fieldexists('modules', 'webapp_support')) {
			pdo_query("ALTER TABLE " . tablename('modules') . " ADD `webapp_support` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '是否支持pc应用 1 不支持 2支持';");
		}
	}
	
	/**
	 *  回滚更新
	 */
	public function down() {
		

	}
}
		