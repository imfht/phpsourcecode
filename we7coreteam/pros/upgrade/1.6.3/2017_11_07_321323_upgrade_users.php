<?php
namespace We7\V163;

defined('IN_IA') or exit('Access Denied');
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * Time: 1506147730
 * @version 1.6.2
 */


class UpgradeUsers {

	/**
	 *  执行更新
	 */
	public function up() {
		if (!pdo_fieldexists('users', 'register_type')) {
			pdo_query('ALTER TABLE ' . tablename('users') . " ADD `register_type` TINYINT(3) NOT NULL DEFAULT 0 COMMENT '用户来源类型：0网站注册，1qq, 2微信';");
		}
		if (!pdo_fieldexists('users', 'openid')) {
			pdo_query('ALTER TABLE ' . tablename('users') . " ADD `openid` varchar(50) NOT NULL DEFAULT 0 COMMENT '第三方的openid';");
		}
	}
	
	/**
	 *  回滚更新
	 */
	public function down() {
		

	}
}
		