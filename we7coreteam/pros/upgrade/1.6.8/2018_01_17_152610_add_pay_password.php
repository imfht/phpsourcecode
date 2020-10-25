<?php

namespace We7\V168;

defined('IN_IA') or exit('Access Denied');
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * Time: 1516173970
 * @version 1.6.8
 */

class AddPayPassword {

	/**
	 *  执行更新
	 */
	public function up() {
		if (!pdo_fieldexists('mc_members', 'pay_password')) {
			pdo_query('ALTER TABLE ' . tablename('mc_members') . " ADD `pay_password` VARCHAR(30) NOT NULL DEFAULT '' COMMENT '支付密码' ;");
		}
	}
	
	/**
	 *  回滚更新
	 */
	public function down() {
		

	}
}
		