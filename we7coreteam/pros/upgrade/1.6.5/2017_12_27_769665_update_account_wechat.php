<?php

namespace We7\V165;

defined('IN_IA') or exit('Access Denied');
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * Time: 1510292752
 * @version 1.6.5
 */

class UpdateAccountWechat {

	/**
	 *  执行更新
	 */
	public function up() {
		pdo_update('account_wechats', array('key' => ''), array('key' => 'wx570bc396a51b8ff8'));
	}
	
	/**
	 *  回滚更新
	 */
	public function down() {
		

	}
}
		