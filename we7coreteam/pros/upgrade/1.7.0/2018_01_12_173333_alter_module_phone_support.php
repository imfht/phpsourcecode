<?php

namespace We7\V170;

defined('IN_IA') or exit('Access Denied');

class AlterModulePhoneSupport {

	/**
	 *  执行更新
	 */
	public function up() {
		if(!pdo_fieldexists('modules', 'phoneapp_support')) {
			pdo_query("ALTER TABLE " . tablename('modules') . " ADD `phoneapp_support` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '是否支持手机应用 1 不支持 2支持';");
		}
	}
	
	/**
	 *  回滚更新
	 */
	public function down() {
		

	}
}
		