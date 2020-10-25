<?php

namespace We7\V165;

defined('IN_IA') or exit('Access Denied');
/**
 * 修改pc 最多新建应用
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * Time: 1510121253
 * @version 1.6.5
 */

class AlterPcMaxaccount {

	/**
	 *  执行更新
	 */
	public function up() {
		if(!pdo_fieldexists('users_group', 'maxwebapp')) {
			pdo_query("ALTER TABLE " . tablename('users_group') . " ADD `maxwebapp` INT(10) NOT NULL DEFAULT 0 COMMENT 'PC最大创建数量';");
		}

		if(!pdo_fieldexists('users_founder_group', 'maxwebapp')) {
			pdo_query("ALTER TABLE " . tablename('users_founder_group') . " ADD `maxwebapp` INT(10) NOT NULL DEFAULT 0 COMMENT 'PC最大创建数量';");
		}
	}
	
	/**
	 *  回滚更新
	 */
	public function down() {
		

	}
}
		