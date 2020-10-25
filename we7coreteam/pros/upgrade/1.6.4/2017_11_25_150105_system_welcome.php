<?php

namespace We7\V164;

defined('IN_IA') or exit('Access Denied');
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * Time: 1511593265
 * @version 1.6.4
 */

class SystemWelcome {

	/**
	 *  执行更新
	 */
	public function up() {
		if (!pdo_fieldexists('modules', 'welcome_support')) {
			pdo_query('ALTER TABLE ' . tablename('modules') . " ADD `welcome_support` int(2) NOT NULL DEFAULT 1 COMMENT '支持系统首页';");
		}
		if (!pdo_fieldexists('modules', 'oauth_type')) {
			pdo_query('ALTER TABLE ' . tablename('modules') . " ADD `oauth_type` varchar(10) NOT NULL DEFAULT 'base' COMMENT '获取用户信息方式；base:静默获取，userinfo：用户授权获取';");
		}
	}
	
	/**
	 *  回滚更新
	 */
	public function down() {
		

	}
}
		