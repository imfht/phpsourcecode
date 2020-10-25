<?php

namespace We7\V165;

defined('IN_IA') or exit('Access Denied');
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * Time: 1512029181
 * @version 1.6.5
 */

class ChangeOauthType {

	/**
	 *  执行更新
	 */
	public function up() {
		if (pdo_fieldexists('modules', 'oauth_type')) {
			pdo_query('ALTER TABLE `ims_modules` CHANGE `oauth_type` `oauth_type` TINYINT(1) NOT NULL DEFAULT 1');
		}
	}
	
	/**
	 *  回滚更新
	 */
	public function down() {
		

	}
}
		